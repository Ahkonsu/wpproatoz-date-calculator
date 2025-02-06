<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * PLDYN_DateCalc_Shortcode Class
 *
 * @class PLDYN_DateCalc_Shortcode
 * @version	1.0
 * @since 1.0
 * @package	PLDYN_DateCalc
 * @author dewolfe001
 */
final class WPAZ_DateCalc_Shortcode {
  // build the available shortcodes
  public function __construct() {
	add_shortcode('wpaz_calculator', array($this, 'shortcode_calc'));
	add_action( 'init', array($this,'register_resources') );
  }
  // End __construct()

  
  public function shortcode_calc($atts, $content = "") {
	$atts = shortcode_atts(
		array('id' => 'shortcode'), $atts, 'shortcode_calc' 
	);  

	// Register site styles and scripts
	$WPAZ_DateCalc = new WPAZ_DateCalc();		
	wp_enqueue_script( 'wpazdc_script' );
	wp_enqueue_style( 'wpazdc_style' );
	return $WPAZ_DateCalc->calculator($atts['id']); 
  }

    public function register_resources() {
		wp_register_script( 'wpazdc_script', plugins_url( '../public/js/wpaz_date_calculator.js', __FILE__ ), array('jquery') );
		wp_register_style( 'wpazdc_style', plugins_url( '../public/css/wpaz_date_calculator.css', __FILE__ ) );
    }


	public function register_scripts() {
		wp_register_script( 'wpazdc_script', plugins_url( '../public/js/wpaz_date_calculator.js', __FILE__ ), array('jquery') );
		print __LINE__." is HAPPENING";
	}

	public function register_styles() {
		wp_register_style( 'wpazdc_style', plugins_url( '../public/css/wpaz_date_calculator.css', __FILE__ ) );
		print __LINE__." is HAPPENING";
	}
} // End Class