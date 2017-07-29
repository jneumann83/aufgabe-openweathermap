<?php
ob_start ("ob_gzhandler");
include	'includes/head.php';

require_once	'frontend/weather/weather.php';

$Head		=	new Head();
$Weather	=	new \Weather\Weather();

session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<?php
	echo $Head->getHead();
?>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="headline">
                <h1 class="text-center">Das aktuelle Wetter<span id="city" class="hidden-xs"></span></h1>
            </div>
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <!-- Heading Row -->
        <div class="row">
            <div class="col-xs-12">
                <input type="text" name="city" size="30" class="city typeahead" placeholder="Stadtname bitte eingeben" />
            </div>
            <!-- /.col-md-8 -->
        </div>
        <!-- /.row -->

        <!-- Content Row -->
        <div class="row weather hidden">
            <div class="col-xs-12 col-md-4">
                <h4>Temperatur</h4>
                <p id="temperature"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Luftfeuchtigkeit</h4>
                <p id="humidity"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Wind</h4>
                <p id="wind"></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->
		
		<!-- Heading Row -->
        <div class="row weather hidden">
            <div class="col-xs-12">
                <h3>Vorschau</h3>
            </div>
        </div>
        <!-- /.row -->
		
		<!-- Content Row -->
		<div class="row weather hidden forecastHead">
            <div class="col-xs-12">
                Datum <span id="dateForecast0"></span>
            </div>
        </div>
        <div class="row weather hidden">
            <div class="col-xs-12 col-md-4">
                <h4>Temperatur</h4>
                <p id="temperatureForecast0"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Luftfeuchtigkeit</h4>
                <p id="humidityForecast0"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Wind</h4>
                <p id="windForecast0"></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->
		<!-- Content Row -->
		<div class="row weather hidden forecastHead">
            <div class="col-xs-12">
                Datum <span id="dateForecast1"></span>
            </div>
        </div>
        <div class="row weather hidden">
            <div class="col-xs-12 col-md-4">
                <h4>Temperatur</h4>
                <p id="temperatureForecast1"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Luftfeuchtigkeit</h4>
                <p id="humidityForecast1"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Wind</h4>
                <p id="windForecast1"></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->
		<!-- Content Row -->
		<div class="row weather hidden forecastHead">
            <div class="col-xs-12">
                Datum <span id="dateForecast2"></span>
            </div>
        </div>
        <div class="row weather hidden">
            <div class="col-xs-12 col-md-4">
                <h4>Temperatur</h4>
                <p id="temperatureForecast2"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Luftfeuchtigkeit</h4>
                <p id="humidityForecast2"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Wind</h4>
                <p id="windForecast2"></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->
		<!-- Content Row -->
		<div class="row weather hidden forecastHead">
            <div class="col-xs-12">
                Datum <span id="dateForecast3"></span>
            </div>
        </div>
        <div class="row weather hidden">
            <div class="col-xs-12 col-md-4">
                <h4>Temperatur</h4>
                <p id="temperatureForecast3"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Luftfeuchtigkeit</h4>
                <p id="humidityForecast3"></p>
            </div>
            <!-- /.col-md-4 -->
            <div class="col-xs-12 col-md-4">
                <h4>Wind</h4>
                <p id="windForecast3"></p>
            </div>
            <!-- /.col-md-4 -->
        </div>
        <!-- /.row -->

        <!-- Footer -->
        <footer class="hidden">
            <div class="row">
                <div class="col-lg-12">
                    Daten vom: <span id="dataUpdate"></span>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

</body>

</html>