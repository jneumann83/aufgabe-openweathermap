<?php
include	'../includes/dbconnection.php';

$DB	=	new \Database\Database();
$createTableCity = 'CREATE TABLE `citys` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `countryCode` char(2) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
$DB->query($createTableCity, []);

$createTableWindspeeddirection = 'CREATE TABLE `windspeeddirection` (
  `directionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `direction` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`directionId`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
$DB->query($createTableWindspeeddirection, []);

$createTableForecastweather = 'CREATE TABLE `forecastweather` (
  `cityId` int(10) unsigned NOT NULL,
  `temperatureNow` decimal(4,2) NOT NULL,
  `temperatureMin` decimal(4,2) NOT NULL,
  `temperatureMax` decimal(4,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `windSpeed` decimal(5,2) NOT NULL,
  `windSpeedDirectionId` int(10) unsigned NOT NULL,
  `weatherText` text COLLATE utf8_bin NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`cityId`,`date`),
  KEY `fk_directionId` (`windSpeedDirectionId`),
  CONSTRAINT `fk_cityIdForecast` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_directionIdForecast` FOREIGN KEY (`windSpeedDirectionId`) REFERENCES `windspeeddirection` (`directionId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
$DB->query($createTableForecastweather, []);

$createTableWeather = 'CREATE TABLE `weather` (
  `cityId` int(10) unsigned NOT NULL,
  `temperatureNow` decimal(4,2) NOT NULL,
  `temperatureMin` decimal(4,2) NOT NULL,
  `temperatureMax` decimal(4,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `windSpeed` decimal(5,2) NOT NULL,
  `windSpeedDirectionId` int(10) unsigned NOT NULL,
  `weatherText` text COLLATE utf8_bin NOT NULL,
  `lastUpdate` datetime NOT NULL,
  `insertDate` datetime NOT NULL,
  PRIMARY KEY (`cityId`,`lastUpdate`),
  KEY `lastUpdate` (`lastUpdate`),
  KEY `fk_directionId` (`windSpeedDirectionId`),
  CONSTRAINT `fk_cityId` FOREIGN KEY (`cityId`) REFERENCES `citys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_directionId` FOREIGN KEY (`windSpeedDirectionId`) REFERENCES `windspeeddirection` (`directionId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
$DB->query($createTableWeather, []);

?>