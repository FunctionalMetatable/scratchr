<?php
class ScratchComponent extends Object
{
    /**
	* Helper for setting up auto html links for urls in comments
	**/
    function linkify($content) {
        $pattern = '/\b(https?:\/\/)?(www.)?[A-Z0-9.]*('
                    . WHITELISTED_URL_PATTERN
                    . ')[-A-Z0-9+&@#()\/%?=~_|!:,.;]*/i';

        return preg_replace_callback(
                $pattern, array( &$this,'linkify_cb'),
                $content
            );
    }

    /*
     * callback function for linkify's preg_replace
     */
    function linkify_cb($matches) {
        $url = $text = $matches[0];
        $url_texts = array(
            TOPLEVEL_URL.'/projects' => ___('link to project', true),
            TOPLEVEL_URL.'/galleries' => ___('link to gallery', true),
            TOPLEVEL_URL.'/forums' => ___('link to forum', true),
        );
        foreach($url_texts as $u => $t) {
            if(strpos($url, $u) !== false) {
                $text = '('.$t.')';
                break;
            }
        }
        if(strpos($url, "http://") !== 0) { $url = "http://" . $url; }

        return "<a href=\"{$url}\">{$text}</a>";
    }
}

?>
