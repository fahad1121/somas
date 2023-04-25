<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Boost
 * @subpackage Boost/includes
 * @author     cristian stoicescu <email@email.email>
 */
class Boost_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		Boost_Deactivator::cronstarter_deactivate();
	}

	// unschedule event upon plugin deactivation
	public static function cronstarter_deactivate() {
		$timestamp = wp_next_scheduled ('boost_fake_boosts_job');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'boost_fake_boosts_job');
	}

}
