package scratch.projectanalyzer;

import java.io.File;
import java.io.FilenameFilter;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;

/**
 * The DatabaseBuilder serves a few purposes:
 * 	- it takes the files/directories passed in as command line arguments and creates a 
 * 	  queue with all the scratch projects in these files/directories to be analyzed
 *  - it then picks files off the queue and calls methods to parse and generate the 
 *    relevant information from these projects that we would like to capture in the database
 *  - finally, it takes that information and inserts the records directly into the database
 *    tables
 *    
 * It was built to be versatile with the ability to analyze single files, batches of files, 
 * as well as the entire collection of Scratch projects on the server.  However, it also has a 
 * lot of special cases such that it runs well in two situations -- bulk analysis of all previously
 * uploaded Scratch projects and single file analysis for projects being uploaded to the website
 */
public class DatabaseBuilder {

	LinkedList<File> sbFilesQueue; // queue of all sb project files to be analyzed and recorded in the database
	boolean allFilesQueued;  // true if all projects have been queued (no more files will be added to the queue)
	int evaluatedFiles = 0;  // number of files that have been evaluated
	
	Connection conn;  // connection the database containing the tables to insert records into
	String commandLineArgs;  // command line arguments passed to the application (used for logging purposes)
	
	
	/**
	 * The FileSystemReader traces down the directory trees to build the <code>sbFilesQueue</code>
	 * with all the Scratch projects to be analyzed
	 */
	class FileSystemReader extends Thread {
		ArrayList<String> dirs;
		Date before = null;
		Date after = null;  
		
		/**
		 * 
		 * @param dirs  Arraylist of directories/files that contain projects to be queued
		 * @param before  Only queue projects modified before this date
		 * @param after  Only queue projects modified after this date
		 */
		public FileSystemReader(ArrayList<String> dirs, Date before, Date after) {
			this.dirs = dirs;
			this.before = before;
			this.after = after;
		}

		public void run() {			
			for (String dir : dirs) {				
				File file = new File(dir);
				if (file.isDirectory()) {
					File[] files = file.listFiles();
					for (File single : files) {
						addSbFilesToList(single, files);
					}
				} else {
					addSbFilesToList(file, null);
				}
			}
			
			allFilesQueued = true;
		}
		
		private boolean satisfiesCriteria(File file, File[] directoryFiles) {
			if (!FileUtils.isFileOfType(file, "sb"))
				return false;
			
			if (before != null && file.lastModified() > before.getTime()) {
				return false;
			}
				
			if (after != null && file.lastModified() < after.getTime()) {  
				return false;
			}
				
			if (directoryFiles == null) {
				return true;  // skips check for if the project is in the database if the analyzer was called for this project directly (ie: not part of a bulk analysis)
			}
			return !isProjectInDb(file, directoryFiles); // do not queue files that have previously been analyzed
		}
		
		private void addSbFilesToList(File file, File[] directoryFiles) {
			while (sbFilesQueue.size() > 500) {  // keep queue at about 500 files
				try {
					sleep(100);
				} catch (InterruptedException e) {
				}
			}
			
			if (!file.isDirectory()) {
				if (satisfiesCriteria(file, directoryFiles)) {
					synchronized(sbFilesQueue) {
						sbFilesQueue.add(file);
					}
				}
					
				return;
			}
			
			File[] files = file.listFiles();
			ArrayList<File> currentProjects = new ArrayList<File>();
			for (File single : files) {				
				if (single.isDirectory()) {
					//addSbFilesToList(single);
					continue;  // only go 2 levels deep  (special case for Scratch website file structure)
				} else {	
					if (!satisfiesCriteria(single, files))
						continue;
					
					if (FileUtils.getVersion(single) == 0) { // current project - analyze these after all other projects in order to determine version number
						currentProjects.add(single);
					} else {
						synchronized(sbFilesQueue) {
							sbFilesQueue.add(single);
						}
					}
				}			
			}
			
			// analyze current projects last to make sure we provide the project with the correct version number
			// queried from the database (there is also an option to determine version using the file system, enabled through command line arg)
			for (File single : currentProjects) {				
				synchronized(sbFilesQueue) {
					sbFilesQueue.add(single);
				}
			}
		}
	}
	
	public DatabaseBuilder(ArrayList<String> files, Connection conn, Date before, Date after, String clArgs) throws SQLException {
		this.conn = conn;
		commandLineArgs = clArgs;
		sbFilesQueue = new LinkedList<File>();
		allFilesQueued = false;
		
		// Create Database Tables
		createTables();
		
		// Build queue of projects
		Thread fileReader = new FileSystemReader(files, before, after);
		fileReader.start();			
	}
	
	/**
	 * Creates database tables if they don't already exist in the database
	 * @throws SQLException
	 */
	private void createTables() throws SQLException {
		Statement stat = conn.createStatement();
		
		for (String table : DatabaseConstants.getInstance().allTables()) {
			stat.addBatch(DatabaseConstants.getInstance().getCreateTableQueries(table));
		}
		
		stat.executeBatch();
	}

	/**
	 * Parses projects from the queue and inserts the records into the database
	 */
	public void buildDb() {
		long starttime = System.currentTimeMillis();
		
		List<String> allImages = new ArrayList<String>();
		for (String[] costumeCategory : ScratchConstants.COSTUMES) {
			allImages.addAll(Arrays.asList(costumeCategory));
		}
		for (String[] backgroundCategory : ScratchConstants.BACKGROUNDS) {
			allImages.addAll(Arrays.asList(backgroundCategory));
		}
		List<String> allSounds = new ArrayList<String>();
		for (String[] soundCategory : ScratchConstants.SOUNDS) {
			allSounds.addAll(Arrays.asList(soundCategory));
		}
		ProjectParser parser = new ProjectParser(allImages, allSounds);

		DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:s");
		
		// Evaluate all .sb files and insert data into database
		fullLoop: while (!allFilesQueued || !sbFilesQueue.isEmpty()) {
			while (sbFilesQueue.isEmpty()) {  // wait for queue to fill
            	if (allFilesQueued)
            		break fullLoop;
	            try {
	            	Thread.sleep(500);
	            } catch (InterruptedException ignored) {
	            }
	        }
			
			File sbFile;
			synchronized (sbFilesQueue) {
				sbFile = sbFilesQueue.removeFirst();
			}
			evaluatedFiles++;
			if (Loader.ENABLE_FILENAME_PRINTING) {
				System.out.println(evaluatedFiles + ": " + sbFile.getAbsolutePath());
			}
			
			String id = FileUtils.getFileNameWithoutExtension(sbFile);
			int version = FileUtils.getVersion(sbFile); 
			if (version == 0) { // current version -- query database or look in file system for actual version number
				if (Loader.ENABLE_FILESYSTEM_VERSION_CHECKING) {
					version = getLatestVersionInFileSystem(new File(sbFile.getParent()), id);
				} else {
					version = getLatestVersionInDb(conn, id);
				}
				version++;
			}
			
			
			// log file start before actual analysis to catch bad files
			HashMap<String,Object> logMap = new HashMap<String,Object>(2);
			logMap.put("time_started",df.format(new Date(System.currentTimeMillis())));
			logMap.put("arguments", commandLineArgs);			
			insertIntoTable(conn, DatabaseConstants.LOGTABLE, id, version, logMap); 
			
			try {
				parser.evaluateFile(sbFile);  // parse relevant information in project
			} catch (Exception e) {
				if (Loader.ENABLE_LOGGING) {
					System.out.println("Exception with File: " + sbFile);
					e.printStackTrace(System.out);
					if (Loader.ENABLE_LOGGING && evaluatedFiles % 1000 == 0) {
						System.out.println(evaluatedFiles + " files evaluated in " + (System.currentTimeMillis()-starttime)+"ms");
					}	
				}
				continue;
			}
			
			insertIntoTable(conn, DatabaseConstants.BLOCKSTABLE, id, version, parser.getBlocksUsage());
			insertIntoTable(conn, DatabaseConstants.INFOTABLE, id, version, parser.getInfo());
			insertListIntoTable(conn, DatabaseConstants.SAVEHISTORYTABLE, id, version, parser.getSaveHistory());
			insertListIntoTable(conn, DatabaseConstants.SHAREHISTORYTABLE, id, version, parser.getShareHistory());
			insertListIntoTable(conn, DatabaseConstants.MEDIATABLE, id, version, parser.getMediaUsage());
			insertListIntoTable(conn, DatabaseConstants.MIDIINSTRUMENTSTABLE, id, version, parser.getInstrumentsUsage());
			insertListIntoTable(conn, DatabaseConstants.DRUMSTABLE, id, version, parser.getDrumsUsage());
			insertListIntoTable(conn, DatabaseConstants.SPRITESTACKSTABLE, id, version, parser.getSpriteStack());
			insertListIntoTable(conn, DatabaseConstants.SPRITESTABLE, id, version, parser.getSpritesInfo());
			insertListIntoTable(conn, DatabaseConstants.STRINGSTABLE, id, version, parser.getUserStrings());
			insertListIntoTable(conn, DatabaseConstants.DISCONNECTEDBLOCKS, id, version, parser.getDisconnectedBlocks());
			
			if (Loader.ENABLE_LOGGING && evaluatedFiles % 1000 == 0) {
				System.out.println(evaluatedFiles + " files evaluated in " + (System.currentTimeMillis()-starttime)+"ms");
			}
		}

		if (Loader.ENABLE_LOGGING)
			System.out.println("Total time ("+evaluatedFiles+"): "+(System.currentTimeMillis()-starttime)+"ms");
	}
	
	/**
	 * Inserts the information in <code>map</map> into the table <code>tableName</code>
	 * @param conn
	 * @param tableName
	 * @param projectId
	 * @param projectVersion
	 * @param map
	 * @return
	 */
	private boolean insertIntoTable(Connection conn, String tableName, String projectId, int projectVersion, Map<String,Object> map) {
			String colNames = "`project_id`,`project_version`";
			String colValues = "'"+projectId+"','"+projectVersion+"'";
			String otherBlocks = "";
			
			List<String> tableCols = Arrays.asList(DatabaseConstants.getInstance().getTableFields(tableName));
			
			for (String key : map.keySet()) {
				//TODO shouldn't hard code these safety checks here
				String value = map.get(key).toString().replace("'", "''");
				value = value.replace("\\", "\\\\");
				key = key.replace('-','_');
				if (tableCols.contains(key)) {
					colNames += ",`"+key+"`";
					colValues += ",'"+value+"'";
				} else {
					if (!otherBlocks.equals("")) 
						otherBlocks += ",";
					otherBlocks += key+"="+value;
				}
			}
			
			String sql = "INSERT INTO "+tableName+" ("+colNames;
			if (tableCols.contains("other")) {
				sql += ", `other`) VALUES ("+colValues+", '"+otherBlocks+"');";
			} else {
				sql += ") VALUES ("+colValues+");";
			}
		try {
			Statement stat = conn.createStatement();
			stat.execute(sql);
			stat.close();
			return true;
		} catch (SQLException e) {
			if (Loader.ENABLE_LOGGING) {
				System.out.println(sql);
				e.printStackTrace(System.out);
			}
			return false;
		}	
	}

	/**
	 * Inserts multiple records specified by <code>list</code> into tableName
	 * @param conn
	 * @param tableName
	 * @param projectId
	 * @param projectVersion
	 * @param list
	 * @return
	 */
	private boolean insertListIntoTable(Connection conn, String tableName, String projectId, int projectVersion, List<Map<String, Object>> list) {
		String sql="";
		try {		
			Statement stat = conn.createStatement();
			
			for (Map<String,Object> map : list) {
				String colNames = "`project_id`,`project_version`";
				String colValues = "'"+projectId+"','"+projectVersion+"'";
			
				for (String key : map.keySet()) {
					//TODO shouldn't hard code these safety checks here
					String value = map.get(key).toString().replace("'", "''");
					value = value.replace("\\", "\\\\");
					key = key.replace('-','_');
					colNames += ",`"+key+"`";
					colValues += ",'"+value+"'";
				}
				sql += "\nINSERT INTO "+tableName+" ("+colNames+") VALUES ("+colValues+");";
				stat.addBatch("INSERT INTO "+tableName+" ("+colNames+") VALUES ("+colValues+");");
			}
			
			stat.executeBatch();
			stat.close();
			return true;
		} catch (SQLException e) {
			if (Loader.ENABLE_LOGGING) {
				System.out.println(sql);
				e.printStackTrace(System.out);
			}
			return false;
		}	
	}
	
	/**
	 * Returns true if there have been previous attempts to analyze this project by querying the 
	 * analyzer_log table
	 * @param file
	 * @return
	 */
	private boolean isProjectInDb(File file, File[] directoryFiles) {
		String sql = "";
		String id = FileUtils.getFileNameWithoutExtension(file);
		int version = FileUtils.getVersion(file); 
		if (version == 0) {
			version = getLatestVersionFromDirectory(directoryFiles, id);
			version++;
		}
		try {		
			Statement stat = conn.createStatement();
			sql = "SELECT * FROM analyzer_log WHERE project_id='"+id+"' AND project_version='"+version+"'";
			ResultSet rs = stat.executeQuery(sql);
			if (rs.next())
				return true;
			stat.close();
			return false;
		} catch (SQLException e) {
			if (Loader.ENABLE_LOGGING) {
				System.out.println(sql);
				e.printStackTrace(System.out);
			}
			return false;
		}	
	}
	
	/**
	 * Returns the latest version with this <code>projectId</code> that is in the database.  Discovered
	 * by querying the project_info table.
	 * @param conn
	 * @param projectId
	 * @return 0 if no version of the project is currently in the database
	 */
	private int getLatestVersionInDb(Connection conn, String projectId) {
		String sql = "";
		try {		
			Statement stat = conn.createStatement();
			sql = "SELECT project_version FROM project_info WHERE project_id='"+projectId+"' ORDER BY project_version DESC LIMIT 1";
			ResultSet versionRS = stat.executeQuery(sql);
			int version = 0;
			if (versionRS.next())
				version = versionRS.getInt("project_version");
			stat.close();
			return version;
		} catch (SQLException e) {
			if (Loader.ENABLE_LOGGING) {
				System.out.println(sql);
				e.printStackTrace(System.out);
			}
			return 0;
		}	
	}
	
	/**
	 * Returns the latest version with this <code>proejctId</code> that is in the file system.
	 * @param directory
	 * @param projectId
	 * @return
	 */
	private int getLatestVersionInFileSystem(File directory, final String projectId) {
		int version = 0;
		
		File[] files = directory.listFiles(new FilenameFilter() {
			public boolean accept(File dir, String name) {
				if (name.contains(".sb") && FileUtils.getFileNameWithoutExtension(name).equals(projectId)) {
					return true;
				}
				return false;
			}
		});
		
		for (File project : files) {
			int projectversion = FileUtils.getVersion(project);
			if (projectversion > version)
				version = projectversion;
		}
		return version;
	}
	
	private int getLatestVersionFromDirectory(File[] directoryFiles, String projectId) {
		int version = 0;
		
		for (File file : directoryFiles) {
			if (file.getName().contains(".sb") && FileUtils.getFileNameWithoutExtension(file.getName()).equals(projectId)) {
				int projectversion = FileUtils.getVersion(file);
				if (projectversion > version) 
						version = projectversion;
			}
		}
		
		return version;
		
	}
}
