<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* A base controller for CodeIgniter 
*
* @link http://github.com/alzalabany
* @copyright Copyright (c) 2014, alzalabany@gmail.com
*/

class MY_Controller extends CI_Controller
{

#Sections inside your base template.
protected	$body='base/body',
			$title='Codeigniter Z master',//txt
			$js=array(),//filename
			$inline_js='',//script
			$css=array(),
			$inline_css='',//style
			$breadcrumb=FALSE,//<li><a>
			$content=array(),//html
			$noEcho = FALSE,
			$onlogout='auth',	#NOT USED
			$onSuccess=null;	#NOT USED

public function __construct()
{
	parent::__construct();
	
	#DEFAULTS
	$this->_out($this->router->fetch_class(),'page_title');#set section page_title = current class
	$this->_asset('jquery-2.0.3.min.js');
}
public function _set($var,$value){if(isset($this->$var))$this->$var = $value;return $this;}
public function logout(){$this->session->sess_destroy();redirect();}


//AUTH
///I like to use auth check and login and logout in my basecontroller; yet i removed it for this repo
//END AUTH

##TEMPLATE SETTERS
function _title($txt){$this->title=$txt;return $this;}
function _out($txt='',$section='content'){
		 	if(!isset($this->content[$section]))$this->content[$section]=[];
			$this->content[$section][]=$txt;return $this;
}
function _outv($view,$data=[],$section='content'){
	if(!isset($this->content[$section]))$this->content[$section]=[];
	$this->content[$section][]=$this->load->view($view,$data,TRUE);return $this;
}
function _asset($link,$txt=FALSE){
	
	if($txt !== FALSE){
		if($txt == 'js')$this->inline_js[]=$txt;
		elseif($txt == 'css')$this->inline_css[]=$txt;
		return $this;
	}else{
		if(pathinfo($link,PATHINFO_EXTENSION) == 'css')
			$this->css[]=link_tag(base_url('assets/css/'.trim($link,"/\\"))); #css()/js() is @ helpers/html_helper
		else 
			$this->js[]='<script src="'.base_url('assets/js/'.trim($link,"/\\")).'"></script>';
	}
	return $this;
}
function _bread($li,$html=FALSE){
	//hide breadcrumb
	if( $li == FALSE ){$this->breadcrumb=FALSE;return $this;}
	
	if($html){$this->breadcrumb[]=$li;}else{ 
		if( is_array($li) )foreach($li as $l)$this->breadcrumb[]='<li>'.$l.'</li>'; else $this->breadcrumb[]='<li>'.$li.'</li>';
	}
		
	return $this;
}
##END TEMPLATE SETTERS

#flush output
function _flush($protocol='html'){
	if($protocol == 'json')
		return $this->output->set_content_type('application/json')
					->set_output(json_encode($this->content[0]));

	$data['title']=$this->title;
	
	$data['css']		=is_array($this->css)	?implode("\n",$this->css)	:	'NO CSS';
	$data['js']			=is_array($this->js)	?implode("\n",$this->js)	:	'NO JS';
	$data['inline_css']	=($this->inline_css)	? '<style>'.implode("\n",$this->inline_css).'</style>'	:	'';
	$data['inline_js']	=($this->inline_js)	? implode("\n",$this->inline_js)	:	'';
	
	$data['breadcrumb']	=is_array($this->breadcrumb)?implode("\n",$this->breadcrumb)."\n"	:	$this->breadcrumb;
	
	foreach($this->content as $section=>$content){
		$data[$section]	= is_array($content) ? implode("\n\n\n ",$content)	:	$content;
	}
	
	if($this->noEcho)ob_clean();
	
	if($this->body){
		$this->load->view($this->body,$data);
	}else{
		#debuging data sent to view
		echo '<pre>'.html_escape(print_r($data,TRUE))."</pre>";#@todo remove later..
	}
	
}

}
/*
End my controller
*/
