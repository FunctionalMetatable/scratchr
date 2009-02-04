<?php
class RedirectController extends Controller {
var $components = array('Cookie');
var $uses =array();

function about(){
	$lang_array =array();
	$cookie_lang = $this->Cookie->read(lang);
	$pos=strpos($cookie_lang,'-');
	if($pos===false){
		$lang = $cookie_lang;
	}
	else
	{
	 	$lang_array =explode('-',$cookie_lang);
	 	$lang = $lang_array['0'];
	
	}
	if($lang=='en')
	$this->redirect('http://info.scratch.mit.edu/About_Scratch');
	else
	$this->redirect('http://info.scratch.mit.edu/About_Scratch/'.$lang);
}//about fun


function support(){
	$lang_array =array();
	$cookie_lang = $this->Cookie->read(lang);
	$pos=strpos($cookie_lang,'-');
	if($pos===false){
		$lang = $cookie_lang;
	}
	else
	{
	 	$lang_array =explode('-',$cookie_lang);
	 	$lang = $lang_array['0'];
	
	}
	if($lang =='en')
	$this->redirect('http://info.scratch.mit.edu/Support');
	else
	$this->redirect('http://info.scratch.mit.edu/Support/'.$lang);
}//about support



}//class
?>