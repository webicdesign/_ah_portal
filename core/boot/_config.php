<?php
/*
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 * portal configration file
 * no direct access
 */

if (!defined('AH_ROOT')) exit('No direct script access allowed');

$_ah_config = array(
        # install portal check
        'portal_install'		=>TRUE,							# install or not install

        # mysqli configration
        'mysqli_host'          	=> 'localhost',					# database server adress
        'mysql_user'           	=> 'root',						# database username[root]
        'mysql_pass'           	=> '',							# database password
        'mysql_db'             	=> '',							# database name

        # session configration
        'session_name' 		  	=> '_as_p',
        'session_time_out'    	=> 7200,						# sec
        'session_store'       	=> 'db', 					    # db or cookie
        'session_table' 	  	=> '_ah_portal_session',		# session table name in database
        'session_path' 		  	=> '/temp/session/',			# session path for save session [not recommend]
        'session_sec_code' 	  	=> '_ah_p',						# session security code for save to database
        'session_lock_time'   	=> 30,							# lock time out for ajax request

        # tracking configration
        'track' 			  	=> TRUE, 					    # TRUE or FALSE
        'track_store' 		  	=> 'file', 						# file or db
        'track_host' 		  	=> '', 							# empty for current host
        'track_table' 		  	=> '_ah_portal_track',			# track table name
        'track_user' 		  	=> '', 							# empty for current user
        'track_pass' 		  	=> '', 							# empty for current pass
        'track_db' 			  	=> '', 							# empty for current db
        'track_file' 		  	=> '',							# full path and file name

        # error configration
        'error_report' 		  	=> -1,							# 1 => show[developer] | 0 => hide[use]
        'error_file'		  	=> '',							# set if want save to file [full path with file name]

        # theme configration
        'theme_default' 	  	=> 'default',				    # theme folder name

        # language configration
        'multi_lang' 		  	=> TRUE,					    # TRUE[multi lang and change adress] or FALSE[one lang without adress]
        'default_lang' 		  	=> 'fa',						# default language

        # offline or online
        'offline' 			  	=> FALSE,

        # adress configration
        'user_area' 		  	=> TRUE,						# if portal need user area adress
        'user_area_adress' 	  	=> 'user',						# user area adress perfix
        'only_user_area' 	  	=> FALSE,						# only user area for protected application only

        # first title
        'first_title'      	  	=> 'first page title',			# portal first page title
        'user_first_title'    	=> 'user page title',			# portal user page tile

        # script confgi
        'portal_host_name'    	=> 'http://',					# web site name for cookie, session and other fethure

		# adress configration
		'first_page'			=> array(						# first page name [recommend not change]
										'',
										'index.html',
										'index.php',
										'main.html',
										'main.php'
									),
		# ajax configration
		'ajax_var'				=> 'ajax'						# change it when you need

);




