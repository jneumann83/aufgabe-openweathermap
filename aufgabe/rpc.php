<?php
namespace Rpc;

require_once	'frontend/weather/weather.php';

class Rpc {
	
	private $Weather;
	
	public function __construct()
    {
		$this->Weather	=	new \Weather\Weather();
    }
	
	public function makeQuery() {
		if(Empty($_REQUEST['action'])) {
			return;
		}
		if(!method_exists($this->Weather,$_REQUEST['action'])) {
			return;
		}
		$response = $this->Weather->{$_REQUEST['action']}((isset($_REQUEST['params']) ? $_REQUEST['params'] : null));
		if($response) {
			echo json_encode($response);
		}
	}
	
}

$rpc	=	new \Rpc\Rpc();
$rpc->makeQuery();