<?php
/*
Plugin Name: WP CrowdProcess
Plugin URI: http://vanilla-lounge.pt/
Description: CrowdProcess is a distributed supercomputer. Its computing power comes from visitors' browsers. This plugin installs a CrowdProcess "worker" on your WordPress site. If you still do not understand how CrowdProcess works, you can check <a href="http://crowdprocess.com/#home-how">the overview</a>.
Version: 1.0
Author: vanillalounge
Author URI: http://vanilla-lounge.pt/
License: GPL2
*/

/**
 * WP_CrowdProcess class
 *
 * This plugin is a class because reasons, #blamenacin
 *
 */
if ( !class_exists( 'wp_crowdprocess' ) ) {

	class wp_crowdprocess {

		// Holds the api key value
		var $api_key;

		// instance
		static $instance;

		/**
		 * Add init hooks on class construction
		 */
		function wp_crowdprocess() {

			// allow this instance to be called from outside the class
			self::$instance = $this;

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		}

		/**
		 * Init callback 
		 * 
		 * Load translations and add iframe code, if present
		 *
		 */
		function init() {

			load_plugin_textdomain( 'wp-crowdprocess', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			$wp_crowdprocess_vars = get_option( 'wp_crowdprocess_vars' );

			if ( !empty( $wp_crowdprocess_vars['apikey'] ) ) {
				$this->api_key = $wp_crowdprocess_vars['apikey'];
				add_action( 'wp_footer', array( $this, 'add_code' ), 999 );
			}
		}


		/**
		 * Admin init callback
		 * 
		 * Register options, add settings page
		 *
		 */
		function admin_init() {

			register_setting(
				'wp_crowdprocess_vars_group',
				'wp_crowdprocess_vars',
				array( $this, 'validate_form' ) );

			add_settings_section(
				'wp_crowdprocess_vars_id',
				__( 'Instructions', 'wp-crowdprocess' ),
				array( $this, 'overview' ),
				'WP CrowdProcess Settings' );

			add_settings_field(
				'wpcp-apikey',
				__( 'API Key:', 'wp-crowdprocess' ),
				array( $this, 'render_field' ),
				'WP CrowdProcess Settings',
				'wp_crowdprocess_vars_id' );
		}

		/**
		 * Build the menu and settings page callback
		 * 
		 */
		function admin_menu() {

			if ( !function_exists( 'current_user_can' ) || !current_user_can( 'manage_options' ) )
				return;

			if ( function_exists( 'add_options_page' ) )
				add_options_page( __( 'WP CrowdProcess Settings', 'wp-crpwdprocess' ), __( 'WP CrowdProcess', 'wp-crpwdprocess' ), 'manage_options', 'wp_crowdprocess', array( $this, 'show_form' ) );
		}

		/**
		 * Show instructions
		 * 
		 */
		function overview() {

			printf( __( '<p>In order for this plugin to function, you need to have a valid CrowdProcess API key. Please log in to your CrowdProcess account and either create a new API key or retrieve an existing one, from your <a href="%1$s" target="_blank">administration area</a>. The value you need to enter below is just the key itself, usually in a form similar to <strong>YTD8qqYY</strong>.</p>', 'wp-crowdprocess' ), 'http://crowdprocess.com/#admin' );

			_e( '<p>Please make sure that you are <strong>not pasting the whole line of code</strong> on the field below.</p>', 'wp-crowdprocess' ) . '</p>';

		}

		/**
		 * Render options field
		 * 
		 */ 
		function render_field() {
			$wp_crowdprocess_vars = get_option( 'wp_crowdprocess_vars' );

?>
                                <input id="wpcp-apikey" name="wp_crowdprocess_vars[apikey]" class="regular-text" value="<?php echo $wp_crowdprocess_vars['apikey']; ?>" />
                        <?php
		}

		/**
		 * Validate user options
		 * 
		 */ 
		function validate_form( $input ) {

			$wp_crowdprocess_vars = get_option( 'wp_crowdprocess_vars' );

			if ( isset( $input['apikey'] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings
				$wp_crowdprocess_vars['apikey'] = strip_tags( stripslashes( $input['apikey'] ) );

			}
			return $wp_crowdprocess_vars;
		}

		/**
		 * Render options page
		 * 
		 */ 
		function show_form() {
			$wp_crowdprocess_vars = get_option( 'wp_crowdprocess_vars' );

?>
                                <div class="wrap">
                                        <?php screen_icon( "options-general" ); ?>
                                        <h2><?php _e( 'WP CrowdProcess Settings', 'wp-crowdprocess' ); ?></h2>
                                        <form action="options.php" method="post">
                                                <?php settings_fields( 'wp_crowdprocess_vars_group' ); ?>
                                                <?php do_settings_sections( 'WP CrowdProcess Settings' ); ?>
                                                <p class="submit">
                                                        <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'wp-crowdprocess' ); ?>" />
                                                </p>
                                        </form>
                                </div>
                        <?php
		}

		/**
		 * Add iframe code to the site's footer
		 * 
		 */ 
		function add_code() {
			echo "\n";
			echo '<iframe src="http://as.crowdprocess.com/#/' . sanitize_text_field( $this->api_key ) . '" style="display:none"></iframe> ';
			echo "\n";
		}


	}

	new wp_crowdprocess();
}
