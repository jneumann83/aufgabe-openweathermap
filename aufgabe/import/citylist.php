<?php
namespace Citylist;

set_time_limit(600);

include	'../includes/dbconnection.php';

class Citylist {
	
	private $DB;
	
	public function __construct()
    {
		$this->DB	=	new \Database\Database();
    }
	
	public function importCitylist() {
		$data = file_get_contents("http://openweathermap.org/help/city_list.txt");
		$rows = explode("\n",$data);
		$this->saveData($rows);
	}
	
	private function saveData($data) {
		if(!empty($data)) {
			$header = ['id', 'name', 'latitude', 'longitude', 'countryCode'];
			$value = [];
			for ($i = 0; $i < count($header); $i++) {
				$value[] = '?';
			}

			$uvalue = [];
			foreach($header as $c) {
				$uvalue[] = '`' . $c . '`' . '=VALUES(`'.$c.'`)';
			}

			$insertTemplate = 'INSERT INTO `aufgabe`.`citys` (`' . implode('`,`', $header) . '`) VALUES (' . implode(',', $value) . ') ON DUPLICATE KEY UPDATE '.implode(',', $uvalue);
			$this->DB->query('BEGIN');
			$i = 1;
			foreach($data as $row) {
				if(empty($row)) {
					$i++;
					continue;
				}
				$s = str_getcsv($row, "\t");
				if($i === 1) {
					$i++;
					continue;
				}
				$success = $this->DB->query($insertTemplate, $s);
				
				if (!$success['status']) {
					throw new \Exception('DB-Fehler');
				}
				
				$i++;
				
				if ($i % 1000 == 0) {
					$this->DB->query('COMMIT');
					$this->DB->query('BEGIN');
				}
			}
			$this->DB->query('COMMIT');
		}
	}
}

$cityList	=	new \Citylist\Citylist();
$cityList->importCitylist();
?>