<?php
class Head {
	
	function getHead() {
		$tmp	=	'<head>'."\n";
		$tmp	.=	$this->getMetaTags();
		$tmp	.=	$this->getCSS();
		$tmp	.=	$this->getJs();		
		$tmp	.=	'</head>'."\n";
		
		return	$tmp;
	}
	
	private function getMetaTags() {
		$tmp	=	'<meta charset="utf-8">'."\n";
		$tmp	=	'<meta http-equiv="X-UA-Compatible" content="IE=edge">'."\n";
		$tmp	=	'<meta name="viewport" content="width=device-width, initial-scale=1">'."\n";
		$tmp	=	'<meta name="description" content="">'."\n";
		$tmp	=	'<meta name="author" content="">'."\n";
		$tmp	=	'<title>Bootstrap Template</title>'."\n";
		$tmp	.=	'<meta http-equiv="language" content="DE">'."\n";
		$tmp	.=	'<meta http-equiv="cache-control" content="no-cache" />'."\n";
		$tmp	.=	'<meta name="Robots" content="follow,index" />'."\n";
		$tmp	.=	'<meta http-equiv="content-language" content="de" />';	
		
		return	$tmp;
	}
	
	private function getJS() {
		$tmp	=	'<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>'."\n";
		$tmp	.=	'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>'."\n";
		$tmp	.=	'<!--[if lt IE 9]>'."\n".
					'<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>'."\n".
					'<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>'."\n".
					'<![endif]-->'."\n".
					'<script src="js/custom.js"></script>'."\n".
					'<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>'."\n";
					
		return	$tmp;
	}
	
	private function getCSS() {
		$tmp	=	'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous" />'."\n";
		$tmp	.=	'<link rel="stylesheet" href="/aufgabe/css/custom.css" />'."\n";
		
		return	$tmp;
	}

}
?>