$(document).ready(function() {
	
	var city = new Bloodhound({
	  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
	  queryTokenizer: Bloodhound.tokenizers.whitespace,
	  remote: {
		url: './rpc.php?action=getCity&params=%QUERY',
		wildcard: '%QUERY'
	  }
	});
	
	city.initialize();
	
	$('input.city').typeahead({
		hint: true,
		highlight: true,
		minLength: 1
	},
	{
		name: 'city',
		source: city,
		limit:10,
		display: 'name',
		templates: {
			suggestion: function(data) {
				suggestion: return '<div>'+data.name+' – '+data.countryCode+'</div>'
			}
		}
	}).on("typeahead:selected", function(event, object) {
		$('input.city').blur();
		showWeather(object);
	});
	
});

function showWeather(obj) {
	$.getJSON( "./rpc.php", {
    action: 'getWeather',
	params: {id:obj.id}
   }, function( data ) {
	  if(data) {
		  $('#city').text( ' für '+data.name );
		  $('#dataUpdate').text( data.lastUpdateFormatted+' (UTC)' );
		  $('#humidity').text( data.humidity+data.humidityUnit );
		  $('#wind').text( data.windSpeed+' '+data.windSpeedUnit+' '+data.windSpeedDirection );
		  $('#temperature').html( 'Aktuell: '+data.temperature.now+' '+data.temperature.unit+'<br />'+
			'Minimal: '+data.temperature.min+' '+data.temperature.unit+'<br />'+
			'Maximal: '+data.temperature.max+' '+data.temperature.unit
		  );
		  
		  $.each(data.forecast, function( index, value ) {
			  $('#dateForecast'+index).text( value.dateFormatted );
			  $('#humidityForecast'+index).text( value.humidity+value.humidityUnit );
			  $('#windForecast'+index).text( value.windSpeed+' '+value.windSpeedUnit+' '+value.windSpeedDirection );
			  $('#temperatureForecast'+index).html( 'Aktuell: '+value.temperature.now+' '+value.temperature.unit+'<br />'+
				'Minimal: '+value.temperature.min+' '+value.temperature.unit+'<br />'+
				'Maximal: '+value.temperature.max+' '+value.temperature.unit
			  );
		  });
		  
		  $('.weather, footer').removeClass('hidden');
	  } else {
		  $('.weather, footer').addClass('hidden');
		  $('#dataUpdate, #city').text('');
	  }
   });
}