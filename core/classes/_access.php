<?php
/*
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 * module access list for user and guest
 * no direct access
 */

class _ah_access
{
    public  $module;
    private $link;
    public  $_ah_error;
    public  $_ajax_inc;
    public  $_ah_mod;

    # first request
    function _ah_access() {
        if (!isset($this->module) && !is_array($this->module)) {
        	//echo 'get module from database<br/>';
            $this->module = array();
            self::_module_list();
        }
    }

    # module access by url [only current request module]
    public function _module_access_check($mod, $type) {
        foreach ($this->module as $md) {
            if ($md['uri'] == $mod && $md['type'] == $type) {
                $this->_ah_mod['file']  = AH_MOD.$md['folder'].'/'.$md['first_file'];
                $this->_ah_mod['page']  = $md['page_type'];
                $this->_ah_mod['title'] = $md['name'];
                return TRUE;
            }
        }
        $this->_ah_error['type'] 	= 'mod';
        $this->_ah_error['problem'] = 'no access to module';
        return FALSE;
    }

    # ajax access check
    public function _ajax_access_check($ajax) {
        if ($ajax) {
            foreach ($this->module as $mod) {
                if ($mod['ajax_var'] == $ajax['ajax'] && $mod['ajax_type'] == $ajax['type']) {
                    # check for file exist
                    $this->_ajax_inc = AH_MOD.$mod['folder'].'/'.$mod['ajax_file'];
                    return TRUE;
                }
            }
        }
        else {
            $this->_ah_error['type'] 	= 'ajax';
            $this->_ah_error['problem'] = 'not portal standard sending';
            return FALSE;
        }
    }

    # create user module array
    private function _module_list() {

    	# create database object
        self::_connect();

        # define counter
        $counter = 0;

        # check for user access
        if (_user_define()) {

            $res = $this->link->result(
            		array('_ah_portal_mod._uri', '_ah_portal_mod._folder', '_ah_portal_mod._first_file', '_ah_portal_mod._page_type', '_ah_portal_mod._ajax_file', '_ah_portal_mod._ajax_var', '_ah_portal_mod._ajax_type', '_ah_portal_mod._name', '_ah_portal_mod._image', '_ah_portal_mod._color'),
            		'_ah_portal_mod_access INNER JOIN _ah_portal_mod ON _ah_portal_mod_access._mod_id = _ah_portal_mod._id',
            		array(array('', '_ah_portal_mod._active', '=', 1, 'AND'), array('', '_ah_portal_mod._type', '=', 1, 'AND'), array('', '_ah_portal_mod._access', '=', 1, 'AND'), array('', '_ah_portal_mod_access._user_id',	'=', $_SESSION['_ah_user'], ''))
            );

            # define user access list as array
            if ($res) {
                foreach ($res as $row) {
                    $this->module[$counter]['uri']          = $row[0];
                    $this->module[$counter]['folder']       = $row[1];
                    $this->module[$counter]['first_file']   = $row[2];
                    $this->module[$counter]['page_type']    = $row[3];
                    $this->module[$counter]['ajax_file']    = $row[4];
                    $this->module[$counter]['ajax_var']     = $row[5];
                    $this->module[$counter]['ajax_type']    = $row[6];
                    $this->module[$counter]['name']         = $row[7];
                    $this->module[$counter]['image']        = $row[8];
                    $this->module[$counter]['color']        = $row[9];
                    $this->module[$counter]['type']        	= 1;
                    $counter++;
                }
            }
            unset($res);
        }

        # guest access [for all user]

        $res = $this->link->result(
        		array('_uri', '_folder', '_first_file', '_page_type', '_ajax_file', '_ajax_var', '_ajax_type', '_name', '_image', '_color'),
        		'_ah_portal_mod',
        		array(array('', '_active', '=', 1, 'AND'), array('', '_type', '=', 1, 'AND'), array('', '_access', '=', 0, ''))
        );

        # define guest access list as array
        if ($res) {
            foreach ($res as $row) {
                $this->module[$counter]['uri']          = $row[0];
                $this->module[$counter]['folder']       = $row[1];
                $this->module[$counter]['first_file']   = $row[2];
                $this->module[$counter]['page_type']    = $row[3];
                $this->module[$counter]['ajax_file']    = $row[4];
                $this->module[$counter]['ajax_var']     = $row[5];
                $this->module[$counter]['ajax_type']    = $row[6];
                $this->module[$counter]['name']         = $row[7];
                $this->module[$counter]['image']        = $row[8];
                $this->module[$counter]['color']        = $row[9];
                $this->module[$counter]['type']        	= 0;
				$counter++;
			}
		}
		unset($res);

		# kill database object
		self::_disconnect();
	}

	# connect to database
    private function _connect() {
        if (!isset($this->link)) {
            include_once AH_DB;
            $this->link = new _ah_mysqli();
        }
        return TRUE;
    }
    # disconnect from database
    private function _disconnect() {
        if (isset($this->link)) {
            $this->link = NULL;
            unset($this->link);
        }
        return TRUE;
    }
}