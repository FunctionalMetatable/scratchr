package scratch.projectanalyzer;
import java.io.File;

/**
 * A collection of useful methods for Files, in particular Scratch Files that were uploaded to the 
 * website.  
 * 
 * These are often of the form 123.4.sb where 4 is the version number of the project.  The current version
 * of the project does not have an extension for the version (123.sb).  Hidden files have the additional
 * extension of .hid (123.4.sb.hid) 
 *
 */
public class FileUtils {
	
	/**
	 * Returns the (last) extension of the file. 
	 * (ie: 123.4.sb.hid will return hid)
	 * @param file
	 * @return
	 */
	public static String getExtension(File file) {
		String name = file.getName();
		int index = name.lastIndexOf('.');
		if (index<0) { // no last index
			return "";
		}
		return name.substring(index+1);
	}
	
	/**
	 * Returns true if <code>ext</code> is one of the extensions for File <code>file</code>
	 * (ie: <code>isFileOfType(123.4.sb.hid, sb)</code> will return true)
	 * @param file
	 * @param ext
	 * @return
	 */
	public static boolean isFileOfType(File file, String ext) {
		String extension = "."+ext;
		if (file.getName().indexOf(extension) > 0) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the version of the file as specified by the file name.  
	 * ie: 123.1.sb would have version number 1, 123.sb would have version number 0
	 * @param file
	 * @return 0 if it is the current version
	 */
	public static int getVersion(File file) {
		String name = file.getName();
		int sbIndex = name.indexOf(".sb");
		int firstIndex = name.indexOf('.');
		if (sbIndex != -1 && sbIndex != firstIndex) {
			Integer integer = Integer.decode(name.substring(firstIndex+1, sbIndex));
			return integer.intValue();
		}
		return 0;
	}
	
	/**
	 * Returns the name of the file without any extensions.  Specifically will return 
	 * the file name up until the first '.'
	 * @param file
	 * @return
	 */
	public static String getFileNameWithoutExtension(File file) {
		String name = file.getName();
		return getFileNameWithoutExtension(name);
	}
	
	/**
	 * Returns the name of the file without any extensions.  Specifically will return 
	 * the file name up until the first '.'
	 * @param filename
	 * @return
	 */
	public static String getFileNameWithoutExtension(String filename) {
		int index = filename.indexOf('.');
		if (index<0) { // no last index
			return filename;
		}

		return filename.substring(0,index);
	}
}
