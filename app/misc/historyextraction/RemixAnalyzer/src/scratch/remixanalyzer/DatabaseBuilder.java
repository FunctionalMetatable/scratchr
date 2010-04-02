package scratch.remixanalyzer;

import java.io.File;
import java.sql.Connection;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Comparator;
import java.util.Date;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;


public class DatabaseBuilder {

	LinkedList<File> sbFilesQueue;
	boolean allFilesQueued;
	int evaluatedFiles = 0;
	
	Connection conn;
	String tablename;
	
	class FileSystemReader extends Thread {
		
		ArrayList<String> dirs;
		int skip;
		int numAnalyze;
		int filesSkipped, filesAnalyzed;
		Date before = null;
		Date after = null;
		
		public FileSystemReader(ArrayList<String> dirs, int skip, int numAnalyze, Date before, Date after) {
			this.dirs = dirs;
			this.skip = skip;
			this.numAnalyze = numAnalyze;
			filesSkipped = 0;
			filesAnalyzed = 0;
			this.before = before;
			this.after = after;
		}

		public void run() {
			if (Loader.ENABLE_LOGGING) {
				System.out.println("Skipping first "+skip+" files.");
			}
			
			for (String dir : dirs) {				
				File file = new File(dir);
				if (file.isDirectory()) {
					File[] files = file.listFiles();
					if (numAnalyze != (int) Double.POSITIVE_INFINITY || skip != 0) {
						Arrays.sort(files, new Comparator<File>(){
							public int compare(File f1, File f2)
							{
								return Long.valueOf(f1.lastModified()).compareTo(f2.lastModified());
							} 
						});
					}
					for (File single : files) {
						if (filesAnalyzed == numAnalyze) {
							break;
						}
						addSbFilesToList(single);
					}
				} else {
					addSbFilesToList(file);
				}
			}
			
			allFilesQueued = true;
		}
		
		private void addSingleFile(File file) {
			if (getExtension(file).equals("sb") && getVersion(file) == 0) {
				if (before != null && file.lastModified() > before.getTime()) {
					return;
				}
				
				if (after != null && file.lastModified() < after.getTime()) {
					return;
				}
				
				if (skip > filesSkipped) {
					filesSkipped++;
					return;
				}
				synchronized(sbFilesQueue) {
					if (filesAnalyzed < numAnalyze) {
						sbFilesQueue.add(file);
						filesAnalyzed++;
					}
				}
			}
		}
		
		private void addSbFilesToList(File file) {
			while (sbFilesQueue.size() > 500) { // keep queue at about 500 files
				try {
					sleep(500);
				} catch (InterruptedException e) {
				}
			}
			
			if (numAnalyze == filesAnalyzed) {
				return;
			}
			
			if (!file.isDirectory()) {
				addSingleFile(file);
				return;
			}
			
			File[] files = file.listFiles();
			if (numAnalyze != (int) Double.POSITIVE_INFINITY || skip != 0) {
				Arrays.sort(files, new Comparator<File>(){
					public int compare(File f1, File f2)
					{
						return Long.valueOf(f1.lastModified()).compareTo(f2.lastModified());
					} 
				});
			}
			for (File single : files) {
				if (numAnalyze == filesAnalyzed) {
					break;
				}
				
				if (single.isDirectory()) {
					//addSbFilesToList(single);
					continue;  // only go 2 levels deep (special case of Scratch projects directory)
				} else {
					addSingleFile(single);
				}			
			}
		}
	}
	
	public DatabaseBuilder(String tablename, ArrayList<String> files, Connection conn, int skip, int numAnalyze, Date before, Date after) throws SQLException {
		this.conn = conn;
		this.tablename = tablename;
		
		sbFilesQueue = new LinkedList<File>();
		allFilesQueued = false;
		
		// Create Database Tables
		createTables();
		
		Thread fileReader = new FileSystemReader(files, skip, numAnalyze, before, after);
		fileReader.start();			
	}
	
	private void createTables() throws SQLException {
		String query = DatabaseConstants.getCreateRemixTableQuery(tablename);
		conn.createStatement().execute(query);
	}
	
	public void buildDb() {
		long starttime = System.currentTimeMillis();
		
		ProjectParser parser = new ProjectParser(conn);
		
		while (sbFilesQueue.isEmpty() && !allFilesQueued) {
            try {
            	Thread.sleep(500);
            } catch (InterruptedException ignored) {
            }
        }
		
		if (Loader.ENABLE_LOGGING) {
			System.out.println("FILES QUEUED");
		}

		DateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:s");
		
		// Evaluate all .sb files and insert data into database
		while (sbFilesQueue.size() > 0) {
			File sbFile;
			synchronized (sbFilesQueue) {
				sbFile = sbFilesQueue.removeFirst();
			}
			
			if (Loader.ENABLE_FILENAME_PRINTING) {
				System.out.println((evaluatedFiles+1) + ": " + sbFile.getAbsolutePath());
			}
			
			try {
				parser.evaluateFile(sbFile);
			} catch (Exception e) {
				if (Loader.ENABLE_LOGGING) {
					System.out.println("Exception with File: " + sbFile);
					e.printStackTrace(System.out);
				}
				continue;
			}
			evaluatedFiles++;
			if (Loader.ENABLE_LOGGING && evaluatedFiles % 1000 == 0) {
				System.out.println(evaluatedFiles + " files evaluated in " + (System.currentTimeMillis()-starttime)+"ms");
			}	
			
			String remixedFrom = parser.getRemixFromId();
			if (remixedFrom.equals("")) {
				continue;
			}
			
			String id = getFileNameWithoutExtension(sbFile);
			String modifiedTime = df.format(new Date(sbFile.lastModified()));
		
			HashMap<String,Object> remixMap = new HashMap<String,Object>(2);
			remixMap.put("date",modifiedTime);
			remixMap.put("based_on", remixedFrom);			
			insertIntoTable(conn, tablename, id, remixMap);
		}

		if (Loader.ENABLE_LOGGING)
			System.out.println("Total time ("+evaluatedFiles+"): "+(System.currentTimeMillis()-starttime)+"ms");
	}

	private boolean insertIntoTable(Connection conn, String tableName, String projectId, Map<String,Object> map) {
			String colNames = "`project_id`";
			String colValues = "'"+projectId+"'";
			
			for (String key : map.keySet()) {
				String value = map.get(key).toString().replace("'", "''");
				value = value.replace("\\", "\\\\");
				colNames += ",`"+key+"`";
				colValues += ",'"+value+"'";
			}
			
			String sql = "INSERT INTO "+tableName+" ("+colNames;
			sql += ") VALUES ("+colValues+");";
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
	 * 
	 * @param file
	 * @return
	 */
	private String getExtension(File file) {
		String name = file.getName();
		int index = name.lastIndexOf('.');
		if (index<0) { // no last index
			return "";
		}
		return name.substring(index+1);
	}
	
	/**
	 * 
	 * @param file
	 * @return 0 if current version
	 */
	private int getVersion(File file) {
		String name = file.getName();
		int lastIndex = name.lastIndexOf('.');
		int firstIndex = name.indexOf('.');
		if (lastIndex != firstIndex) {
			Integer integer = Integer.decode(name.substring(firstIndex+1, lastIndex));
			return integer.intValue();
		}
		return 0;
	}
	
	private String getFileNameWithoutExtension(File file) {
		String name = file.getName();
		int index = name.lastIndexOf('.');
		if (index<0) { // no last index
			return name;
		}
		return name.substring(0,index);
	}
}
