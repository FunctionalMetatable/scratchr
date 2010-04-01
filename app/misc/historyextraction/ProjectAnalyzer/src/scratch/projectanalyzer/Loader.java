package scratch.projectanalyzer;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;


/**
 * This class parses and verifies command line arguments, which include a database and 
 * a set of files to analyze.  A database connection is also opened so that errors with
 * database connections fail fast.
 */
class Loader {

	public static boolean ENABLE_LOGGING = false; 
	public static boolean ENABLE_FILENAME_PRINTING = false; 
	public static boolean ENABLE_FILESYSTEM_VERSION_CHECKING = false;
	public static boolean ENABLE_REMIX_PROJECT_QUERYING = false;
	
	
	private static Connection remixConnection;
	private static String remixurl;
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {		
		ArrayList<String> folders = new ArrayList<String>();  // folders/files to be analyzed
		String url = "";  // url of database to insert records into
		Date before = null;  // analyze only projects with modified time before this date
		Date after = null;  // analyze only projects with modified time after this date
		String clArgs = "";  // commandline arguments being supplied 
		
		for (int i=0;i<args.length;i++) {
			String arg = args[i];
			
			if (arg.equals("-p")) {  // print each file name before project is parsed
				ENABLE_FILENAME_PRINTING = true;
				clArgs += arg+" ";
			} else if (arg.equals("-l")) {  // log caught exceptions and analysis progress to the console
				ENABLE_LOGGING = true;
				clArgs += arg+" ";
			} else if (arg.equals("-f")) {  // use the file system to determine version numbers for current projects (as oppose to the database tables - reason to use database tables is if the project directory does not help determine version number)
				ENABLE_FILESYSTEM_VERSION_CHECKING = true;
				clArgs += arg+" ";
			} else if (arg.equals("-b")) {  // analyze only projects with modified time before a certain date
				try {
					before = new SimpleDateFormat("yyyy-M-d HH:mm:ss").parse(args[i+1]);
					clArgs += arg+" "+args[i+1]+" ";
					i++;
				} catch (ParseException e) {
					System.err.println("Could not parse date: "+args[i+1]);
					System.exit(0);
				}
			} else if (arg.equals("-a")) {  // analyze only projects with modified time after a certain date
				try {
					after = new SimpleDateFormat("yyyy-M-d HH:mm:ss").parse(args[i+1]);
					clArgs += arg+" "+args[i+1]+" ";
					i++;
				} catch (ParseException e) {
					System.err.println("Could not parse date: "+args[i+1]);
					System.exit(0);
				}
			} else if (arg.startsWith("-r")) {  // query projects and users table for project id of the project being remixed
				if (args.length-1 > i) {
					ENABLE_REMIX_PROJECT_QUERYING = true;
					remixurl = args[i+1];
					clArgs += arg+" "+remixurl.substring(0,remixurl.indexOf('?'))+" ";
					i++;
				}
			} else if (arg.startsWith("-")) {
				System.err.println("Option not recognized: "+arg);
			} else {
				if (url.equals("")) {
					url = arg;
				} else {
					folders.add(arg);
					clArgs += arg+" ";
				}
			}
		}
		
		if (url.equals("") || folders.size() == 0) {
			System.out.println("Usage: java -jar analyzer.jar \"host:port/database?user=username&password=password\" \"directory\" " +
					"[\"project1.sb\" \"project2.sb\" ...] [-l] [-p] [-f] [-r \"host:port/database?user=username&password=password\"] [-b \"yyyy-M-d HH:mm:ss\"] [-a \"yyyy-M-d HH:mm:ss\"]");
			System.exit(0);
		}
		
		if (before != null && after != null && before.before(after)) {
			System.err.println("No files that satisfy the criteria provided.");
			System.exit(0);
		}
		
		try {
			Class.forName("com.mysql.jdbc.Driver").newInstance();
			if (ENABLE_REMIX_PROJECT_QUERYING) {
				remixConnection = DriverManager.getConnection("jdbc:mysql://"+remixurl+"&autoReconnect=true");
			}
			Connection conn = DriverManager.getConnection("jdbc:mysql://"+url+"&autoReconnect=true");
			DatabaseBuilder builder = new DatabaseBuilder(folders, conn, before, after, clArgs.substring(0,clArgs.length()-1));
			builder.buildDb();
			
			conn.close();
		} catch (Exception e) {
			System.err.println("COULD NOT CONNECT TO DATABASE");
			e.printStackTrace();
			System.exit(0);
		}
		
	}

	/**
	 * Returns Connection to the database containing the projects and users tables for queries to determine
	 * remix origins.
	 * @return
	 * @throws SQLException 
	 */
	public static Connection getConnectionForRemixProjectQuerying() throws SQLException {
		if (remixConnection.isClosed())
			remixConnection = DriverManager.getConnection("jdbc:mysql://"+remixurl+"&autoReconnect=true");
		
		return remixConnection;
	}
}
