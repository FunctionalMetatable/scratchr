<?php

/***************************
 * REST Response component
 ***************************
 Author: Ubong Ukoh
 License: MIT
 Version: 1.0
 
 Description: This class is designed to marshall response
 messages in rest/xml format.
 ***************************/

class XmlrestResponseComponent extends Object
{
	/**
	 * TODO: finish
	 * 
	 * All messages are marshalled and appended to
	 * this variable
	 */
	var $doc = null;
	var $doc_tree = null;
	
	/**
	 * __construct: overides parent::__construct
	 * initializes header and calls the parent constructor
	 */
	function __construct()
	{
		//$doc = new_xmldoc('1.0');
		$doc = "<?xml version=\"1.0\">"
		parent::__construct();
	}

	function makeElement($tag) {
	}

	function makeRoot($tag) {
	}

	function setAttribute($name, $value) {
	}

	function appendChild($node_elemnt) {
	}

	/**
	 * Read
	 */
	function read()
	{
	}
	
	/**
	 * write
	 * Creates a new element with $tag name
	 * and appends it to the output buffer
	 */
	function write()
	{
	}

	/**
	 * returns a space delimited string of attributes="value"
	 * from the $options array parameter
	 */
	function __parseAttributes($options, $insertBefore = ' ', $insertAfter = null) {
		if (!is_array($exclude)) {
			$exclude = array();
		}
		if (is_array($options)) {
			$out = array();
			foreach($options as $key => $value) {
				if (!in_array($key, $exclude)) {
					if (in_array($key, $minimizedAttributes) && ($value === 1 || $value === true || $value === 'true' || in_array($value, $minimizedAttributes))) {
						$value = $key;
					} elseif(in_array($key, $minimizedAttributes)) {
						continue;
					}
					$out[] = "{$key}=\"{$value}\"";
				}
			}
			$out = join(' ', $out);
		   return $out ? $insertBefore . $out . $insertAfter : null;
		} else {
			return $options ? $insertBefore . $options . $insertAfter : null;
		}
	}
}
?>
