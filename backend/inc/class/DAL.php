<?php

class DbConnection{

	protected static $connection;

	public function __contruct(){}

	//function that return the connection with the DB
	private function connect(){
		if(!isset($connection)){
			$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}

		if($connection === false){
			return mysqli_connect_error;
		}

		return $connection;
	}

	//generic query
	public function db_query($sql){
		$connection = $this->connect();

		$result = mysqli_query($connection, $sql);
		return $result;
	}

	//query for insert and return the id of the last insert
	public function db_insert($sql){
		$connection = $this->connect();

		$result = mysqli_query($connection, $sql);

		return $connection->insert_id;
	}

	//returned error db
	public function db_error(){
		$connection = $this->connect();

		return mysqli_error($connection);
	}

	//return the data array from db after a select query
	public function db_select($sql){
		$connection = $this->connect();
		$result = $this->db_query($sql);

		//if the query failed, return false
		if($result === false){
			return false;
		} else {
			//otherwise, fetch the result and return an array with the data
			while($row = mysqli_fetch_assoc($result)){
				$rows[] = $row;
			}

			return $rows;
		}
	}

	//Check the input strings before pass on the db
	public function db_quote($val){
		$connection = $this->connect();

		return "'" .mysqli_real_escape_string($connection, $val). "'";
	}

  	public function showAll($html){
		$queryShowAll = 'SELECT SUM(TIMESTAMPDIFF(SECOND, time_start, time_end)) AS total, project.name, project.id FROM times INNER JOIN project ON project.id = times.project GROUP BY times.project';
  		$resultShowAll = $this -> db_select($queryShowAll);
		$stringToShow='';

		if($resultShowAll === false) {
			$error = $this -> db_error();
			$stringToShow = DB_ERROR .' '. $error;
		} else {

			foreach ($resultShowAll as $row){
				$stringToShow = $stringToShow .$row['name'] .': '. gmdate('H:i:s',$row['total']);

 				preg_match('/\{\#WHILE_THERE_ARE_PROJECTS\#\}(.*?)\{\#END_WHILE_THERE_ARE_PROJECTS\#\}/s', $html, $htmlLinkArray);

  				$htmlLink = str_replace('{#ID_PROJECT#}', $row['id'], $htmlLinkArray[1]);
  				$stringToShow = $stringToShow . $htmlLink;
			}
		}
		return $stringToShow;
	}

	public function showDetail($id){
    	$queryShowDetails = 'SELECT TIMESTAMPDIFF(SECOND, time_start, time_end) AS time, time_start, name FROM times INNER JOIN project ON times.project ='.$id.' AND times.project = project.id';
    	$resultShowDetails = $this -> db_select($queryShowDetails);
    	$stringToShow='';

  		if($resultShowDetails === false) {
			$error = $this -> db_error();
			$stringToShow = DB_ERROR .' '. $error;
		} else {
    		$stringToShow = $stringToShow . $resultShowDetails[0]['name'] . '<br />';

			foreach ($resultShowDetails as $row){
  				$dateStart = date_create($row['time_start']);
  				$stringToShow = $stringToShow .date_format($dateStart, 'm-d-Y') . ' = '. gmdate('H:i:s', $row['time']) .'<br />';
			}
		}
		return $stringToShow;
	}

  	public function showTotalTime($id){
		$queryTotal = 'SELECT SUM(TIMESTAMPDIFF(SECOND, time_start, time_end)) AS total FROM times WHERE project = '.$id;
      	$resultTotal = $this -> db_select($queryTotal);

      	if($resultTotal === false) {
			return false;
		} else {
			return $resultTime[0]['total'];
        }
    }
}
?>