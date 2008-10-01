<?php
/**
 * Head Helper
 * Register <head> tags from helpers, then print them
 * in head through layout.
 * @author RosSoft
 * @license MIT
 * @version 0.2 
 */
class HeadHelper extends Helper
{
    var $helpers=array('html','javascript');
   
    var $_library; //static array of items to be included            

    function __construct()
    {
           static $library=array();  //for php4 compat
           $this->_library=& $library;
    }

    /**
     * Adds a css file to array
     * @param string $file CSS file to be included
     * @param string $param Array of htmlAttributes
     */
    function register_css($file,$htmlAttributes=null)
    {
        $this->_register(array($file,'css',$htmlAttributes));
    }
   
    /**
     * Adds an inline css block to array
     * @param string $css CSS tags to be included
     * @param string $param Array of htmlAttributes
     */
    function register_cssblock($css,$htmlAttributes=null)
    {
        $this->_register(array($css,'cssblock',$htmlAttributes));
    }
   
   
    /**
     * Adds a js file to array
     * @param string $file CSS file to be included
     * @param string $param Array of htmlAttributes
     */
    function register_js($file)
    {
        $this->_register(array($file,'js'));
    }
   
    /**
     * Adds a javascript block to array
     * @param string $javascript Javascript block to be included
     * @param string $param Array of htmlAttributes
     */
    function register_jsblock($javascript)
    {
        $this->_register(array($javascript,'jsblock'));
    }   
   
    /**
     * Adds a meta tag to array
     * @param array $htmlAttributes Array of html attributes of meta tag 
     */
    function register_meta($htmlAttributes)
    {
        $this->_register(array($htmlAttributes,'meta'));
    }
   
       
    /**
     * Adds a link tag to array
     * @param array $htmlAttributes Array of html attributes of meta tag 
     */
    function register_link($htmlAttributes)
    {
        $this->_register(array($htmlAttributes,'link'));
    }         
   
    /**
     * Adds a raw sequence of html tags to array
     * @param string $raw Sequence of html tags
     */
    function register_raw($raw)
    {
        $this->_register(array($raw,'raw'));
    }

    /**
     * Prints the html for all of the items registered
     * @return string
     */          
    function print_registered()
    {
        foreach ($this->_library as $l)
        {
            switch ($l[1])
            {
                case 'css':
                    echo $this->html->css($l[0],'stylesheet',$l[2]);   
                    break;
                case 'js':
                    echo $this->javascript->link($l[0]);
                    break;
                case 'jsblock':
                    echo $this->javascript->codeBlock($l[0]);
                    break;
                case 'meta':
                    echo "<meta " . $this->_parseAttributes($l[0]) . " />";
                    break;
                case 'link':
                    echo "<link " . $this->_parseAttributes($l[0]) . " />";
                    break;
                case 'raw':
                    echo $l[0];
                    break;
                case 'cssblock':
                    echo '<style type="text/css" ' .  $this->_parseAttributes($l[2]) . " ><!--{$l[0]}--></style>";
                    break;
                default:
                    die('Internal error on HeadHelper: Unknown type registered.');
            }
        }            
    }
   
   
    /**
     * Adds the item in the array if it doesn't already exist
     * @param array $item Item to be added
     * @access private
     */
    function _register($item)
    {
        if (! in_array($item,$this->_library))
        {
            $this->_library[]=$item;
        }                   
    }                                          
   
   
   
    /**
     * This is a copy of the same function in HtmlHelper
     * Returns a space-delimited string with items of the $options array. If a
     * key of $options array happens to be one of:
     *    + 'compact'
     *    + 'checked'
     *    + 'declare'
     *    + 'readonly'
     *    + 'disabled'
     *    + 'selected'
     *    + 'defer'
     *    + 'ismap'
     *    + 'nohref'
     *    + 'noshade'
     *    + 'nowrap'
     *    + 'multiple'
     *    + 'noresize'
     *
     * And its value is one of:
     *    + 1
     *    + true
     *    + 'true'
     *
     * Then the value will be reset to be identical with key's name.
     * If the value is not one of these 3, the parameter is not output.
     *
     * @param  array  $options      Array of options.
     * @param  array  $exclude      Array of options to be excluded.
     * @param  string $insertBefore String to be inserted before options.
     * @param  string $insertAfter  String to be inserted ater options.
     * @return string
     */
    function _parseAttributes($options, $exclude = null, $insertBefore = ' ',
    $insertAfter = null)
    {
        $minimizedAttributes = array(
        'compact',
        'checked',
        'declare',
        'readonly',
        'disabled',
        'selected',
        'defer',
        'ismap',
        'nohref',
        'noshade',
        'nowrap',
        'multiple',
        'noresize');

        if (!is_array($exclude))
        {
            $exclude = array();
        }

        if (is_array($options))
        {
            $out = array();

            foreach ($options as $key => $value)
            {
                if (!in_array($key, $exclude))
                {
                    if (in_array($key, $minimizedAttributes) && ($value === 1 ||
                    $value === true || $value === 'true' || in_array($value,
                    $minimizedAttributes)))
                    {
                        $value = $key;
                    }
                    elseif (in_array($key, $minimizedAttributes))
                    {
                        continue;
                    }
                    $out[] = "{$key}=\"{$value}\"";
                }
            }
            $out = join(' ', $out);
            return $out? $insertBefore.$out.$insertAfter: null;
        }
        else
        {
            return $options? $insertBefore.$options.$insertAfter: null;
        }
    }
    

}
          
?>