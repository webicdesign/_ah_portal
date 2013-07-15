<?php
/**
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * _inc.php include and needed function
 */
if ( ! defined('AH_ROOT')) exit('No direct script access allowed');

# current persian time
function _ah_time() {
	include_once AH_CLASS.'_pd.php';
	$pd = new _ah_pd();
	$return  = $pd->current_time();
	$pd = NULL;
	return $return;
}

# change time to real or number
function _ah_change_time($time) {
	switch(strlen($time)) {
	    case 6:
	    	return substr($time, 0,2).':'.substr($time, 2,2).':'.substr($time, 4,2);
	    	break;
	    case 5:
	    	return '0'.substr($time, 0,1).':'.substr($time, 1,2).':'.substr($time, 3,2);
	    	break;
	    case 8:
	    	return (substr($time, 0,2)*10000)+(substr($time, 3,2)*100)+substr($time, 6,2);
	    	break;
	}
}

# return current persian date as number
function _ah_date() {
	include_once AH_CLASS.'_pd.php';
	$pd = new _ah_pd();
	$return  = $pd->current_date();
	$pd = NULL;
	return $return;
}

# return $day before current day in persian
function _ah_day_begore($day) {
	$timeBefore = $day == 1 ? time() : time() - (($day-1) * 24 * 60 * 60);
	list($year, $month, $day) = explode("-", date("Y-m-d", $timeBefore));
	include_once AH_CLASS.'_pd.php';
	$pd = new _ah_pd();
	$date = $pd->gregorian_to_jalali( $year, $month, $day );
	if(strlen($date[1]) == 1) {
		$date[1] = '0'.$date[1];
	}
	if(strlen($date[2]) == 1) {
		$date[2] = '0'.$date[2];
	}
	$pd = NULL;
	return substr($date[0],2,4).$date[1].$date[2];
}

# change date format add|remove /
function _ah_change_date($date) {
	switch (strlen($date)) {
	    case 10:
	    	return substr($date, 0,4).substr($date, 5,2).substr($date, 8,2);
	    	break;
	    case 8:
	    	if(substr($date,0,5) == '/') {
	    		$ar = explode("/", $date);
	    		if(strlen($ar[1]) == 1) {
	    			$m = '0'.$ar[1];
	    		}
	    		if(strlen($ar[2]) == 1) {
	    			$d = '0'.$ar[2];
	    		}
	    		return ($ar[0].$m.$d);
	    	}
	    	else {
	    		return substr($date,0,4).'/'.substr($date,4,2).'/'.substr($date,6,2);
	    	}
	    	break;
	    case 6:
	    	return '13'.substr($date,0,2).'/'.substr($date,2,2).'/'.substr($date,4,2);
	    	break;
	    default:
	    	return $date;
	    	break;
	}
}

# convert number persian | english
function _ah_c_n($str, $mod = TRUE) {
	$num_a = array('0','1','2','3','4','5','6','7','8','9');
	$key_a = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
	return ($mod=='fa') ? str_ireplace($num_a, $key_a, $str) : str_ireplace($key_a, $num_a, $str);
}

# change string lenght
function _ah_lenght($string, $length, $start=0) {
	return mb_strlen($string) > $length ? mb_substr($string, $start, $length, 'UTF-8').'...' : $string;
}

# return number in string
function _ah_st2num($string) {
	return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
}

# return html from var
function _ah_hr($var) {
	return strtolower(trim(strip_tags($var)));
}

# redirect to 404.html page
function _ah_404($msg = '') {
	$_ah_404_msg = $msg;
	include AH_MOD.'404/index.php';
	exit();
}

# return user ip
function _ah_user_ip() {
	return isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];
}

# return user id
function _ah_user_id() {
	if(_user_define()) {
		return $_SESSION['_ah_user'];
	}
	return 0;
}

# return capcha validation
function _ah_capcha_valid($number) {
	return $number == $_SESSION['_ah_capcha'] ? TRUE : FALSE;
}

# redirect page
function _ah_rdr($url, $permanent = false, &$type = 'header') {
	# check url
	if($type == 'header') {
		if($permanent) {
			header('HTTP/1.1 301 Moved Permanently');
		}
		header('Location: '.$url);
		exit();
	}else{
		echo "<meta http-equiv='refresh' content='0;URL=$url'>";
	}
}

# reload current page
function _ah_reload() {
	_ah_rdr($_SERVER['REQUEST_URI']);
}

# return flag image
function _ah_get_flag($ip) {
	$flag = _ah_ip2num($ip);
	if ($flag) {
		return '<img src="/images/flag/'.$flag.'.gif">';
	}
}

# get country code from ip
function _ah_ip2num($ip) {
	$rt = htmlspecialchars($ip, ENT_QUOTES);
	$pp = sprintf("%u",ip2long($rt));
	include AH_DB;
	$con = new _ah_mysqli();
	$res = $con->result(array('_c_c'), '_ah_portal_ip',array(array('',$pp,'BETWEEN','_b_ip AND _e_ip','')));
	$con = NULL;
	if($res) {
		return $res[0];
	}
	return FALSE;
}

# image name for save
function imagename($name, $type = NULL) {
	return $type ? $name : (time()+rand(0, 10000)).".".pathinfo($name, PATHINFO_EXTENSION);
}

# user agant
function _ah_agent() {
	return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
}

# microtime
function _ah_microtime($e = 10) {
	list($u, $s) = explode(' ',microtime());
	return bcadd($u, $s, $e);
}

# microtome complate for security
function _ah_full_time() {
	$dd = explode(' ',microtime());
	$dddd = $dd[1].$dd[0];
	return str_replace('.', '', $dddd);
}

# memory usage
function _ah_memory_use() {
	return memory_get_usage(TRUE);
}

# return wirepoole hash for string
function _ah_hash($var = '_as_portal') {
	return hash('whirlpool', $var);
}

# encrypt value
function _ah_encrypt($value, $secret = '_ah_p') {
	include_once AH_CLASS.'_crypt.php';
	$converter = new _ah_encrypt();
	$encode = $converter->encode($value, $secret);
	$converter = NULL;
	return $encode;
}

# compaire forget code
function _ah_decrypt($value, $secret = '_ah_p') {
	include_once AH_CLASS.'_crypt.php';
	$converter = new _ah_encrypt();
	$decode = $converter->decode($value, $secret);
	$converter = NULL;
	return $decode;
}

# _ah Http status discription
function _ah_http_status_desc($code) {
	$code_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended'
	);
	return isset($code_to_desc[$code]) ? $code_to_desc[$code] : 'unknown request';
}

# _ah Checks string is valid UTF-8
function _ah_validate_utf8($text) {
	if(strlen($text) == 0) {
		return TRUE;
	}
	return preg_match('/^./us', $text) == 1;
}

# check for submit form
function _ah_form_submit_detect() {
	return (count($_POST) || count($_GET)) ? TRUE : FALSE;
}

# article adress create
function _article_adress($id,$titr) {
	include AH_CFG;
	return '/'.$_ah_default_lang.'/news/'.$id.'/'.$titr.'.html';
}

# random string
function _ah_r_s($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

# module adress
function _module_adress($mod, $type) {
	include AH_CFG;
	$adress  = $_SESSION['_ah_perfix'] ? '/'.$_SESSION['_ah_perfix'].'/' : '/';
	$adress .= $type ? $_ah_cfg_user_area_adress.'/'.$mod.'/' : $mod.'/';
	return $adress;
}

# user area adress
function _user_area_adress() {
	include AH_CFG;
	$adress  = $_SESSION['_ah_perfix'] ? '/'.$_SESSION['_ah_perfix'].'/' : '/';
	$adress .= $_ah_cfg_user_area_adress.'/';
	return $adress;
}

# lower and clean string
function _ah_tl($str) {
	return strtolower(trim($str));
}

# return file extention
function _ah_get_file_ext($file) {
	return pathinfo($file, PATHINFO_EXTENSION);
}

# _ah File type by ext
function _ah_file_ext_type($file) {
	$file = _ah_get_file_ext($file);
	$ext2type = array(
			'audio'       	=> array('aac', 'ac3', 'aif', 'aiff', 'm3a', 'm4a', 'm4b', 'mka', 'mp1', 'mp2', 'mp3', 'ogg', 'oga', 'ram', 'wav', 'wma'),
			'video'       	=> array('asf', 'avi', 'divx', 'dv', 'flv', 'm4v', 'mkv', 'mov', 'mp4', 'mpeg', 'mpg', 'mpv', 'ogm', 'ogv', 'qt',  'rm', 'vob', 'wmv'),
			'document'    	=> array('doc', 'docx', 'docm', 'dotm', 'odt', 'pages', 'pdf', 'rtf', 'wp', 'wpd'),
			'spreadsheet' 	=> array('numbers', 'ods', 'xls',  'xlsx', 'xlsb', 'xlsm'),
			'interactive' 	=> array('key', 'ppt', 'pptx', 'pptm', 'odp', 'swf'),
			'text'        	=> array('asc', 'csv', 'tsv', 'txt'),
			'archive'     	=> array('bz2', 'cab', 'dmg', 'gz', 'rar', 'sea', 'sit', 'sqx', 'tar', 'tgz', 'zip'),
			'code'        	=> array('css', 'htm', 'html', 'php', 'js'),
			'image'			=> array('jpg', 'png', 'bmp', 'gif', 'ico')
	);
	foreach ($ext2type as $type => $exts) {
		if (in_array($file, $exts)) {
			return $type;
		}
	}
}

# portal first page adress
function _portal_first_page () {

	# return adress by language perfix
	return isset($_SESSION['_ah_perfix']) ? '/'.$_SESSION['_ah_perfix'].'/index.html' : '/index.html';
}

# set block list user
function _block_list_add ($reason) {

}