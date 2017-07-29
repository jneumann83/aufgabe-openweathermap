<?php
namespace Weather;

include	'./library/Cmfcmf/OpenWeatherMap/Util/Temperature.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Unit.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Wind.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Sun.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Weather.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Location.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/City.php';
include	'./library/Cmfcmf/OpenWeatherMap/Util/Time.php';
include	'./library/Cmfcmf/OpenWeatherMap/CurrentWeather.php';
include	'./library/Cmfcmf/OpenWeatherMap/Exception.php';
include	'./library/Cmfcmf/OpenWeatherMap/Forecast.php';
include	'./library/Cmfcmf/OpenWeatherMap/WeatherForecast.php';
include	'./library/Cmfcmf/OpenWeatherMap/Fetcher/FetcherInterface.php';
include	'./library/Cmfcmf/OpenWeatherMap/Fetcher/CurlFetcher.php';
include	'./library/Cmfcmf/OpenWeatherMap.php';
include	'./includes/dbconnection.php';

class Weather {

	private $lang = 'de';
	private $units = 'metric';
	private $apiKey = '6cfb4b06a0cca56d1c04f3251120972c';
	private $own;
	private $DB;
	private $humidityUnit = '%';
	private $temperatureUnit = '&deg;C';
	private $windSpeedUnit = 'm/s';
	private $forecastDays = 6;
	
	public function __construct()
    {
		$this->owm	=	new \Cmfcmf\OpenWeatherMap($this->apiKey);
		$this->DB	=	new \Database\Database();
    }
	
	public function getWeather($params) {
		$weather = $this->getLastWeather($params['id']);
		if(empty($weather)) {
			// Wetter nur live abfragen, wenn nicht vorhanden, oder älter als 10min
			$weather = $this->loadLiveWeather($params['id']);
			if($weather) {
				$this->saveWeather($weather);
			}
		}
		$weather['forecast'] = $this->getLastWeatherForecast($params['id']);
		if(empty($weather['forecast'])) {
			$data = $this->loadLiveForecastWeather($params['id']);
			if($data) {
				$weather['forecast'] = $data;
				$this->saveForecastWeather($data);
			}
		}
		return $weather;
	}
	
	public function getCity($param, $col = 'name') {
		if(!empty($param)) {
			if($col == 'id') {
				$query = 'SELECT id, name, countryCode FROM aufgabe.citys WHERE id = ? LIMIT 1';
			} else {
				$query = 'SELECT id, name, countryCode FROM aufgabe.citys WHERE LOWER(name) like ? LIMIT 5';
				$param = strtolower($param).'%';
			}
			$possibleCitys = $this->DB->querySelect($query, $param);
			if($possibleCitys['status']) {
				$citys = [];
				foreach($possibleCitys['result'] as $city) {
					$citys[] = ['id' => $city['id'], 'name' => $city['name'], 'countryCode' => $city['countryCode']];
				}
				return $possibleCitys['result'];
			}
		}
	}
	
	public function loadLiveForecastWeather($cityId) {
		if($cityId) {
			try {
				$forecast = $this->owm->getWeatherForecast($cityId, $this->units, $this->lang, '', $this->forecastDays);
			} catch(OWMException $e) {
				return false;
			} catch(\Exception $e) {
				return false;
			}
		}
		
		$forecastData = [];
		if($forecast) {
			foreach ($forecast as $weather) {
				$parsedWeather = $this->parseWeather($weather);
				if($parsedWeather && $parsedWeather['date'] > date('Y-m-d')) {
					$forecastData[] = $parsedWeather;
				}
			}
			return $forecastData;
		}
		return false;
	}
	
	public function loadLiveWeather($id) {
		try {
			$weather = $this->owm->getWeather($id, $this->units, $this->lang);
		} catch(OWMException $e) {
			return false;
		} catch(\Exception $e) {
			return false;
		}
		if($weather) {
			return $this->parseWeather($weather);
		}
		return false;
	}
	
	public function saveWeather($weather) {
		$query = 'SELECT `directionId` FROM `aufgabe`.`windSpeedDirection` WHERE `direction` = ?';
		$windSpeedDirection = $this->DB->querySelect($query, [$weather['windSpeedDirection']]);
		if(empty($windSpeedDirection['result'][0])) {
			$query = 'INSERT INTO `aufgabe`.`windSpeedDirection` (`direction`) VALUES (?)';
			$res = $this->DB->query($query, $weather['windSpeedDirection']);
			if($res['status']) {
				$weather['windSpeedDirectionId'] = $res['data'];
			}
		} else {
			$weather['windSpeedDirectionId'] = $windSpeedDirection['result'][0]['directionId'];
		}
		
		$query = 'INSERT INTO `aufgabe`.`weather` (`cityId`, `temperatureNow`, `temperatureMin`, `temperatureMax`, `humidity`, `windSpeed`,
		`windSpeedDirectionId`, `weatherText`, `lastUpdate`, `insertDate`) VALUES (?,?,?,?,?,?,?,?,?,NOW()) ON DUPLICATE KEY UPDATE
		`cityId`=VALUES(`cityId`),`temperatureNow`=VALUES(`temperatureNow`),`temperatureMin`=VALUES(`temperatureMin`),`temperatureMax`=VALUES(`temperatureMax`),
		`humidity`=VALUES(`humidity`),`windSpeed`=VALUES(`windSpeed`),`windSpeedDirectionId`=VALUES(`windSpeedDirectionId`),`weatherText`=VALUES(`weatherText`),
		`lastUpdate`=VALUES(`lastUpdate`)';
		$res = $this->DB->query($query, [$weather['cityId'],$weather['temperature']['now'],$weather['temperature']['min'],$weather['temperature']['max'],$weather['humidity'],
		$weather['windSpeed'],$weather['windSpeedDirectionId'],$weather['weatherText'],$weather['lastUpdate']]);
	}
	
	public function saveForecastWeather($weather) {
		foreach($weather as $dayWeather) {
			$query = 'SELECT `directionId` FROM `aufgabe`.`windSpeedDirection` WHERE `direction` = ?';
			$windSpeedDirection = $this->DB->querySelect($query, [$dayWeather['windSpeedDirection']]);
			if(empty($windSpeedDirection['result'][0])) {
				$query = 'INSERT INTO `aufgabe`.`windSpeedDirection` (`direction`) VALUES (?)';
				$res = $this->DB->query($query, $dayWeather['windSpeedDirection']);
				if($res['status']) {
					$dayWeather['windSpeedDirectionId'] = $res['data'];
				}
			} else {
				$dayWeather['windSpeedDirectionId'] = $windSpeedDirection['result'][0]['directionId'];
			}
			
			$query = 'INSERT INTO `aufgabe`.`forecastWeather` (`cityId`, `temperatureNow`, `temperatureMin`, `temperatureMax`, `humidity`, `windSpeed`,
			`windSpeedDirectionId`, `weatherText`, `date`) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE
			`cityId`=VALUES(`cityId`),`temperatureNow`=VALUES(`temperatureNow`),`temperatureMin`=VALUES(`temperatureMin`),`temperatureMax`=VALUES(`temperatureMax`),
			`humidity`=VALUES(`humidity`),`windSpeed`=VALUES(`windSpeed`),`windSpeedDirectionId`=VALUES(`windSpeedDirectionId`),`weatherText`=VALUES(`weatherText`),
			`date`=VALUES(`date`)';
			$res = $this->DB->query($query, [$dayWeather['cityId'],$dayWeather['temperature']['now'],$dayWeather['temperature']['min'],$dayWeather['temperature']['max'],$dayWeather['humidity'],
			$dayWeather['windSpeed'],$dayWeather['windSpeedDirectionId'],$dayWeather['weatherText'],$dayWeather['date']]);
		}
	}
	
	private function getLastWeather($cityId) {
		$query = 'SELECT w.*, c.name, wd.direction FROM aufgabe.weather w
		JOIN aufgabe.citys c ON w.cityId = c.id
		JOIN aufgabe.windspeeddirection wd ON w.windSpeedDirectionId = wd.directionId
		WHERE w.cityId = ? AND insertDate > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 10 MINUTE) ORDER BY lastUpdate DESC LIMIT 1';
		$weather = $this->DB->querySelect($query, $cityId);
		if(!empty($weather['result'][0])) {
			return $this->parseWeather($weather['result'][0]);
		} else {
			return null;
		}
	}
	
	private function getLastWeatherForecast($cityId) {
		$query = 'SELECT w.*, c.name, wd.direction FROM aufgabe.forecastweather w
		JOIN aufgabe.citys c ON w.cityId = c.id
		JOIN aufgabe.windspeeddirection wd ON w.windSpeedDirectionId = wd.directionId
		WHERE w.cityId = ? AND `date` > DATE_SUB(UTC_TIMESTAMP(), INTERVAL '.$this->forecastDays.' DAY) ORDER BY `date` DESC LIMIT '.$this->forecastDays;
		$weather = $this->DB->querySelect($query, $cityId);
		if(!empty($weather['result'])) {
			$data = [];
			$weather['result'] = array_reverse($weather['result']);
			foreach($weather['result'] as $w) {
				$data[] = $this->parseWeather($w);
			}
			return $data;
		} else {
			return null;
		}
	}
	
	private function parseWeather($weather) {
		$data = [];
		if(is_array($weather)) {
			if(!empty($weather)) {
				$data['cityId'] = $weather['cityId'];
				$data['name'] = $weather['name'];
				$data['temperature']['now'] = $weather['temperatureNow'];
				$data['temperature']['min'] = $weather['temperatureMin'];
				$data['temperature']['max'] = $weather['temperatureMax'];
				$data['temperature']['unit'] = $this->temperatureUnit;
				$data['humidity'] = $weather['humidity'];
				$data['humidityUnit'] = $this->humidityUnit;
				$data['windSpeed'] = $weather['windSpeed'];
				$data['windSpeedUnit'] = $this->windSpeedUnit;
				$data['windSpeedDirection'] = $weather['direction'];
				$data['weatherText'] = $weather['weatherText'];
				if(!empty($weather['lastUpdate'])) {
					$data['lastUpdateFormatted'] = date('d.m.Y H:i:s', strtotime($weather['lastUpdate']));
				} elseif(!empty($weather['date'])) {
					$data['dateFormatted'] = date('d.m.Y', strtotime($weather['date']));
				}
			}
		} else {
			$data['cityId'] = $weather->city->id;
			$data['name'] = $weather->city->name;
			$data['temperature']['now'] = $weather->temperature->now->getValue();
			$data['temperature']['min'] = $weather->temperature->min->getValue();
			$data['temperature']['max'] = $weather->temperature->max->getValue();
			$data['temperature']['unit'] = $this->temperatureUnit;
			$data['humidity'] = $weather->humidity->getValue();
			$data['humidityUnit'] = $this->humidityUnit;
			$data['windSpeed'] = $weather->wind->speed->getValue();
			$data['windSpeedUnit'] = $this->windSpeedUnit;
			$data['windSpeedDirection'] = $weather->wind->direction->getUnit();
			$data['weatherText'] = $weather->weather->description;
			$data['lastUpdate'] = $weather->lastUpdate->format('Y-m-d H:i:s');
			$data['lastUpdateFormatted'] = $weather->lastUpdate->format('d.m.Y H:i:s');
			if(get_class($weather) == 'Cmfcmf\OpenWeatherMap\Forecast' && $weather->time && $weather->time->from) {
				$data['date'] = $weather->time->from->format('Y-m-d');
				$data['dateFormatted'] = $weather->time->from->format('d.m.Y');
			}
		}
		return $data;
	}
}

?>