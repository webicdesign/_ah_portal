<?php
/*
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 * boot and prepare portal
 * no direct access
 */

if ( ! defined('AH_ROOT')) exit('No direct script access allowed');

// define portal path

# core file base path
defined ( 'AH_CORE'		) || define( 'AH_CORE'  , AH_ROOT.'core/'						);

# boot file base path
defined ( 'AH_BOOT'		) || define( 'AH_BOOT'  , AH_CORE.'boot/'						);

# internal module folder base path [core module]
defined ( 'AH_MOD'		) || define( 'AH_MOD'   , AH_CORE.'mod/'						);

# external module folder base path
defined ( 'AH_MODULE'	) || define( 'AH_MOD'   , AH_CORE.'mod/'						);

# class file base path
defined ( 'AH_CLASS'	) || define( 'AH_CLASS' , AH_CORE.'classes/'					);

# theme folder base path
defined ( 'AH_THEME'	) || define( 'AH_THEME' , AH_ROOT.'theme/'						);

# mysqli database class path file for include
defined ( 'AH_DB'		) || define( 'AH_DB'    , AH_CLASS.'_mysqli.php'				);

# login page for normal request
defined ( 'AH_LOGIN'	) || define( 'AH_LOGIN' , AH_MOD.'login/index.php'				);

# login include file for ajax request
defined ( 'AH_LOGIN_JX'	) || define( 'AH_LOGIN' , AH_MOD.'login/index.php'				);

# labguage file base path
defined ( 'AH_LANG'		) || define( 'AH_LANG'  , AH_CORE.'lang/'						);

# php version for feture
defined ( 'AH_PHP'		) || define( 'AH_PHP'   , phpversion() 							);

# configration file path
defined ( 'AH_CFG'		) || define( 'AH_CFG'   , AH_BOOT.'/_config.php'	 			);

# temp folder path
defined ( 'AH_TMP'		) || define( 'AH_TMP'   , AH_CORE.'temp/'						);

# cache folder for cache class
defined ( 'AH_CACHE'	) || define( 'AH_CACHE' , AH_CORE.'cache/'						);

# true value for portal
defined ( 'AH_TRUE'		) || define( 'AH_TRUE'  , true 									);

# false value for portal
defined ( 'AH_FALSE'	) || define( 'AH_FALSE' , false 								);

# null value for portal
defined ( 'AH_NULL'		) || define( 'AH_NULL'  , null 									);

# include file folder adress
defined ( 'AH_INC'		) || define( 'AH_INC'   , '/inc/'								);

# security base system for starting by php version
if ( AH_PHP < '5.2.0' ) {
    set_magic_quotes_runtime(0);
}

# get portal cnfigration
include AH_CFG;

// php configration run time

# error reportin display
error_reporting(E_ALL);
ini_set ( "display_errors"                    	, $_ah_config['error_report'] 			);

# register global must be off
ini_set ( "register_globals"                  	, "Off"                   				);

# security reason
ini_set ( "register_argc_argv"                	, "Off"                   				);

# open short tag for echo short wrtite
ini_set ( "short_open_tag"                    	, "On"                    				);

# security reason
ini_set ( "magic_quotes_qpc"                  	, "On"                    				);

# max excute time for each proccess
ini_set ( "max_execution_time"                	, "600"                   				);

# memory limit by MB for each proccess
ini_set ( "memory_limit"                      	, "32M"                   				);

# post max size must not be larger then 5 MB for article or other things
# for larger post, config must change in module
ini_set ( "post_max_size"                     	, "2M"                    				);

# max upload file size not larger then 5 MB
# for larger file upload, config must change in module
ini_set ( "upload_max_filesize"               	, "5M"                   				);

# session not auto start
# other session security change in _session.php class
ini_set ( 'session.auto_start'                  , 0                                     );

# needed function include for use
require AH_BOOT.'_inc.php';

# start portal by request
function _ah_boot() {

	# get portal cnfigration
	include AH_CFG;

    # session manager
    require AH_CLASS.'_session.php';
    $_ah_portal_session = new _ah_session();

    # connect from command line
    if (cli()) {
        _ah_404('cli_error');
    }

    # connect normal
    else {

    	# portal not install
    	if (!$_ah_config['portal_install']) {

    		# install portal file
    		include AH_CLASS.'_install.php';
    	}

    	# portal install
    	else {

    		# include html class for response
    		include AH_CLASS.'_html.php';
    		$_ah_html = new _ah_html();

    		# include user access class for authorization
    		include AH_CLASS.'_access.php';
    		$_ah_access = new _ah_access();

    		//var_dump($_SESSION);

    		# check for url validation
    		if (_ah_valid_url( _current_adress() )) {

    			# security fix
    			include AH_CLASS.'_sec.php';
    			$_ah_sec = new _ah_sec();

    			# ajax request detect
    			if (_ajax_detect()) {

    				# get ajax value
    				$_ah_ajax_var = _ajax_var();

    				# right ajax value
    				if ($_ah_ajax_var) {

    					// check for ajax access

    					# access to ajax
    					if ($_ah_access->_ajax_access_check($_ah_ajax_var)) {
    						include $_ah_access->_ajax_inc;
    					}

    					# not access to ajax
    					else {
    						echo '404';
    					}
    				}

    				# wrong ajax value
    				else {
    					echo '404';
    				}
    			}

    			# normal request detect
    			else {

    				# get array from adress
    				$_ah_adress = _adress_array();

    				# define default theme
    				$_ah_default_theme = AH_THEME.$_ah_config['theme_default'].'/';

    				# first page request
    				if ((isset($_ah_adress[0]) && in_array($_ah_adress[0], $_ah_config['first_page'])) || !isset($_ah_adress[0])) {

    					# define first page title
    					$_ah_page_title = $_ah_config['first_title'];

    					# include first page file
    					include $_ah_default_theme.'main/header.php';
    					include $_ah_default_theme.'main/first.php';
    					include $_ah_default_theme.'main/footer.php';

    				}

    				# user first page
    				elseif (isset($_ah_adress[0]) && $_ah_adress[0] == $_ah_config['user_area_adress']) {

    					// check for user type [user or guest]

    					# loginned user
    					if (_user_define()) {

    						# user first page request
    						if ((isset($_ah_adress[1]) && in_array($_ah_adress[1], $_ah_config['first_page'])) || !isset($_ah_adress[1])) {

    							# define user first page title
    							$_ah_page_title = $_ah_config['user_first_title'];

    							# include user first page file
    							include $_ah_default_theme.'user/header.php';
    							include $_ah_default_theme.'user/first.php';
    							include $_ah_default_theme.'user/footer.php';
    						}

    						# user module request
    						elseif (isset($_ah_adress[1]) && !in_array($_ah_adress[1], $_ah_config['first_page'])) {

    							// ceck access module for request user

    							# user access to module
    							if ($_ah_access->_module_access_check($_ah_adress[1], 1)) {

    								# set page title
    								$_ah_page_title = $_ah_access->_ah_mod['title'];

    								// check for page type include

    								# just first file
    								if ($_ah_access->_ah_mod['page']) {
    									include $_ah_access->_ah_mod['file'];
    								}

    								# user header and footer
    								else {

    									# include user first page file
    									include $_ah_default_theme.'user/header.php';
    									include $_ah_default_theme.'user/module.php';
    									include $_ah_default_theme.'user/footer.php';
    								}
    							}

    							# user cant access to module
    							else {
    								_ah_404('user not access or wrong adress');
    							}
    						}

    					}

    					# not loggined user
    					else {
    						include AH_LOGIN;
    					}
    				}
    				else {

    					// ceck access module for request guest or user

    					# guest or user access to module
    					if ($_ah_access->_module_access_check($_ah_adress[0], 0)) {

    						# set page title
    						$_ah_page_title = $_ah_access->_ah_mod['title'];

    						// check for page type include

    						# just first file
    						if ($_ah_access->_ah_mod['page']) {
    							include $_ah_access->_ah_mod['file'];
    						}

    						# user header and footer
    						else {

    							# include user first page file
    							include $_ah_default_theme.'main/header.php';
    							include $_ah_default_theme.'main/module.php';
    							include $_ah_default_theme.'main/footer.php';
    						}
    					}

    					# guest or user not access to module
    					else {
    						_ah_404('guest not access or wrong adress');
    					}
    				}
    			}
    		}
    	}
    }
}

# plugin detect [soon]
function _plugin_detect(){

}

# get language file list
function _language_file_list() {

	# return array of files name
    $_ah_language_files = array();

    # language path and list of file
    $langfile = glob('core/lang/*.php');

    # need only file name without extention
    foreach ($langfile as $file) {
        $_ah_language_files[] = basename($file, ".php");;
    }

    # return language file array
    return $_ah_language_files;
}

# create array from current adress
function _adress_array() {

	# get current adress from _current_adress() function
    $_current_adress =  _current_adress();

    # get array from current adress for proccess
    $adress_array = explode('/', filter_var(substr($_current_adress, 1), FILTER_SANITIZE_URL));

    # include configration file for default language
    include AH_CFG;

    # session perfix for language perfix if avalible
    $_SESSION['_ah_perfix'] = NULL;

    # if portal run by multi language
    if ($_ah_config['multi_lang']) {

    	# get language file list as array
        $_ah_lang_file = _language_file_list();

        # if first adress not in language file
        if (!in_array($adress_array[0], $_ah_lang_file)) {

            $adress = '';

            foreach ($adress_array as $adrs) {

            	# empty adress must be delete
                if (!empty($adrs)) {
                    $adress .= '/'.$adrs;
                }
            }

            # redirect to right page with language perfix
            _ah_rdr('/'.$_ah_config['default_lang'].$adress);
        }

        # define language perfix now
        $_SESSION['_ah_perfix'] = $adress_array[0];

        # remove language from first adress array
        array_shift($adress_array);
    }

    # return adress array
    return $adress_array;
}

# return current adress
function _current_adress() {

	# return current adress from $_SERVER['REQUEST_URI'] or $_SERVER['SCRIPT_NAME'] and $_SERVER['QUERY_STRING'] for get value
    return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
}

# check for url is valid
function _ah_valid_url($url, $absolute = FALSE) {

	# url validation
    return $absolute ? (bool)preg_match("/^(?:ftp|https?|feed):\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*(?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:(?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?](?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi", $url) : (bool)preg_match("/^(?:[\w#!:\.\?\+=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})+$/i", $url);
}

# return ajax var
function _ajax_var() {

	# include configration file for ajax variable name
	include AH_CFG;

	$ajax_var = $_ah_config['ajax_var'];

	# portal standard ajax variable detect
    if (isset($_POST[$ajax_var]) || isset($_GET[$ajax_var])) {

    	# ajax variable from post or get
        $ajax = isset($_POST[$ajax_var]) ? $_POST[$ajax_var] : $_GET[$ajax_var];

        # ajax type post or get
        $type = isset($_POST[$ajax_var]) ? 0 : 1;

        # return array
        return array('ajax'=>$ajax,'type'=>$type);
    }

    # not portal standard ajax variable
    else {
        return FALSE;
    }
}

# ajax request detect
function _ajax_detect() {

	# ajax request detect for framework only
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? TRUE : FALSE ;
}

# check for command line connect
# command line connect check
function cli(){
	return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() === 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0))) ? TRUE : FALSE;
}

# check for user or guest [true|false]
# define user type
function _user_define() {

	# login
    if (isset($_SESSION['_ah_user']) && !isset($_SESSION['_ah_guest'])) {

    	# delete guest session
    	$_SESSION['_ah_guest'] 	= NULL;
        unset($_SESSION['_ah_guest']);

        # return true for loggined user
        return TRUE;
    }

    # guest
    else {

    	# guest re define
        $_SESSION['_ah_guest'] 	= 1;

        # delete user session again
        $_SESSION['_ah_user']  	= NULL;
        unset($_SESSION['_ah_user']);

        # return false for guest
        return FALSE;
    }
}