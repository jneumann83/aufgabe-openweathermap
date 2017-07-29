<?php

  namespace Database;
  
  class Database {
	  
	 public $db;
     private $connection = NULL;
 	 private $server	=	'localhost';
 	 private $user		=	'root';
 	 private $password	=	'';
 	 private $new		=	FALSE;
 	 private $database	=	'aufgabe';
	 
	public function __construct()
    {
		$this->database();
    } 
     
    public function database() {
 
       $this->connection = mysqli_connect($this->server, $this->user, $this->password, $this->new)
         or die("Verbindung zum Server fehlgeschlagen!");
 
       $this->db	=	mysqli_select_db($this->connection, $this->database)
         or die("Verbindung zur Datenbank fehlgeschlagen!");
 
    }
 
    public function disconnect() {
 
       mysql_close( $this->connection )
         or die("Verbindung konnte nicht geschlossen werden!");
 
    }
	
	public function querySelect($query, $params=null) {
		$query = $this->prepareParams( $query, $params );
		$result = mysqli_query($this->connection, $query, MYSQLI_STORE_RESULT);
		$success = false;
		$data = [];
		if($result) {
			$data = mysqli_fetch_all($result,MYSQLI_ASSOC);
			$success = true;
		}
		mysqli_free_result($result);
		return ['status' => $success, 'query' => $query, 'result' => $data];
    }
	
	public function query($query, $params=null) {
		$query = $this->prepareParams( $query, $params );
		$result = mysqli_query($this->connection, $query);
		$success = false;
		$insertId = null;
		if($result) {
			$success = true;
			$insertId = mysqli_insert_id($this->connection);
		}
		return ['status' => $success, 'query' => $query, 'data' => $insertId];
    }
	
	private function prepareParams($query, $params) {
		if( $params !== null ){
			$params = (array) $params;
			
			if( !empty($params) ) {
				$queryParts = explode('?', $query);
				$query = '';

				foreach($params as $p) {
					$part = array_shift($queryParts);
					if(!$part) {
						die('Too few placeholders for query.');
						break;
					}
					if(!is_array($p)) {
						if(!is_null($p)) {
							$query .=  $part . '"'.mysqli_real_escape_string($this->connection, $p).'"';
						} else {
							$query .=  $part . 'NULL';
						}
					} else {
						$query .= $part . $this->escapeArray($p);
					}
				}
				$query .= array_shift($queryParts);
			}
		}
		return $query;
	}
	
	private function escapeArray($array) {
		foreach($array as &$p) {
			$p = '"' . $this->real_escape_string($p) . '"';
		}
		return implode(',', $array);
	}
}