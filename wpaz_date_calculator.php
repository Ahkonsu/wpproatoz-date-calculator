<?php
/**
 * Plugin Name: === WP Pro A to Z Date Calculator ===
 * Plugin URI: https://wpproatoz.com/plugins/
 * Description: Plugin to add a date calculator to a website. This plugin adds a date calculator to your site to help you determine a date into the future or the past. It accounts for leap years and you can choose hours, days, weeks, months and years. You can adjust for only 8 hour business days when choosing per hour or business days when choosing by date. Use the following shortcode to place on a page or post [wpaz_calculator]
 * Version: 1.0.2
 * Author: Shawn DeWolfe (dewolfe001) / WPPro AtoZ / Carl
 * Author URI: https://WPPluginsAtoZ.com/
 * Requires at least: 5.0.0
 * Tested up to: 6.7.1
 *
 * Text Domain: wpazdc
 * Domain Path: /languages/
 *
 * @package WPAZ_DateCalc
 * @category Core
 * @author dewolfe001
 */
 
 ////***check for updates code

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Ahkonsu/wpproatoz-date-calculator/',
	__FILE__,
	'wpproatoz-date-calculator'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//$myUpdateChecker->getVcsApi()->enableReleaseAssets();
 
 
//Optional: If you're using a private repository, specify the access token like this:
//$myUpdateChecker->setAuthentication('your-token-here');

/////////////////////


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of WPAZ_DateCalc to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WPAZ_DateCalc
 */
function WPAZ_DateCalc() {
	return WPAZ_DateCalc::instance();
} // End WPAZ_DateCalc()

add_action( 'plugins_loaded', 'WPAZ_DateCalc' );

/**
 * Main WPAZ_DateCalc Class
 *
 * @class WPAZ_DateCalc
 * @version	1.0.0
 * @since 1.0.0
 * @package	WPAZ_DateCalc
 * @author dewolfe001
 */
final class WPAZ_DateCalc {
	/**
	 * WPAZ_DateCalc The single instance of WPAZ_DateCalc.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $plugin_path;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * The settings object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings;
	// Admin - End
	
	// Shortcode - Start
	/**
	 * The the shortcode we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $shortcode;
	//end shorcode
	

	// Post Types - Start
	/**
	 * The post types we're registering.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $post_types = array();
	// Post Types - End
	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		$this->token 			= 'wpazdc';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		// Admin - Start
		require_once( 'classes/wpazdc-settings.php' );
			$this->settings = WPAZ_DateCalc_Settings::instance();

		if ( is_admin() ) {
			require_once( 'classes/wpazdc-admin.php' );
			$this->admin = WPAZ_DateCalc_Admin::instance();
		}
		// Admin - End

		// Widget
		require_once( 'classes/wpazdc-widget.php' );
		add_action( 'widgets_init', function(){
			register_widget( 'WPAZ_DateCalc_Widget' );
		});

		// Shortcode
		require_once( 'classes/wpazdc-shortcode.php' );
		$this->shortcode = new WPAZ_DateCalc_Shortcode();

		// Post Types - Start
		// require_once( 'classes/wpazdc-post-type.php' );
		// require_once( 'classes/wpazdc-taxonomy.php' );

		// Register an example post type. To register other post types, duplicate this line.
		// $this->post_types['thing'] = new WPAZ_DateCalc_Post_Type( 'thing', __( 'Thing', 'wpazdc' ), __( 'Things', 'wpazdc' ), array( 'menu_icon' => 'dashicons-carrot' ) );
		// Post Types - End
		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	} // End __construct()

	/**
	 * Main WPAZ_DateCalc Instance
	 *
	 * Ensures only one instance of WPAZ_DateCalc is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WPAZ_DateCalc()
	 * @return Main WPAZ_DateCalc instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wpazdc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

	/**
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'We dont serve droids' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'No bots allowed' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	} // End _log_version_number()

	// return the HTML of the form
	public function calculator($use = 'widget', $unitdefault = 'day') {
		$wpazdc_display_fields          = get_option( 'wpazdc-display-fields' );
		$calculator_form_area_title     = $wpazdc_display_fields['calculator_form_area_title'] ?? '';
		$display_use_only_business_days = $wpazdc_display_fields['display_use_only_business_days'] ?? 'false';
		$display_use_eight_hour_days    = $wpazdc_display_fields['display_use_eight_hour_days'] ?? 'false';
		$display_hour_option            = $wpazdc_display_fields['display_hour_option'] ?? 'false';
		$display_week_option            = $wpazdc_display_fields['display_week_option'] ?? 'false';
		$display_month_option           = $wpazdc_display_fields['display_month_option'] ?? 'false';
		$display_year_option            = $wpazdc_display_fields['display_year_option'] ?? 'false';

		$html = '<div class="wpazdc_calc" id="calc_'.$use.'">';
		if( !empty( $calculator_form_area_title ) ) {
			$html .= '<h2>'.__($calculator_form_area_title, 'wpazdc' ).'</h2>';
		}

		$html .= '<form>';
		$html .= '<div class="bundled">';
		$html .= '<div>';
		$html .= '<input type="date" value="'.date("Y-m-d").'" id="desired_date" />';
		$html .= '</div>';
		$html .= '<div class="time"> <br>Enter Time HH:MM AM/PM <br>';
		$html .= '<input type="time" value="'.date("H:i:s").'" id="desired_time" />';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div>';
		$html .= '<input type="number" id="add_unit" min="0" value="1" size="6" />';
		$html .= '</div>';
		$html .= '<div class="selectbox">';
		$html .= '<select id="the_unit">';
		if( $display_hour_option !== 'false' ){
			$html .= '<option'.($unitdefault == 'hour' ? ' selected' : '').' value="hour">'.__('Hour').'</option>';
		}

		$html .= '<option'.($unitdefault == 'day' ? ' selected' : '').' value="day">'.__('Day').'</option>';

		if( $display_week_option !== 'false' ){
			$html .= '<option'.($unitdefault == 'week' ? ' selected' : '').' value="week">'.__('Week').'</option>';
		}

		if( $display_month_option !== 'false' ){
			$html .= '<option'.($unitdefault == 'month' ? ' selected' : '').' value="month">'.__('Month').'</option>';
		}

		if( $display_year_option !== 'false' ){
			$html .= '<option'.($unitdefault == 'year' ? ' selected' : '').' value="year">'.__('Year').'</option>';
		}

		$html .= '</select>';
		$html .= '</div>';
		
		$html .= '<div class="checkxboxes">';
		$html .= '<nobr class="backforward"><span class="pre">'.__("Back").'</span><label class="switch"><input type="hidden" id="backforwards" name="backforwards" value="1" /><span class="slider round backforwards"></span></label><span class="post">'.__("Forward").'</span></nobr><br class="backforwards"/>';

		if( $display_use_eight_hour_days === 'true' ) {
			$html .= '<nobr class="time"><span class="pre"></span><label class="switch"><input type="hidden" id="workhours" name="workhours" value="-1" /><span class="slider round"></span></label><span class="post">'.__("Use 8 hour 'days'").'</span></nobr><br class="time"/>';
		}
		if( $display_use_only_business_days === 'true' ) {
			$html .= '<nobr class="days"><span class="pre"></span><label class="switch"><input type="hidden" id="workdays" name="workdays" value="-1" /><span class="slider round"></span></label><span class="post">'.__('Use only business days').'</span></label></nobr>';
		}

		// $html .= '<br class="holidaytime"/><nobr class="holidaytime"><input type="hidden" id="holidays" name="holidays" value="-1"/><label for="holidays">'.__("Don't count holidays").'</label></nobr>';
		$html .= '</div>';
		$html .= '<div class="output-format">
    <label for="output_format">Choose Output Format:</label><br>
    <select id="output_format">
        <option value="YYYY-MM-DD HH:mm:ss A">YYYY-MM-DD HH:mm:ss AM/PM</option>
        <option value="MM/DD/YYYY h:mm A">MM/DD/YYYY h:mm AM/PM</option>
        <option value="DD-MM-YYYY HH:mm">DD-MM-YYYY HH:mm</option>
        <option value="DD/MM/YYYY">DD/MM/YYYY</option>
    </select>
</div>';
		$html .= '<div class="submitbutton">';
		$html .= '<input type="button" id="calculate" value="'.__('Calculate').'" />';
		$html .= '</div>';
		$html .= '</form>';
		$html .= '<div id="result"></div>';
		// $html .= '<div id="dump">DUMP</div>';		
  		$html .= '</div>';
		return $html;
	}
} // End Class