<?php
/*
Plugin Name: PDXChambers Weather
Plugin URI: https://www.pdxchambers.com/pdxchambers-weather
Description: Plugin to display local weather information as a widget and at the top of the page.
Author: pdxchambers
Version: 1.0
Author URI: https://www.pdxchambers.com/
*/

/*
	Register JavaScript
*/
add_action('wp_enqueue_scripts','pdxc_top_weather_init');

function pdxc_top_weather_init() {
	wp_enqueue_script( 'pdxc-weather-js', plugins_url( '/js/pdxc-weather-insert.js', __FILE__ ), array('jquery'),'1.0', false);
}

/*
	Register widget
*/

class pdxchambers_weather extends WP_Widget {
	public function __construct() {
		$widget_options = array(
			'classname' => 'pdxc_weather',
			'description' => 'A simple weather widget. Adds local weather to WordPress.',
		);

		parent::__construct('pdxchambers_weather', 'PDXChambers Weather', $widget_options);
	}

	public function widget($args, $instance){		
		$data = get_weather($instance['countryCode'], $instance['zipCode'], $instance['tempUnits'], $instance['api_key']);
		if($data['cod'] != 200 && array_key_exists('message', $data)){
			echo '<h3>Weather data not found.</h3>';
			echo '<p><strong>Code ' . $data['cod'] . ':</strong> ' . $data['message'] . '</p>';
		} else {
		?>
		<?php 
			if($instance['tempUnits'] == 'metric'){
				$units = '&deg; C';
			} else if($instance['tempUnits'] == 'imperial'){
				$units = '&deg; F';
			}else {
				$units = ' K';
			}
			$temp = round($data['main']['temp'], 0);
			$barometer = round($data['main']['pressure'], 0);
			$humidity = round($data['main']['humidity'], 0);
			$weatherIcon = 'http://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png';
			$condition = $data['weather'][0]['description'];
			$displayWidget = (! empty($instance['widgetDisplay'])) ? 'block' : 'none';
			$displayTop = (! empty($instance['topDisplay'])) ? 'block' : 'none';
			/*Render the widget*/
			echo $args['before_widget'];
		?>
			<table style="display: <?php echo $displayWidget; ?>;">
				<caption><?php echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; ?></caption>
				<tbody>
					<tr>
						<td rowspan="5"><img src="<?php echo $weatherIcon; ?>" alt="<?php echo $condition; ?>"></td>
					</tr>
					<tr>
						<td><span><?php echo $condition; ?></span></td>
					</tr>
					<tr>
						<td><span>Temperature: <?php echo $temp . $units; ?></span></td>
					</tr>
					<tr>
						<td><span>Pressure: <?php echo $barometer . ' hPa'; ?></span></td>
					</tr>
					<tr>
						<td><span>Humidity: <?php echo $humidity . '%'; ?></span></td>
					</tr>
				</tbody>
			</table>
			<p style="font-style: italic; display: <?php echo $displayWidget; ?>;">Weather data courtesy of <a href="https://openweathermap.org/" style="color: inherit;">OpenWeather</a>.</p>
		<?php
		}
		echo $args['after_widget'];
		$topHTML = '<div id="pdxc-top-weather" style="background-color: black; color: white; display: ' .$displayTop . '; margin-right: 30px; text-align: right;">Current Temperature: ' . $temp . $units . '</div>';
		echo $topHTML;
	}

	public function form( $instance ){
		$title = 'Local Weather';
		$formHTML = '';
		$countryCode = 'us';
		$zipCode = '97035';
		$tempUnits = 'imperial';
		$topDisplay = false;
		$widgetDisplay = false;
		if(! empty($instance['tempUnits'])){
			$tempUnits = $instance['tempUnits'];
		}
		if (! empty( $instance['title'] )){ 
			$title = $instance['title'];
		}

		if (! empty( $instance['zipCode'] )){ 
			$zipCode = $instance['zipCode'];
		}

		if (! empty( $instance['countryCode'] )){ 
			$countryCode = $instance['countryCode'];
		}
		if(! empty($instance['topDisplay'])){
			$topDisplay = true;
		}
		if(! empty($instance['widgetDisplay'])){
			$widgetDisplay = true;
		}
		
		if (! empty( $instance['api_key'] )){ 
			$api_key = $instance['api_key'];
		}
		?>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>">

		<label for="<?php echo $this-> get_field_id( 'zipCode' ); ?>">Zip/Postal Code:</label>
		<input class="widefat" id="<?php echo $this-> get_field_id( 'zipCode' ); ?>" name="<?php echo $this->get_field_name( 'zipCode' ); ?>" type="text" value="<?php echo $zipCode; ?>">

		<label for="<?php echo $this-> get_field_id( 'countryCode' ); ?>">Country Code:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'countryCode' ); ?>" name="<?php echo $this->get_field_name( 'countryCode' ); ?>" type="text" value="<?php echo $countryCode;?>">

		<label for="<?php echo $this-> get_field_id( 'tempUnits' ); ?>">Select temperature units.</label>
		<select  class="widefat" id="<?php echo $this-> get_field_id( 'tempUnits' ); ?>" name="<?php echo $this->get_field_name('tempUnits'); ?>">
			<option value="kelvin" <?php echo ($tempUnits == '') ? 'selected' : ''; ?>>
				Kelvin(K)
			</option>
			<option value="metric" <?php echo ($tempUnits == 'metric') ? 'selected' : '';  ?>>
				Celcius(C)
			</option>
			<option value="imperial" <?php echo ($tempUnits == 'imperial') ? 'selected' : '';  ?>>
				Farenheit(F)
			</option>
		</select>
		<div class="widefat">
			<label for="<?php echo $this-> get_field_id( 'topDisplay' ); ?>">Display current temperature at top of page:</label>
			<input class="widgetCheck" id="<?php echo $this-> get_field_id( 'topDisplay' ); ?>" name="<?php echo $this-> get_field_name( 'topDisplay' ); ?>" type="checkbox" <?php echo $topDisplay ? 'checked': '';?>>
		</div>
		<div class="widefat">
			<label for="<?php echo $this-> get_field_id( 'widgetDisplay' ); ?>">Display widget:</label>
			<input class="widgetCheck" id="<?php echo $this-> get_field_id( 'widgetDisplay' ); ?>" name="<?php echo $this-> get_field_name( 'widgetDisplay' ); ?>" type="checkbox" <?php echo $widgetDisplay ? 'checked': '';?>>
		</div>
		<div class="widefat">
			<label for="<?php echo $this->get_field_id( 'api_key' ); ?>"><?php esc_attr_e( 'Open Weather Map Application Key: ',  'text_domain');?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'api_key' ); ?>" name="<?php echo $this->get_field_name( 'api_key' ) ?>" type="text" value="<?php echo $api_key; ?>">
		</div>
		<?php
	}

	public function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['countryCode'] = ( ! empty( $new_instance['countryCode'] ) ) ? strip_tags( $new_instance['countryCode'] ) : ''; 
		$instance['zipCode'] = ( ! empty( $new_instance['zipCode'] ) ) ? strip_tags( $new_instance['zipCode'] ) : '';
		$instance['tempUnits'] =  ( $new_instance['tempUnits'] != '' ) ? $new_instance['tempUnits'] : '';
		$instance['topDisplay'] =  $new_instance['topDisplay'];
		$instance['widgetDisplay'] = $new_instance['widgetDisplay'];
		$instance['api_key'] = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';

		return $instance;
	}
}

function get_Weather($country, $zip, $units, $api_key){
	$url = 'api.openweathermap.org/data/2.5/weather';
	$queryString = '?zip=';
	if($country = ''){
		$queryString .= $zip . ',us';
	} else {
		$queryString .= $zip . ',' . $country;
	}

	if($units == 'metric'){
		$queryString .= '&units=metric';
	} else if ($units == 'imperial'){
		$queryString .= '&units=imperial';
	}
	$queryString .= '&APPID=' . $api_key;
	$headers = array(
		'Accept' => 'application/json',
	);

	/*Fetch weather data*/
	$rs = curl_init($url . $queryString);
	curl_setopt($rs, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($rs, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($rs, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($rs, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($rs, CURLOPT_HTTPGET, TRUE);
	$weather_data = json_decode(curl_exec($rs), true);
	
	return $weather_data;
}

add_action( 'widgets_init', function(){
	register_widget( 'pdxchambers_weather');
});
