<?php
/**
 * Note from Bakyt Niyazov
 *
 * This shell script is just an adoption of Cake's core i18n shell script and it's extract task.
 * Extract task needs an interaction with user while the intention of this script is
 * to be invoked by the cron - so it won't require any interaction at all.
 *
 * You will be more happy if you use Cake's i18n shell
 *
 * Best! (Questions? Sure! bakyt@bakytn.com)
 *
 * usage: cake LocaleExtractorAndRegenerator
 * you can also provide: -output (where to put .pot file) -path (path where extract from)
 *
 */
/*
 * @license		 http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class LocaleExtractorAndRegeneratorShell extends Shell {
/**
 * Path to use when looking for strings
 *
 * @var string
 * @access public
 */
	var $path = null;
/**
 * Files from where to extract
 *
 * @var array
 * @access public
 */
	var $files = array();
/**
 * Filename where to deposit translations
 *
 * @var string
 * @access private
 */
	var $__filename = 'default';
/**
 * True if all strings should be merged into one file
 *
 * @var boolean
 * @access private
 */
	var $__oneFile = true;
/**
 * Current file being processed
 *
 * @var string
 * @access private
 */
	var $__file = null;
/**
 * Extracted tokens
 *
 * @var array
 * @access private
 */
	var $__tokens = array();
/**
 * Extracted strings
 *
 * @var array
 * @access private
 */
	var $__strings = array();
/**
 * History of file versions
 *
 * @var array
 * @access private
 */
	var $__fileVersions = array();
/**
 * Destination path
 *
 * @var string
 * @access private
 */
	var $__output = null;

/**
 * Msgmerge command full path
 *
 * @var string
 * @access private
 */
	var $__msgmerge = null;

/**
 * Override startup of the Shell
 *
 * @access public
 */
	function startup() {
	}
/**
 * Override main() for help message hook
 *
 * @access public
 */
	function main() {
		$command = explode(' ', `whereis msgmerge`);
		$this->__msgmerge = trim($command[1]);
		if (empty($this->__msgmerge)) {
			$this->err('"msgmerge" command\'s path was not found. "whereis" command did not help. Please install gettext package');
		}

		// Assuming that this script is located at any "app" (app folder name could be different - no problem)
		// I'm setting variables (just to not pass long parameters
		$this->__output = APP . 'locale' . DS;
		$this->path = rtrim(APP, DS);

		$this->execute();
	}

	function execute() {
		if (isset($this->params['files']) && !is_array($this->params['files'])) {
			$this->files = explode(',', $this->params['files']);
		}
		if (isset($this->params['path'])) {
			$this->path = $this->params['path'];
		} elseif(is_null($this->path)) {
			$response = '';
			while ($response == '') {
				$response = $this->in("What is the full path you would like to extract?\nExample: " . $this->params['root'] . DS . "myapp\n[Q]uit", null, 'Q');
				if (strtoupper($response) === 'Q') {
					$this->out('Extract Aborted');
					$this->_stop();
				}
			}

			if (is_dir($response)) {
				$this->path = $response;
			} else {
				$this->err('The directory path you supplied was not found. Please try again.');
				$this->execute();
			}
		}

		if (isset($this->params['debug'])) {
			$this->path = ROOT;
			$this->files = array(__FILE__);
		}

		if (isset($this->params['output'])) {
			$this->__output = $this->params['output'];
		} elseif (is_null($this->__output)) {
			$response = '';
			while ($response == '') {
				$response = $this->in("What is the full path you would like to output?\nExample: " . $this->path . DS . "locale\n[Q]uit", null, $this->path . DS . "locale");
				if (strtoupper($response) === 'Q') {
					$this->out('Extract Aborted');
					$this->_stop();
				}
			}

			if (is_dir($response)) {
				$this->__output = $response . DS;
			} else {
				$this->err('The directory path you supplied was not found. Please try again.');
				$this->execute();
			}
		}

		if (empty($this->files)) {
			$this->files = $this->__searchDirectory();
		}
		$this->__extract();

		$this->regenerator();
	}

	/**
	 * Requires: "msgmerge" command for every locale
	 * and updates their default.po with updated phrases and comment out
	 * deleted or changed phrases
	 *
	 *
	 */
	function regenerator() {
		$poTemplateFullPath = $this->__output . $this->__filename . '.pot';

		$dh = opendir($this->__output);
		if (!$dh) {
			$this->err('Cannot work with ' . $this->__output . ' folder. It unreadable or does not exist');
			$this->_stop();
		}
		while (false !== ($file = readdir($dh))) {
			if (in_array($file, array('.', '..')) || !is_dir($this->__output . $file) || !is_dir($this->__output . $file . DS . 'LC_MESSAGES')) {
				continue;
			}

			// using hard coded file name.. you can adapt to scan any po file in the dir
			// look at chunk below
			$poFile = $this->__output . $file . DS . 'LC_MESSAGES' . DS . 'default.po';
			if (is_file($poFile) && is_writable($poFile)) {
				// outputting into intermediate file ".updated"
				// just because msgmerge has problems when it generates
				// the new file from the source po file
				shell_exec($this->__msgmerge . ' ' . $poFile . ' ' . $poTemplateFullPath . ' > ' . $poFile . '.updated');
				// now just rename it
				copy($poFile . '.updated', $poFile);
			}

			/*$nextLevel = opendir($this->__output . $file . DS . 'LC_MESSAGES');
			while (false !== ($poFile = readdir($nextLevel))) {
				if (in_array($poFile, array('.', '..')) || substr($poFile, -3) !== '.po') {
					continue;
				}
				echo "\n";
				print_r($poFile);
			}
			closedir($nextLevel);
			*/

		}
		closedir($dh);
	}

/**
 * Extract text
 *
 * @access private
 */
	function __extract() {
		$this->out('');
		$this->out('');
		$this->out(__('Extracting...', true));
		$this->hr();
		$this->out(__('Path: ', true). $this->path);
		$this->out(__('Output Directory: ', true). $this->__output);
		$this->hr();
		$this->__extractTokens();
	}

/**
 * Extract tokens out of all files to be processed
 *
 * @access private
 */
	function __extractTokens() {
		foreach ($this->files as $file) {
			if (false !== strpos($file, $this->path . DS . 'vendors')) {
				continue;
			}
			if (false !== strpos($file, $this->path . DS . 'tmp')) {
				continue;
			}
			if (false !== strpos($file, $this->path . DS . 'tests')) {
				continue;
			}
			if (false !== strpos($file, $this->path . DS . 'webroot')) {
				continue;
			}
			$this->__file = $file;

			$this->out(sprintf(__('Processing %s...', true), str_replace(APP, '', $file)));

			$code = file_get_contents($file);

			$this->__findVersion($code, $file);
			$allTokens = token_get_all($code);
			$this->__tokens = array();
			$lineNumber = 1;

			foreach ($allTokens as $token) {
				if ((!is_array($token)) || (($token[0] != T_WHITESPACE) && ($token[0] != T_INLINE_HTML))) {
					if (is_array($token)) {
						$token[] = $lineNumber;
					}
					$this->__tokens[] = $token;
				}

				if (is_array($token)) {
					$lineNumber += count(split("\n", $token[1])) - 1;
				} else {
					$lineNumber += count(split("\n", $token)) - 1;
				}
			}
			unset($allTokens);
			$this->basic();
			$this->basic('__c');
			$this->extended();
			$this->extended('__dc', 2);
			$this->extended('__n', 0, true);
			$this->extended('__dn', 2, true);
			$this->extended('__dcn', 4, true);
		}
		$this->__buildFiles();
		$this->__writeFiles();
		$this->out('Done.');
	}
/**
 * Will parse  __(), __c() functions
 *
 * @param string $functionName Function name that indicates translatable string (e.g: '__')
 * @access public
 */
	function basic($functionName = '__') {
		$count = 0;
		$tokenCount = count($this->__tokens);

		while (($tokenCount - $count) > 3) {
			list($countToken, $parenthesis, $middle, $right) = array($this->__tokens[$count], $this->__tokens[$count + 1], $this->__tokens[$count + 2], $this->__tokens[$count + 3]);
			if (!is_array($countToken)) {
				$count++;
				continue;
			}

			list($type, $string, $line) = $countToken;
			if (($type == T_STRING) && ($string == $functionName) && ($parenthesis == '(')) {

				if (in_array($right, array(')', ','))
				&& (is_array($middle) && ($middle[0] == T_CONSTANT_ENCAPSED_STRING))) {

					if ($this->__oneFile === true) {
						$this->__strings[$this->__formatString($middle[1])][$this->__file][] = $line;
					} else {
						$this->__strings[$this->__file][$this->__formatString($middle[1])][] = $line;
					}
				} else {
					$this->__markerError($this->__file, $line, $functionName, $count);
				}
			}
			$count++;
		}
	}
/**
 * Will parse __d(), __dc(), __n(), __dn(), __dcn()
 *
 * @param string $functionName Function name that indicates translatable string (e.g: '__')
 * @param integer $shift Number of parameters to shift to find translateable string
 * @param boolean $plural Set to true if function supports plural format, false otherwise
 * @access public
 */
	function extended($functionName = '__d', $shift = 0, $plural = false) {
		$count = 0;
		$tokenCount = count($this->__tokens);

		while (($tokenCount - $count) > 7) {
			list($countToken, $firstParenthesis) = array($this->__tokens[$count], $this->__tokens[$count + 1]);
			if (!is_array($countToken)) {
				$count++;
				continue;
			}

			list($type, $string, $line) = $countToken;
			if (($type == T_STRING) && ($string == $functionName) && ($firstParenthesis == '(')) {
				$position = $count;
				$depth = 0;

				while ($depth == 0) {
					if ($this->__tokens[$position] == '(') {
						$depth++;
					} elseif ($this->__tokens[$position] == ')') {
						$depth--;
					}
					$position++;
				}

				if ($plural) {
					$end = $position + $shift + 7;

					if ($this->__tokens[$position + $shift + 5] === ')') {
						$end = $position + $shift + 5;
					}

					if (empty($shift)) {
						list($singular, $firstComma, $plural, $seoncdComma, $endParenthesis) = array($this->__tokens[$position], $this->__tokens[$position + 1], $this->__tokens[$position + 2], $this->__tokens[$position + 3], $this->__tokens[$end]);
						$condition = ($seoncdComma == ',');
					} else {
						list($domain, $firstComma, $singular, $seoncdComma, $plural, $comma3, $endParenthesis) = array($this->__tokens[$position], $this->__tokens[$position + 1], $this->__tokens[$position + 2], $this->__tokens[$position + 3], $this->__tokens[$position + 4], $this->__tokens[$position + 5], $this->__tokens[$end]);
						$condition = ($comma3 == ',');
					}
					$condition = $condition &&
						(is_array($singular) && ($singular[0] == T_CONSTANT_ENCAPSED_STRING)) &&
						(is_array($plural) && ($plural[0] == T_CONSTANT_ENCAPSED_STRING));
				} else {
					if ($this->__tokens[$position + $shift + 5] === ')') {
						$comma = $this->__tokens[$position + $shift + 3];
						$end = $position + $shift + 5;
					} else {
						$comma = null;
						$end = $position + $shift + 3;
					}

					list($domain, $firstComma, $text, $seoncdComma, $endParenthesis) = array($this->__tokens[$position], $this->__tokens[$position + 1], $this->__tokens[$position + 2], $comma, $this->__tokens[$end]);
					$condition = ($seoncdComma == ',' || $seoncdComma === null) &&
						(is_array($domain) && ($domain[0] == T_CONSTANT_ENCAPSED_STRING)) &&
						(is_array($text) && ($text[0] == T_CONSTANT_ENCAPSED_STRING));
				}

				if (($endParenthesis == ')') && $condition) {
					if ($this->__oneFile === true) {
						if ($plural) {
							$this->__strings[$this->__formatString($singular[1]) . "\0" . $this->__formatString($plural[1])][$this->__file][] = $line;
						} else {
							$this->__strings[$this->__formatString($text[1])][$this->__file][] = $line;
						}
					} else {
						if ($plural) {
							$this->__strings[$this->__file][$this->__formatString($singular[1]) . "\0" . $this->__formatString($plural[1])][] = $line;
						} else {
							$this->__strings[$this->__file][$this->__formatString($text[1])][] = $line;
						}
					}
				} else {
					$this->__markerError($this->__file, $line, $functionName, $count);
				}
			}
			$count++;
		}
	}
/**
 * Build the translate template file contents out of obtained strings
 *
 * @access private
 */
	function __buildFiles() {
		foreach ($this->__strings as $str => $fileInfo) {
			$output = '';
			$occured = $fileList = array();

			if ($this->__oneFile === true) {
				foreach ($fileInfo as $file => $lines) {
					$occured[] = "$file:" . join(';', $lines);

					if (isset($this->__fileVersions[$file])) {
						$fileList[] = $this->__fileVersions[$file];
					}
				}
				$occurances = join("\n#: ", $occured);
				$occurances = str_replace($this->path, '', $occurances);
				$output = "#: $occurances\n";
				$filename = $this->__filename;

				if (strpos($str, "\0") === false) {
					$output .= "msgid \"$str\"\n";
					$output .= "msgstr \"\"\n";
				} else {
					list($singular, $plural) = explode("\0", $str);
					$output .= "msgid \"$singular\"\n";
					$output .= "msgid_plural \"$plural\"\n";
					$output .= "msgstr[0] \"\"\n";
					$output .= "msgstr[1] \"\"\n";
				}
				$output .= "\n";
			} else {
				foreach ($fileInfo as $file => $lines) {
					$filename = $str;
					$occured = array("$str:" . join(';', $lines));

					if (isset($this->__fileVersions[$str])) {
						$fileList[] = $this->__fileVersions[$str];
					}
					$occurances = join("\n#: ", $occured);
					$occurances = str_replace($this->path, '', $occurances);
					$output .= "#: $occurances\n";

					if (strpos($file, "\0") === false) {
						$output .= "msgid \"$file\"\n";
						$output .= "msgstr \"\"\n";
					} else {
						list($singular, $plural) = explode("\0", $file);
						$output .= "msgid \"$singular\"\n";
						$output .= "msgid_plural \"$plural\"\n";
						$output .= "msgstr[0] \"\"\n";
						$output .= "msgstr[1] \"\"\n";
					}
					$output .= "\n";
				}
			}
			$this->__store($filename, $output, $fileList);
		}
	}
/**
 * Prepare a file to be stored
 *
 * @param string $file Filename
 * @param string $input What to store
 * @param array $fileList File list
 * @param integer $get Set to 1 to get files to store, false to set
 * @return mixed If $get == 1, files to store, otherwise void
 * @access private
 */
	function __store($file = 0, $input = 0, $fileList = array(), $get = 0) {
		static $storage = array();

		if (!$get) {
			if (isset($storage[$file])) {
				$storage[$file][1] = array_unique(array_merge($storage[$file][1], $fileList));
				$storage[$file][] = $input;
			} else {
				$storage[$file] = array();
				$storage[$file][0] = $this->__writeHeader();
				$storage[$file][1] = $fileList;
				$storage[$file][2] = $input;
			}
		} else {
			return $storage;
		}
	}
/**
 * Write the files that need to be stored
 *
 * @access private
 */
	function __writeFiles() {
		$output = $this->__store(0, 0, array(), 1);
		$output = $this->__mergeFiles($output);

		foreach ($output as $file => $content) {
			$tmp = str_replace(array($this->path, '.php','.ctp','.thtml', '.inc','.tpl' ), '', $file);
			$tmp = str_replace(DS, '.', $tmp);
			$file = str_replace('.', '-', $tmp) .'.pot';
			$fileList = $content[1];

			unset($content[1]);

			$fileList = str_replace(array($this->path), '', $fileList);

			if (count($fileList) > 1) {
				$fileList = "Generated from files:\n#  " . join("\n#  ", $fileList);
			} elseif (count($fileList) == 1) {
				$fileList = 'Generated from file: ' . join('', $fileList);
			} else {
				$fileList = 'No version information was available in the source files.';
			}

			$fp = fopen($this->__output . $file, 'w');
			fwrite($fp, str_replace('--VERSIONS--', $fileList, join('', $content)));
			fclose($fp);
		}
	}
/**
 * Merge output files
 *
 * @param array $output Output to merge
 * @return array Merged output
 * @access private
 */
	function __mergeFiles($output) {
		foreach ($output as $file => $content) {
			if (count($content) <= 1 && $file != $this->__filename) {
				@$output[$this->__filename][1] = array_unique(array_merge($output[$this->__filename][1], $content[1]));

				if (!isset($output[$this->__filename][0])) {
					$output[$this->__filename][0] = $content[0];
				}
				unset($content[0]);
				unset($content[1]);

				foreach ($content as $msgid) {
					$output[$this->__filename][] = $msgid;
				}
				unset($output[$file]);
			}
		}
		return $output;
	}
/**
 * Build the translation template header
 *
 * @return string Translation template header
 * @access private
 */
	function __writeHeader() {
		$output  = "# LANGUAGE translation of CakePHP Application\n";
		$output .= "# Copyright YEAR NAME <EMAIL@ADDRESS>\n";
		$output .= "# --VERSIONS--\n";
		$output .= "#\n";
		$output .= "#, fuzzy\n";
		$output .= "msgid \"\"\n";
		$output .= "msgstr \"\"\n";
		$output .= "\"Project-Id-Version: PROJECT VERSION\\n\"\n";
		$output .= "\"POT-Creation-Date: " . date("Y-m-d H:iO") . "\\n\"\n";
		$output .= "\"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n\"\n";
		$output .= "\"Last-Translator: NAME <EMAIL@ADDRESS>\\n\"\n";
		$output .= "\"Language-Team: LANGUAGE <EMAIL@ADDRESS>\\n\"\n";
		$output .= "\"MIME-Version: 1.0\\n\"\n";
		$output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
		$output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
		$output .= "\"Plural-Forms: nplurals=INTEGER; plural=EXPRESSION;\\n\"\n\n";
		return $output;
	}
/**
 * Find the version number of a file looking for SVN commands
 *
 * @param string $code Source code of file
 * @param string $file File
 * @access private
 */
	function __findVersion($code, $file) {
		$header = '$Id' . ':';
		if (preg_match('/\\' . $header . ' [\\w.]* ([\\d]*)/', $code, $versionInfo)) {
			$version = str_replace(ROOT, '', 'Revision: ' . $versionInfo[1] . ' ' .$file);
			$this->__fileVersions[$file] = $version;
		}
	}
/**
 * Format a string to be added as a translateable string
 *
 * @param string $string String to format
 * @return string Formatted string
 * @access private
 */
	function __formatString($string) {
		$quote = substr($string, 0, 1);
		$string = substr($string, 1, -1);
		if ($quote == '"') {
			$string = stripcslashes($string);
		} else {
			$string = strtr($string, array("\\'" => "'", "\\\\" => "\\"));
		}
		return addcslashes($string, "\0..\37\\\"");
	}
/**
 * Indicate an invalid marker on a processed file
 *
 * @param string $file File where invalid marker resides
 * @param integer $line Line number
 * @param string $marker Marker found
 * @param integer $count Count
 * @access private
 */
	function __markerError($file, $line, $marker, $count) {
		$this->out("Invalid marker content in $file:$line\n* $marker(", true);
		$count += 2;
		$tokenCount = count($this->__tokens);
		$parenthesis = 1;

		while ((($tokenCount - $count) > 0) && $parenthesis) {
			if (is_array($this->__tokens[$count])) {
				$this->out($this->__tokens[$count][1], false);
			} else {
				$this->out($this->__tokens[$count], false);
				if ($this->__tokens[$count] == '(') {
					$parenthesis++;
				}

				if ($this->__tokens[$count] == ')') {
					$parenthesis--;
				}
			}
			$count++;
		}
		$this->out("\n", true);
	}
/**
 * Search the specified path for files that may contain translateable strings
 *
 * @param string $path Path (or set to null to use current)
 * @return array Files
 * @access private
 */
	function __searchDirectory($path = null) {
		if ($path === null) {
			$path = $this->path .DS;
		}
		$files = glob("$path*.{php,ctp,thtml,inc,tpl}", GLOB_BRACE);
		$dirs = glob("$path*", GLOB_ONLYDIR);

		foreach ($dirs as $dir) {
			if (!preg_match("!(^|.+/)(CVS|.svn)$!", $dir)) {
				$files = array_merge($files, $this->__searchDirectory("$dir" . DS));
				if (($id = array_search($dir . DS . 'extract.php', $files)) !== FALSE) {
					unset($files[$id]);
				}
			}
		}
		return $files;
	}


/**
 * Show help options
 *
 * @access public
 */
	function help() {
		$this->out(__('CakePHP Language String Extraction:', true));
		$this->hr();
		$this->out(__('The Extract script generates .pot file(s) with translations', true));
		$this->out(__('By default the .pot file(s) will be place in the locale directory of -app', true));
		$this->out(__('By default -app is ROOT/app', true));
		$this->hr();
		$this->out(__('usage: cake i18n extract [command] [path...]', true));
		$this->out('');
		$this->out(__('commands:', true));
		$this->out(__('   -app [path...]: directory where your application is located', true));
		$this->out(__('   -root [path...]: path to install', true));
		$this->out(__('   -core [path...]: path to cake directory', true));
		$this->out(__('   -path [path...]: Full path to directory to extract strings', true));
		$this->out(__('   -output [path...]: Full path to output directory', true));
		$this->out(__('   -files: [comma separated list of files, full path to file is needed]', true));
		$this->out(__('   cake i18n extract help: Shows this help message.', true));
		$this->out(__('   -debug: Perform self test.', true));
		$this->out('');
	}
}