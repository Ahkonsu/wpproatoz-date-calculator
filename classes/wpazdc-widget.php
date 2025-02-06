<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPAZ_DateCalc_Widgets Class
 *
 * @class WPAZ_DateCalc_Widgets
 * @version	1.0
 * @since 1.0
 * @package	WPAZ_DateCalc
 * @author dewolfe001
 */

class WPAZ_DateCalc_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'WPAZ_DateCalc_Widget', 
			__('Date Calculator Widget', 'wpazdc'), 
			array( 'description' => __( 'Simple Widget To Display The Calculator' ), 'wpazdc') 
		);

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );


    	add_action( 'init', array($this,'register_resources') );

		// Refreshing the widget's cached output with each new post
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

    public function register_resources() {
		wp_register_script( 'wpazdc_script', plugins_url( '../public/js/wpaz_date_calculator.js', __FILE__ ), array('jquery') );
		wp_register_style( 'wpazdc_style', plugins_url( '../public/css/wpaz_date_calculator.css', __FILE__ ) );
    }

	public function flush_widget_cache() {
    	wp_cache_delete( 'WPAZ_DateCalc_Widget', 'widget' );
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		 
		// before and after widget arguments are defined by themes
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$WPAZ_DateCalc = new WPAZ_DateCalc();

		wp_enqueue_script( 'wpazdc_script' );
		wp_enqueue_style( 'wpazdc_style' );
		echo $WPAZ_DateCalc->calculator(); 
		echo $args['after_widget'];
	}

	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'wpazdc' );
		}
	// Widget admin form  ?>
	<!-- the title -->
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"	/></p>
	<!-- the details -->

	<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	} // end deactivate

	public function register_scripts() {
		wp_register_script( 'wpazdc_script', plugins_url( '../public/js/wpaz_date_calculator.js', __FILE__ ), array('jquery') );
		print __LINE__." is HAPPENING";
	}

	public function register_styles() {
		wp_register_style( 'wpazdc_style', plugins_url( '../public/css/wpaz_date_calculator.css', __FILE__ ) );
		print __LINE__." is HAPPENING";
	}
}