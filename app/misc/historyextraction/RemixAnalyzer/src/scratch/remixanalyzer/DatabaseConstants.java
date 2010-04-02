package scratch.remixanalyzer;


public class DatabaseConstants {	
	
	static final String REMIXED_FROM_PREPARED_STATEMENT = "SELECT projects.id as id FROM projects, users WHERE projects.user_id = users.id and projects.name = ? and users.username = ?;"; 
	
	public static String getCreateRemixTableQuery(String tablename) {
		return "CREATE TABLE IF NOT EXISTS `"+tablename+
			"` (`project_id` BIGINT UNSIGNED, `based_on` BIGINT UNSIGNED, `date` DATETIME);";	
	}
}