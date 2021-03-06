<?php
/**
 * Plugin Name: WDS Announcements
 * Plugin URI:  http://webdevstudios.com
 * Description: Create custom, sticky announcements at the top of your site
 * Version:     0.1.1
 * Author:      WebDevStudios
 * Author URI:  http://webdevstudios.com
 * Donate link: http://webdevstudios.com
 * License:     GPLv2
 * Text Domain: wds-announcements
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp.
 */


/**
 * Autoloads files with classes when needed.
 *
 * @since 0.1.0
 *
 * @param string $class_name Name of the class being requested.
 * @return void
 */
function wds_announcements_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'WDS_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'WDS_' ) )
	) );

	WDS_Announcements::include_file( $filename );
}
spl_autoload_register( 'wds_announcements_autoload_classes' );


/**
 * Main initiation class.
 *
 * @since 0.1.0
 *
 * @var string $version  Plugin version.
 * @var string $basename Plugin basename.
 * @var string $url      Plugin URL.
 * @var string $path     Plugin Path.
 */
class WDS_Announcements {

	/**
	 * Current version.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	const VERSION = '0.1.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var string
	 * @since 0.1.0
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var object WDS_Announcement
	 * @since 0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of WDS_Announcements.
	 *
	 * @var WDS_Announcements
	 */
	protected $announcements;

	/**
	 * Instance of WDS_Announcements_Options.
	 *
	 * @var WDS_Announcements
	 */
	protected $announcements_options;

	/**
	 * Instance of WDS_Announcements_Frontend.
	 *
	 * @var WDS_Announcements
	 */
	protected $announcements_frontend;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 0.1.0
	 * @return WDS_Announcements A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		$this->plugin_classes();
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->announcements = new WDS_Announcements_Cpt( $this );
		$this->announcements_options = new WDS_Announcements_Options( $this );
		$this->announcements_frontend = new WDS_Announcements_Frontend( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since 0.1.0
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 *
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since 0.1.0
	 */
	function _deactivate() {}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'wds-announcements', false, dirname( $this->basename ) . '/languages/' );
		}
	}

	/**
	 * Check if the plugin meets requirements and disable it if they are not present.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean Result of meets_requirements.
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			deactivate_plugins( $this->basename );

			return false;
		}

		return true;
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').

		// We have met all requirements.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function requirements_not_met_notice() {
		echo '<div id="message" class="error">';
		echo '<p>' . sprintf( __( 'WDS Announcements plugin is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wds-announcements' ), admin_url( 'plugins.php' ) ) . '</p>';
		echo '</div>';
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $filename Name of the file to be included.
	 * @return bool Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'includes/class-'. $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since 0.1.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the WDS_Announcement object and return it.
 * Wrapper for WDS_Announcement::get_instance()
 *
 * @since 0.1.0
 *
 * @return object WDS_Announcement Singleton instance of plugin class.
 */
function wds_announcements() {
	return WDS_Announcements::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wds_announcements(), 'hooks' ) );
