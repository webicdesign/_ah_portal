<?php
/**
 * @author atabak.h@gmail.com
 * ah_framework
 * Copyright 2010-2013 gbl group
 * atabak hosein nia production
 * _mysqli.php mysqli database connection and execute statment class
 */
class _ah_mysqli
{
    private $con;
    private $res;
    private $count;
    private $row;
    private $error;

    private function connect (){
        if(!isset($this->con) || !is_resource($this->con)){
            require AH_CFG;
            try{
                $this->con = new mysqli(
                        $_ah_config['mysqli_host'],
                        $_ah_config['mysql_user'],
                        $_ah_config['mysql_pass'],
                        $_ah_config['mysql_db']
                );
                $this->con->set_charset("utf8");
                return TRUE;
            }catch (Exception $e){
                $this->error = $e->getMessage();
                return FALSE;
            }
        }
        return TRUE;
    }

    private function close (){
        if (isset($this->con) && is_resource($this->con)) {
            mysqli_close($this->con);
            $this->con = NULL;
            unset($this->con);
        }
        return TRUE;
    }

    public function civ ($var){
    	if(is_numeric($var)){
    		$string = $var;
    	}else{
	        if(get_magic_quotes_gpc()){
	            $var = stripslashes($var);
	        }
	        if(!isset($this->con)){
	        	self::connect();
	        	$string = $this->con->real_escape_string($var);
	        	self::close();
	        }else{
	        	$string = $this->con->real_escape_string($var);
	        }
    	}
        return $string;
    }

	private function free(){
    	if(self::_count()){
		    $this->res->free();
		    self::close();
    	}
    	return TRUE;
	}

    public function field_scape($fields){
        if($fields[0] == '*' && !isset($fields[1])){
            return '*';
        }else{
        	$fld = '';
        	foreach ($fields as $field){
        		$fld .= " `".str_replace(".","`.`",self::civ($field))."`, ";
        	}
        	return substr($fld,0,-2);
        }
    }

    public function where_scape($where){
    	$whr = '';
    	foreach ($where as $wr){
    		$whr .= ' '.trim($wr[0]).' ';
    		$whr .= strlen($wr[1]) ? " `".str_replace(".","`.`",self::civ($wr[1]))."` ":"";
    		$whr .= ' '.trim($wr[2]).' ';
    		$whr .= strlen($wr[3]) ? " '".str_replace(",","','",self::civ($wr[3]))."' ":'';
    		$whr .= ' '.trim($wr[4]).' ';
    	}
    	return $whr;
    }

    private function query_exec($query){
    	return $this->res = $this->con->query($query);
    }

    public function select($field, $table, $where = NULL, $order = NULL, $ordertype = NULL, $limit1 = NULL, $limit2 = NULL){
    	self::connect();
    	$query = "SELECT ".self::field_scape($field)." FROM ".$table;
		if(isset($where)){
		    $query .= ' WHERE '.self::where_scape($where);
		}
		if(isset($order) && isset($ordertype)){
		    $query .= ' ORDER BY '.self::civ($order).' '.$ordertype;
		}
		if(isset($order) && !isset($ordertype)){
		    $query .= ' ORDER BY '.self::civ($order).' ';
		}
		if(isset($limit1) && isset($limit2)){
		    $query .= ' LIMIT '.(int)$limit1.', '.(int)$limit2;
		}
		if(isset($limit1) && !isset($limit2)){
		    $query .= ' LIMIT 0, '.(int)$limit1;
		}
		$this->res = self::query_exec($query);
		self::close();
		return $this->res;
    }

	public function insert($table, $fields, $values){
		self::connect();
		$value = '';
		foreach ($values as $val){
			$value .= '(';
			$flds = '';
			foreach ($val as $fld){
				$flds .= "'".self::civ($fld)."', ";
			}
			$value .= substr($flds,0,-2).'),';
		}
		$value =  substr($value,0,-1);
	    $query = 'INSERT INTO '.$table.' ( '.self::field_scape($fields).' ) VALUES '.$value.' ';
	    self::query_exec($query);
	    return $this->con->insert_id;
	    self::close();
	}

	public function delete($table, $where = NULL){
		self::connect();
		$query = 'DELETE FROM '.$table;
		if(isset($where)){
			$query .= ' WHERE '.self::where_scape($where);
		}
		self::query_exec($query);
		$result = $this->con->affected_rows;
		$this->close();
		return $result;
	}

	public function update($table, $fieldval, $where){
		self::connect();
		$fld = '';
		foreach ($fieldval as $val){
			$fld .= $val[0]." = '".$val[1]."', ";
		}
		$fld =  substr($fld, 0,-2);
		$query = ('UPDATE '.$table.' SET '.$fld.' WHERE '.self::where_scape($where));
		self::query_exec($query);
		$this->close();
	}

	public function direct($query){
		self::connect();
		$this->res = self::query_exec($query);
		self::close();
		if(!is_resource($this->res)){
		    return FALSE;
		}else{
		    return $this->res;
		}

	}

	public function result ($select, $table, $where = NULL, $order = NULL, $ordertype = NULL, $limit1 = NULL, $limit2 = NULL){
		self::select($select, $table, $where, $order, $ordertype, $limit1, $limit2);
		if($count = self::_count()){
			if($count>1){
				$this->row = array();
				while($r = $this->res->fetch_row()){
					$this->row[] = $r;
				}
				self::free();
				return $this->row;
			}else{
				$r = $this->res->fetch_row();
				self::free();
				return $r;
			}
		}else{
			self::free();
			self::close();
			return FALSE;
		}
	}

	private function _count(){
		return isset($this->res) && (@$this->res->num_rows) ? $this->res->num_rows : FALSE;
	}

	public function count($table, $where = NULL){
		self::connect();
		$query = 'SELECT COUNT(*) FROM '.$table;
		if(isset($where)){
			$query .= ' WHERE '.self::where_scape($where);
		}
		$this->res = self::query_exec($query);
		$this->row = self::_count() ? $this->res->fetch_row() : NULL ;
		self::free();
		self::close();
		return $this->row[0];
	}

	public function show_table(){
		self::connect();
		$this->res = NULL;
		$this->res = self::direct('SHOW TABLES');
		$this->row = array();
		while($row = $this->res->fetch_row()){
			$this->row[] = $row[0];
		}
		self::free();
		self::close();
		return $this->row;
	}

	public function _insert_id(){
		return $this->con->insert_id;
	}

	public function _affected_rows(){
	    return $this->con->affected_rows;
	}
	public function backup_tables($tables = '*'){
		echo '
			<div class="fr fw tc" style="height:30px" id="dbb">
				<img src="/images/wait.gif">
			</div>';
		$tables = ($tables == '*') ? self::show_table() : (is_array($tables) ? $tables : explode(',',$tables));
		foreach($tables as $table){
			$result = self::direct('SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(self::direct('SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			for ($i = 0; $i < $num_fields; $i++){
				while($row = mysqli_fetch_row($result)){
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j<$num_fields; $j++){
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						$return .= (isset($row[$j])) ? '"'.$row[$j].'"' : '""' ;
						if ($j<($num_fields-1)){
							$return.= ',';
						}
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		$filename = 'temp/db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
		$handle = fopen($filename,'w+');
		fwrite($handle,$return);
		fclose($handle);
		echo 'فایل با موفقیت ایجاد شد';
		echo '<br/>';
		include AH_CFG;
		echo $_ah_web_site_adress.'/'.$filename;
		echo '<script>$(function(){$("#dbb").html("")});</script>';
	}
}