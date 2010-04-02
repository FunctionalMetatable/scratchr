package scratch.remixanalyzer;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;




public class ProjectParser {
	
	HashMap<String,String> info; // info map obtained from ObjReader
	String remixFromId; // project id of the parent project
	
	Connection connection;
	
	/**
	 * Creates a ProjectParser that can be used to parse multiple projects. 
	 */
	public ProjectParser(Connection connection) {
		this.connection = connection;
	}
	
	public void evaluateFile(File file) throws IOException {
		FileInputStream fileStream = new FileInputStream(file);
		ObjReader reader = new ObjReader(fileStream);
		info = reader.getInfoTable();
		fileStream.close();	
		
		remixFromId = "";
		evaluateInfo();
	}
	
	public String getRemixFromId() {
		return remixFromId;
	}
	
	private void evaluateInfo() {
		if (!info.containsKey("history")) {
			return;
		}

		String value = info.get("history");
		value = value.replace("\r", "\n");
		String[] lines =  value.split("\n");
		String user = "";
		String file = "";
		for (int i=lines.length-1; i>=0; i--) {
			String[] tabs = lines[i].split("\t");

			// need to have a shared name located at tabs[3]
			if (tabs.length < 4 || tabs[1].equals("save"))
				continue;

			String filename = tabs[2];
			String sharename = tabs[3];

			// current user
			if (user.equals("")) {
				user = sharename;
				file = filename;
			}

			if (user.equalsIgnoreCase(sharename) && file.equalsIgnoreCase(filename)) {
				continue;
			}

			String id = findProjectId(filename, sharename);

			if (!id.equals("")) {
				remixFromId = id;
				break;
			}
		}
	}
	
	private String findProjectId(String projectname, String username) {
		try {
			PreparedStatement stmt = connection.prepareStatement(DatabaseConstants.REMIXED_FROM_PREPARED_STATEMENT);
			stmt.setString(1, projectname);
			stmt.setString(2, username);
			
			
			ResultSet rs = stmt.executeQuery();
			if (rs.next()) 
				return rs.getObject("id").toString();
			return "";
		} catch (SQLException e) {
			e.printStackTrace();
			return "";
		}
		
	}
}
