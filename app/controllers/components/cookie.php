<?php

/**
* Cookie Component
* @author RosSoft
* @license MIT
* @version 0.13
*/

class CookieComponent extends Object
{
/**
* If not null, then the cookies will be encrypted
* with this key. Change to whatever you want.
*/
//var $crypt_key="SCRATHax09823FAJF@Q@JIQ";
var $crypt_key=null;
var $crypt_engine='Mcrypt';

function startup(&$controller)
{
if ($this->crypt_key)
{
vendor('script' . DS . 'simple_crypt');
$this->crypt=& new SimpleCrypt($this->crypt_engine);
}
}

/**
* Writes a cookie
* @param string $name Name of the cookie
* @param mixed $data Data to be written
* @param string $expires A valid strtotime string: when the data expires.
* @return boolean Success
*/
function write($name,$data=NULL,$expires='+30 day')
{
$data=serialize($data);
$time=strtotime($expires);

if ($this->crypt_key)
{
$data=$this->crypt->encrypt($this->crypt_key,$data);
}

if (setcookie($name,$data,$time,"/"))
{
return true;
}
else
{
$this->log("CookieComponent: Write failed [$name][$data][$time]");
return false;
}
}

/**
* Deletes a cookie
* @param string $name Name of the cookie
* @return boolean Success
*/
function delete($name)
{
if($this->write($name,'','-999 day'))
{
return true;
}
else
{
$this->log("CookieComponent: Delete failed [$name]");
return false;
}
}

/**
* Reads a cookie
* @param string $name Name of the cookie
* @param boolean $unserialize Unserializes the content.
* Must be false if the cookie was not written through this component
*
* @return mixed Value of the cookie, or NULL if not exists
*/
function read($name,$unserialize=true)
{
if(isset($_COOKIE[$name]))
{
$string=$_COOKIE[$name];
if (get_magic_quotes_gpc())
{
$string=stripslashes($string);
}
if ($unserialize)
{
if ($this->crypt_key)
{
$string=$this->crypt->decrypt($this->crypt_key,$string);
}
$string=@unserialize($string);
return $string;
}
else
{
return $string;
}

}
else
{
return null;
}
}

/**
* Check if a cookie is set
* @param string $name Name of the cookie
* @return boolean The cookie exists
*/
function check($name)
{
return(isset($_COOKIE[$name]));
}

/**
* Removes all the cookies from the domain
* @author support at duggen dot net
*
* @link http://es.php.net/manual/en/function.setcookie.php#52081
*/
function clear()
{
$cookiesSet = array_keys($_COOKIE);
for ($x = 0; $x < count($cookiesSet); $x++)
{
if (is_array($_COOKIE[$cookiesSet[$x]]))
{
$cookiesSetA = array_keys($_COOKIE[$cookiesSet[$x]]);
for ($c = 0; $c < count($cookiesSetA); $c++)
{
$aCookie = $cookiesSet[$x]."[".$cookiesSetA[$c]."]";
$this->delete($aCookie);
}
}
$this->delete($cookiesSet[$x]);
}
}
}

?>
