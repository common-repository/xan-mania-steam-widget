<?php
/*
Plugin Name: Xan Mania Steam Widget
Plugin URI: http://xan-mania.de
Description: A Wordpress widget for displaying your Steam account information and statistics.
Version: 1.0.3
Author: Xan Mania
Author URI: http://xan-webdesign.de
*/

/*Widgets_init hook. */
add_action( 'widgets_init', 'initialize_widget' );
/* Function that registers widget. */
function initialize_widget() {
	register_widget( 'steam_widget' );
	/* Xan Mania Steam Widget*/
}
class steam_widget extends WP_Widget {
	//Widget Processes
	function steam_widget() {
		$widget_ops = array( 'classname' => 'Xan Mania Steam Widget', 'description' => 'A Wordpress widget for displaying your Steam account informations and statistics.' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 480, 'height' => 300, 'id_base' => 'steam-widget' );
		/* Create the widget. */
		$this->WP_Widget( 'steam-widget', 'Xan Mania Steam Widget', $widget_ops, $control_ops );
	}
	//Output Content
	function widget($args, $instance) {
		extract( $args );
		$ProfileURL = $instance['url'];
		$xml = simplexml_load_file($ProfileURL . '?xml=1');
		$steamID64 = $xml->steamID64;
		$title = apply_filters('widget_title', $instance['title']);
		//TITLE, if Provided
		echo $before_widget;
		if ( $title ){
			echo $before_title . $title . $after_title;
		}
		else {
			echo $before_title . $after_title;
		}
		$xml->stateMessage = str_replace("Counter-Strike:", "CS:", $xml->stateMessage);
		$xml->stateMessage = str_replace("Global Offensive Beta", "GO Beta", $xml->stateMessage);
		$xml->stateMessage = str_replace("In-Game<br />", "<strong>Playing: </strong>", $xml->stateMessage);
		$xml->stateMessage = str_replace("Last Online:", "<strong>Offline</strong> since:", $xml->stateMessage);
		$xml->stateMessage = str_replace("ago", "", $xml->stateMessage);
		$xml->stateMessage = str_replace("Online", "<strong>Currently Online</strong>", $xml->stateMessage);
		//Begin Widget Content
		echo '<strong>SteamID:</strong> ';
		echo '<a href="'.$ProfileURL.'">'.$xml->steamID . '</a><br />';
		echo ''.$xml->stateMessage.'<br />';
		echo '<strong>Steam Rating:</strong> '.$xml->steamRating . '<br />';
		echo '<strong>Playing time:</strong> ';
		echo $xml->hoursPlayed2Wk . " hrs in 2 weeks";
		for($i = 0; $i < $instance['num']; $i++){
			if ($xml->mostPlayedGames->mostPlayedGame[$i]->gameLink != "") {
				//Replacements
				#########################################################################################################
				$xml->stateMessage = str_replace("Counter-Strike:", "CS:", $xml->stateMessage);                         #
				$xml->stateMessage = str_replace("Global Offensive Beta", "GO Beta", $xml->stateMessage);               #
				$xml->stateMessage = str_replace("In-Game<br />", "<strong>Playing: </strong>", $xml->stateMessage);    #
				$xml->stateMessage = str_replace("Last Online:", "<strong>Offline</strong> since:", $xml->stateMessage);#
				$xml->stateMessage = str_replace("ago", "", $xml->stateMessage);                                        #
				$xml->stateMessage = str_replace("Online", "<strong>Derzeit online</strong>", $xml->stateMessage);      #
				$xml->stateMessage = str_replace("Tony Hawk's Pro Skater", "THPS", $xml->stateMessage);                 #
				$xml->stateMessage = str_replace("Edna & Harvey:", "", $xml->stateMessage);                             #
				#########################################################################################################      
				echo '<br/><img src="' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameIcon . '" height="15" width="15" alt="' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameName . '"/> <a href="' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameLink . '">' . $xml->mostPlayedGames->mostPlayedGame[$i]->gameName . '</a> ' . $xml->mostPlayedGames->mostPlayedGame[$i]->hoursPlayed . ' hrs';
				
			}
		}
		echo '<p><a href="steam://friends/add/' . $steamID64 . '">Add to Friends</a> || <a href="'.$ProfileURL.'games?tab=all">View all Games</a></p><br />'; ;
	}
	//Process Options to be saved
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['num'] = strip_tags($new_instance['num']);
		return $instance;
	}
	//Options form on admin
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Steam', 'url' => 'http://steamcommunity.com/id/xanatori/', 'num' => '3');

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
			</p>
			<p>Enter the url of your Steam profile. It should look like this: <a>http://steamcommunity.com/id/xanatori/</a></p>
				<label for="<?php echo $this->get_field_id( 'url' ); ?>">Profile URL:</label>
				<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:100%;" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'num' ); ?>">Show # of games:</label>
				<input type="number" min="1" max="5" id="<?php echo $this->get_field_id( 'num' ); ?>"  name="<?php echo $this->get_field_name( 'num' ); ?>" value="<?php echo $instance['num']; ?>" style="width:100%;" />
			</p>
		<?php
	}
}
?>