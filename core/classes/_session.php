<?php
/*
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * supported by webicdesign.net
 * support@webicdesign.net
 * session handler in db class
 * no direct access
 */

class _ah_session
{
    private $lock_timeout;
    private $security_code;
    private $session_lifetime;
    private $table_name;
    private $link;
    private $session_lock;

    function _ah_session () {
        # include configration
        include AH_CFG;

        # new php configration for session
        ini_set('session.gc_probability'            , 1                                 );
        ini_set('session.gc_divisor'                , 100                               );
        ini_set('session.gc_maxlifetime'            , $_ah_config['session_time_out']   );
        ini_set('session.referer_check'             , ''                                );
        //ini_set("session.entropy_file"              , "/dev/urandom"                    );
        //ini_set("session.entropy_length"            , "512"                             );
        ini_set('session.use_cookies'               , 1                                 );
        ini_set('session.use_only_cookies'          , 1                                 );
        //ini_set('session.use_trans_sid'             , 0                                 );
        ini_set('session.hash_function'             , 1                                 );
        ini_set('session.hash_bits_per_character'   , 5                                 );
        session_cache_limiter('nocache');
        //session_set_cookie_params(0, '/', '.'.$_ah_config['portal_host_name']);
        session_name($_ah_config['session_name']);

        # define default value
        $this->lock_timeout         = $_ah_config['session_lock_time'];
        $this->security_code        = $_ah_config['session_sec_code'];
        $this->session_lifetime     = $_ah_config['session_time_out'];
        $this->table_name           = $_ah_config['session_table'];

        # create database object
        self::_connect();

        # register new session handler
        session_set_save_handler(
                array(&$this, 'open'),
                array(&$this, 'close'),
                array(&$this, 'read'),
                array(&$this, 'write'),
                array(&$this, 'destroy'),
                array(&$this, 'gc')
        );

        # starting session
        session_start();

        # kill database onject
        self::_disconnect();

    }

    # session custom close function
    function close () {

        # create database object if not create
        self::_connect();

        $this->link->direct('SELECT RELEASE_LOCK("'.$this->session_lock.'")');

        return TRUE;
    }

    # session custom destroy function
    function destroy ($session_id) {

        # create database object if not create
        self::_connect();

        # delete current session from database table
        $res = $this->link->delete($this->table_name,array(array('','session_id','=',$session_id,'')));

        return $res !== -1 ? TRUE : FALSE;

    }

    # session custom garbage collector function
    function gc (){

        # create database object if not create
        self::_connect();

        # delete expired session from database table
        $res = $this->link->delete($this->table_name,array(array('','session_expire','<',time(),'')));

    }

    # session custom open function
    function open ($save_path, $session_name) {
        return TRUE;
    }

    # session custom read function
    function read ($session_id) {

        # create database object if not create
        self::_connect();

        # get lock name
        $this->session_lock = $this->link->civ($this->security_code.$session_id);

        # obtain a lock with name and timeout
        $res = $this->link->direct('SELECT GET_LOCK("'.$this->session_lock.'", '.$this->lock_timeout.')');

        if ($res) {
            if (@mysql_num_rows($res)) {
                unset($res);
                $res = $this->link->select(
                        array('session_data'),
                        $this->table_name,
                        array(
                                array('','session_id','=',$session_id,'AND'),
                                array('','session_expire','>',time(),'AND'),
                                array('','http_user_agent','=',self::_user_agant(),'')
                        ),NULL,NULL,1
                );
                if ($res) {
                    $fld = mysqli_fetch_assoc($res);
                    mysqli_free_result($res);
                    return $fld['session_data'];
                } else {
                    mysqli_free_result($res);
                    return '';
                }
            }
        }

    }

    # session regerate id function
    function regenerate_id () {

        # saves the old session's id
        $old_session_id = session_id();

        # regenerates the id
        session_regenerate_id();

        self::destroy($old_session_id);

    }

    # session stop function
    function stop () {

        self::regenerate_id();

        session_unset();

        session_destroy();

    }

    # session custom read function
    function write ($session_id, $session_data) {

        # create database object if not create
        self::_connect();

        $res = $this->link->direct(
                '
                INSERT INTO
                    '.$this->table_name.'
                    (session_id, http_user_agent, session_data, session_expire)
                VALUES
                    ("'.$this->link->civ($session_id).'","'.self::_user_agant().'","'.$this->link->civ($session_data).'","'.$this->link->civ(time() + $this->session_lifetime).'")
                ON DUPLICATE KEY UPDATE
                    session_data = "'.$this->link->civ($session_data).'",
                    session_expire = "'.$this->link->civ(time() + $this->session_lifetime).'"
                '
        );

        if ($res) {
            return $this->link->_affected_rows() > 1 ? TRUE : '';
        }
        else {
            return FALSE;
        }

    }

    # create custom user agant for custom session
    private function _user_agant () {
        return _ah_hash((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '').$this->security_code);
    }

    # connect to database
    private function _connect () {
        if (!isset($this->link)) {
            include_once AH_DB;
            $this->link = new _ah_mysqli();
        }
        return TRUE;
    }

    # disconnect from database
    private function _disconnect () {
        if (isset($this->link)) {
            $this->link = NULL;
            unset($this->link);
        }
        return TRUE;
    }
}