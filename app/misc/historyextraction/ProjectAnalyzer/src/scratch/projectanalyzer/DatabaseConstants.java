package scratch.projectanalyzer;
import java.util.HashMap;
import java.util.Set;

/**
 * This class contains all constants and utilities used to build the database tables 
 * for the project analyses.  It is a singleton class and methods can be called using the 
 * <code>getInstance()</code> method.  All constants are static.
 */
public class DatabaseConstants {	
	// Table names
	static final String BLOCKSTABLE = "project_blocks_count";
	static final String SPRITESTACKSTABLE = "project_sprite_blocks_stack";
	static final String MEDIATABLE = "project_media";
	static final String INFOTABLE = "project_info";
	static final String SAVEHISTORYTABLE = "project_save_history";
	static final String SHAREHISTORYTABLE = "project_share_history";
	static final String MIDIINSTRUMENTSTABLE = "project_midi_instruments";
	static final String DRUMSTABLE = "project_drums";
	static final String SPRITESTABLE = "project_sprites";
	static final String STRINGSTABLE = "project_user_generated_strings";
	static final String DISCONNECTEDBLOCKS = "project_sprite_disconnected_blocks";
	static final String LOGTABLE = "analyzer_log";

	private static DatabaseConstants _instance = null;
	
	// SQL statement to find the project id of the remixed project
	static final String REMIXED_FROM_PREPARED_STATEMENT = "SELECT projects.id as id FROM projects, users WHERE projects.user_id = users.id and projects.name = ? and users.username = ?;"; 
	
	private HashMap<String, TableStructure> tableStructureMap;
	
	/**
	 * Returns this DatabaseConstants instance
	 * @return
	 */
	public static DatabaseConstants getInstance() {
		if (_instance == null) {
			_instance = new DatabaseConstants();
		}
		return _instance;
	}

	/**
	 * Builds and stores the schemas for the database tables
	 */
	private DatabaseConstants() {
		tableStructureMap = new HashMap<String, TableStructure>();
		
		// Blocks Table - tallies all block counts for each project
		String[][] blockFields = new String[ScratchConstants.BLOCKS.length+3][2];
		blockFields[0][0] = "project_id";
		blockFields[0][1] = "BIGINT UNSIGNED";
		blockFields[1][0] = "project_version";
		blockFields[1][1] = "INT UNSIGNED";
		int field = 2;
		for (String blockname : ScratchConstants.BLOCKS) {
			blockFields[field][0] = blockname;
			blockFields[field][1] = "INT UNSIGNED DEFAULT '0'";
			field++;
		}
		blockFields[field][0] = "other";
		blockFields[field][1] = "TEXT";
		tableStructureMap.put(BLOCKSTABLE, new TableStructure(blockFields));
		
		// Sprite Stacks table - stores the blocks stacks for each sprite in each project
		String[][] spriteStackFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"sprite_id", "INT UNSIGNED"},
				{"stack", "LONGTEXT"},
				{"human_readable", "LONGTEXT"}
		};
		tableStructureMap.put(SPRITESTACKSTABLE, new TableStructure(spriteStackFields));
		
		// Media table - stores all media used in each project per sprite
		String[][] mediaTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"sprite_id", "INT UNSIGNED"},
				{"name", "CHAR(255)"},
				{"size", "INT UNSIGNED"},
				{"type", "ENUM('image','sound')"},
				{"library", "BOOL DEFAULT '0'"}
		};
		tableStructureMap.put(MEDIATABLE, new TableStructure(mediaTableFields));
		
		// Info table - stores project meta data including remix information
		String[][] infoTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"hidden", "BOOL DEFAULT 0"},
				{"author", "TEXT"},
				{"comment", "LONGTEXT"},
				{"derived_from", "TEXT"},
				{"language", "CHAR(10)"},
				{"scratch_version", "TEXT"},
				{"history", "TEXT"},
				{"numsprites", "INT UNSIGNED DEFAULT '0'"},
				{"platform", "TEXT"},
				{"os_version","TEXT"},
				{"hasMotorBlocks","BOOL DEFAULT '0'"},
				{"isHosting", "BOOL DEFAULT '0'"},
				{"based_on_project_name", "TEXT"},
				{"based_on_username", "TEXT"},
				{"based_on_project_id", "BIGINT UNSIGNED"},
				{"other", "TEXT"}
		};
		tableStructureMap.put(INFOTABLE, new TableStructure(infoTableFields));
		
		// Share and Save History table - stores times of user shares and saves
		String[][] historyTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"date", "DATETIME"},
				{"filename", "TEXT"},
				{"local_name", "CHAR(255)"},
				{"share_name", "CHAR(255)"}
		};
		tableStructureMap.put(SAVEHISTORYTABLE, new TableStructure(historyTableFields));
		tableStructureMap.put(SHAREHISTORYTABLE, new TableStructure(historyTableFields));
		
		// Instruments table - stores the instrument number (or argument) used for the instrument block
		String[][] instrumentsTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"instrument", "INT UNSIGNED"},
				{"other", "CHAR(30)"}
		};
		tableStructureMap.put(MIDIINSTRUMENTSTABLE, new TableStructure(instrumentsTableFields));
		
		// Drums table - stores the drum number (or argument) used for the drums block
		String[][] drumsTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"drum", "INT UNSIGNED"},
				{"other", "CHAR(30)"}
		};
		tableStructureMap.put(DRUMSTABLE, new TableStructure(drumsTableFields));
		
		// Sprite table - stores summary information for each sprite in a project
		String[][] spritesTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"sprite_id", "INT UNSIGNED"},
				{"name", "CHAR(255)"},
				{"scripts", "INT UNSIGNED DEFAULT '0'"},
				{"sounds", "INT UNSIGNED DEFAULT '0'"},
				{"images", "INT UNSIGNED DEFAULT '0'"}
		};
		tableStructureMap.put(SPRITESTABLE, new TableStructure(spritesTableFields));
		
		// User Generated Strings table - stores all user generated strings
		String[][] stringsTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"sprite_id", "INT UNSIGNED"},
				{"string", "LONGTEXT"},
				{"type", "ENUM('comment','say','think','broadcast','variable_name','variable_value','list_name','list_value')"}
		};
		tableStructureMap.put(STRINGSTABLE, new TableStructure(stringsTableFields));
		
		// Disconnected Blocks table - stores all blocks that are not part of a script (with a hat block)
		String[][] disconnectedTableFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"sprite_id", "INT UNSIGNED"},
				{"block", "CHAR(30)"},
		};
		tableStructureMap.put(DISCONNECTEDBLOCKS, new TableStructure(disconnectedTableFields));
		
		// Log Table - used for logging, inserts a record before a project is analyze so failures can be easily detected
		String[][] logFields = {
				{"project_id", "BIGINT UNSIGNED"},
				{"project_version", "INT UNSIGNED"},
				{"time_started", "DATETIME"},
				{"arguments", "TEXT"},
		};
		tableStructureMap.put(LOGTABLE, new TableStructure(logFields));

	}
	
	/**
	 * Returns the Set of all tables this program generates
	 * @return
	 */
	public Set<String> allTables() {
		return tableStructureMap.keySet();
	}
	
	/**
	 * Returns an array of field names for a particular table in the database
	 * @param tablename
	 * @return
	 */
	public String[] getTableFields(String tablename) {
		return tableStructureMap.get(tablename).getFields();
	}
	
	/**
	 * Returns an SQL query that will create the table referenced by <code>tablename</code>
	 * @param tablename
	 * @return
	 */
	public String getCreateTableQueries(String tablename) {
		String query = "CREATE TABLE IF NOT EXISTS "+tablename+" (";
		TableStructure structure = tableStructureMap.get(tablename);
		for (String[] field : structure.getFieldsWithType()) {
			query += "`"+field[0]+"` "+field[1]+",";
		}
		query = query.substring(0, query.length()-1);
		query += ");";
		
		return query;
	}
	
}
	
	class TableStructure {
		String[][] fieldsWithType;
		String[] fields;
		
		public TableStructure(String[][] fieldsWithType) {
			this.fieldsWithType = fieldsWithType;
			fields = new String[fieldsWithType.length];
			for (int i=0; i<fieldsWithType.length; i++) {
				fields[i] = fieldsWithType[i][0];
			}
			
		}
		
		public String[][] getFieldsWithType() {
			return fieldsWithType;
		}
		
		public String[] getFields() {
			return fields;
		}
	}