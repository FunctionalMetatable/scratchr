package scratch.remixanalyzer;
import java.sql.Connection;
import java.sql.DriverManager;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;


public class Loader {

	public static boolean ENABLE_LOGGING = false; 
	public static boolean ENABLE_FILENAME_PRINTING = false; 	
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {		
		ArrayList<String> folders = new ArrayList<String>();  // folders/files to be analyzed
		int skip = 0; // number of files to skip
		int numAnalyze = (int) Double.POSITIVE_INFINITY;  // number of files to analyze
		String url = "";  // url of database to insert records into
		String table = ""; // table to insert records into
		Date before = null; // only analyze projects with modification time before this date
		Date after = null; // only analyze projects with modification time after this date
		
		for (int i=0;i<args.length;i++) {
			String arg = args[i];
			
			if (arg.equals("-s")) {
				try {
					skip = Integer.parseInt(args[i+1]);
				} catch (NumberFormatException e) {
					System.err.println("Cannot skip "+args[i+1]+" number of files.");
					System.exit(0);
				}
				i++;
			} 	else if (arg.equals("-n")) {
				try {
					numAnalyze = Integer.parseInt(args[i+1]);
				} catch (NumberFormatException e) {
					System.err.println("Cannot analyze "+args[i+1]+" number of files.");
					System.exit(0);
				}
				i++;
			} else if (arg.equals("-p")) {
				ENABLE_FILENAME_PRINTING = true;
			} else if (arg.equals("-l")) {
				ENABLE_LOGGING = true;
			} else if (arg.equals("-b")) {
				try {
					before = new SimpleDateFormat("yyyy-M-d HH:mm:ss").parse(args[i+1]);
				} catch (ParseException e) {
					System.err.println("Could not parse date: "+args[i+1]);
					System.exit(0);
				}
			} else if (arg.equals("-a")) {
				try {
					after = new SimpleDateFormat("yyyy-M-d HH:mm:ss").parse(args[i+1]);
				} catch (ParseException e) {
					System.err.println("Could not parse date: "+args[i+1]);
					System.exit(0);
				}
			} else if (arg.startsWith("-")) {
				System.err.println("Option not recognized: "+arg);
			} else {
				if (url.equals("")) {
					url = arg;
				} else if (table.equals("")) {
					table = arg;
				} else {
					folders.add(arg);
				}
			}
		}
		
		if (url.equals("") || table.equals("") || folders.size() == 0) {
			System.out.println("Usage: java -jar remix_analyzer.jar \"host:port/database?user=username&password=password\" \"tablename\" \"directory\" " +
					"[\"project1.sb\" \"project2.sb\" ...] [-l] [-p] [-s numberOfFilesToSkip] [-n numberOfFilesToAnalyze] [-b \"yyyy-M-d HH:mm:ss\"] [-a \"yyyy-M-d HH:mm:ss\"]");
			System.exit(0);
		}
		
		try {
			Class.forName("com.mysql.jdbc.Driver").newInstance();
			Connection conn = DriverManager.getConnection("jdbc:mysql://"+url);
			DatabaseBuilder builder = new DatabaseBuilder(table, folders, conn, skip, numAnalyze, before, after);
			builder.buildDb();
			
			conn.close();
		} catch (Exception e) {
			e.printStackTrace();
			System.exit(0);
		}
		
	}

}
