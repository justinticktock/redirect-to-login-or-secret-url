<?php
/*
Plugin Name: Redirect To Login or Secret URL
Plugin URI: http://justinandco.com/plugins/
Description: Allow users to pass through when a secrete key is provided as an argument within the URL
Version: 1.0
Author: Justin Fletcher
Author URI: http://justinandco.com
Text Domain: redirect-to-login-or-secret-url
Domain Path: /languages/
License: GPLv2 or later
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * redirect-to-login-or-secret-url class.
 */
class RTLOSU {

    // Refers to a single instance of this class.
    private static $instance = null;
	
    public	 $plugin_full_path;
    public   $plugin_file = 'redirect-to-login-or-secret-url/redirect-to-login-or-secret-url.php';
	
	// Settings page slug	
    public	 $menu = 'redirect-to-login-or-secret-url-settings';
	
	// Settings Admin Menu Title
    public	 $menu_title = 'Login / Secret URL';
	
	// Settings Page Title
    public	 $page_title = 'Login / Secret URL';
    
    /**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Load the textdomain.
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 1 );

		// Set the constants needed by the plugin.
		add_action( 'plugins_loaded', array( $this, 'constants' ), 2 );
		
		// Load the functions files.
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

		// Attached to after_setup_theme. Loads the plugin installer CLASS after themes are set-up to stop duplication of the CLASS.
		// this should remain the hook until TGM-Plugin-Activation version 2.4.0 has had time to roll out to the majority of themes and plugins.
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ));
		
		// register admin side - upgrade routine and menu item.
		add_action( 'admin_init', array( $this, 'admin_init' ));

		// Load admin error messages	
		add_action( 'admin_init', array( $this, 'deactivation_notice' ));
		add_action( 'admin_notices', array( $this, 'action_admin_notices' ));

	}
	
	/**
	 * Defines constants used by the plugin.
	 *
	 * @return void
	 */
	public function constants() {

		// Define constants
		define( 'RTLOSU_MYPLUGINNAME_PATH', plugin_dir_path( __FILE__ ) );
		define( 'RTLOSU_MYPLUGINNAME_FULL_PATH', RTLOSU_MYPLUGINNAME_PATH . 'redirect-to-login-or-secret-url.php' );
		define( 'RTLOSU_PLUGIN_DIR', trailingslashit( plugin_dir_path( RTLOSU_MYPLUGINNAME_PATH )));
		define( 'RTLOSU_PLUGIN_URI', plugins_url('', __FILE__) );
		
		// admin prompt constants
		define( 'RTLOSU_PROMPT_DELAY_IN_DAYS', 30);
		define( 'RTLOSU_PROMPT_ARGUMENT', 'RTLOSU_hide_notice');
		
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @return void
	 */
	public function includes() {

		// settings 
		require_once( RTLOSU_MYPLUGINNAME_PATH . 'includes/settings.php' );  

		// include the role limiting
		require_once( RTLOSU_MYPLUGINNAME_PATH . 'includes/class-redirect-to-login-or-secret-url.php' );
		
		// auto updater
		//require_once( RTLOSU_MYPLUGINNAME_PATH . 'includes/RTLOSU_updater.php' );
				
	}
	
	/**
	 * Initialise the plugin installs
	 *
	 * @return void
	 */
	public function after_setup_theme() {

		// install the plugins and force activation if they are selected within the plugin settings
		require_once( RTLOSU_MYPLUGINNAME_PATH . 'includes/plugin-install.php' );
		
	}

        
    /**
	 * Initialise the plugin menu. 
	 *
	 * @return void
	 */
	public function admin_menu() {

	}
    
	/**
	 * sub_menu_page: 
	 *
	 * @return void
	 */
	public function sub_menu_page() {
		// 
	}	
	
	/**
	 * Initialise the plugin by handling upgrades and loading the text domain. 
	 *
	 * @return void
	 */
	public function admin_init() {
	
		//Registers user installation date/time on first use
		$this->action_init_store_user_meta();
		
		$plugin_current_version = get_option( 'RTLOSU_plugin_version' );
		$plugin_new_version =  self::plugin_get_version();
		
		// Admin notice hide prompt notice catch
		$this->catch_hide_notice();

		//if ( empty($plugin_current_version) || $plugin_current_version < $plugin_new_version ) {
		if ( version_compare( $plugin_current_version, $plugin_new_version, '<' ) ) {
		
			$plugin_current_version = isset( $plugin_current_version ) ? $plugin_current_version : 0;

			$this->RTLOSU_upgrade( $plugin_current_version );

			// set default options if not already set..
			$this->do_on_activation();

			// create the plugin_version store option if not already present.
			$plugin_version = self::plugin_get_version();
			update_option('RTLOSU_plugin_version', $plugin_version ); 
			
			// Update the option again after RTLOSU_upgrade() changes and set the current plugin revision	
			update_option('RTLOSU_plugin_version', $plugin_new_version ); 
		}
	}
	
	/**
	 * Loads the text domain.
	 *
	 * @return void
	 */
	public function i18n( ) {
		$ok = load_plugin_textdomain( 'redirect-to-login-or-secret-url', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}
		
	/**
	 * Provides an upgrade path for older versions of the plugin
	 *
	 * @param float $current_plugin_version the local plugin version prior to an update 
	 * @return void
	 */
	public function RTLOSU_upgrade( $current_plugin_version ) {
		
		/*
		// upgrade code when required.
		if ( $current_plugin_version < '1.0' ) {

			delete_option('XXXXXX');

		}
		*/
	}

	/**
	 * Flush your rewrite rules for plugin activation and initial install date.
	 *
	 * @access public
	 * @return $settings
	 */	
	static function do_on_activation() {

		// Record plugin activation date.
		add_option('RTLOSU_install_date',  time() ); 

	}

	/**
	 * remove the reference site option setting for safety when re-activating the plugin
	 *
	 * @access public
	 * @return $settings
	 */	
	static function do_on_deactivation() {

		//delete_option('RTLOSU_reference_site' );
	}
	
	/**
	 * Returns current plugin version.
	 *
	 * @access public
	 * @return $plugin_version
	 */	
	static function plugin_get_version() {

		$plugin_data = get_plugin_data( RTLOSU_MYPLUGINNAME_FULL_PATH, false, false );	

		$plugin_version = $plugin_data['Version'];	
		return filter_var($plugin_version, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
	
	/**
	 * Register Plugin Deactivation Hooks for all the currently 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public function deactivation_notice() {

		// loop plugins forced active.
		$plugins = RTLOSU_Settings::get_instance()->selected_plugins( 'RTLOSU_plugin_extension' );

		foreach ( $plugins as $plugin ) {
			$plugin_file = RTLOSU_PLUGIN_DIR . $plugin["slug"] . '\\' . $plugin['slug'] . '.php' ;
			register_deactivation_hook( $plugin_file, array( 'redirect-to-login-or-secret-url', 'on_deactivation' ) );
		}
	}

	/**
	 * This function is hooked into plugin deactivation for 
	 * enforced active extension plugins.
	 *
	 * @access public
	 * @return null
	 */
	public static function on_deactivation()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
	
		$plugin_slug = explode( "/", $plugin);
		$plugin_slug = $plugin_slug[0];
		update_option( "RTLOSU_deactivate_{$plugin_slug}", true );
    }
	
	/**
	 * Display the admin warnings.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_notices() {

		// loop plugins forced active.
		$plugins = RTLOSU_Settings::get_instance()->selected_plugins( 'RTLOSU_plugin_extension' );

		// for each extension plugin enabled (forced active) add a error message for deactivation.
		foreach ( $plugins as $plugin ) {
			$this->action_admin_plugin_forced_active_notices( $plugin["slug"] );
		}
		
		// Prompt for rating
		//$this->action_admin_rating_prompt_notices();
	}
	
	/**
	 * Display the admin error message for plugin forced active.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_plugin_forced_active_notices( $plugin ) {
	
		$plugin_message = get_option("RTLOSU_deactivate_{$plugin}");
		if ( ! empty( $plugin_message ) ) {
			?>
			<div class="error">
				  <p><?php esc_html_e(sprintf( __( 'Error the %1$s plugin is forced active with ', 'redirect-to-login-or-secret-url'), $plugin)); ?>
				  <a href="options-general.php?page=<?php echo $this->menu ; ?>&tab=RTLOSU_plugin_extension"> <?php echo esc_html(__( 'Login / Secret URL Settings!', 'redirect-to-login-or-secret-url')); ?> </a></p>
			</div>
			<?php
			update_option("RTLOSU_deactivate_{$plugin}", false); 
		}
	}

		
	/**
	 * Store the current users start date
	 *
	 * @access public
	 * @return null
	 */
	public function action_init_store_user_meta() {
		
		// store the initial starting meta for a user
		add_user_meta( get_current_user_id(), 'RTLOSU_start_date', time(), true );
		add_user_meta( get_current_user_id(), 'RTLOSU_prompt_timeout', time() + 60*60*24*  RTLOSU_PROMPT_DELAY_IN_DAYS, true );

	}

	/**
	 * Display the admin message for plugin rating prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function action_admin_rating_prompt_notices( ) {

		$user_responses =  array_filter( (array)get_user_meta( get_current_user_id(), RTLOSU_PROMPT_ARGUMENT, true ));	
		if ( in_array(  "done_now", $user_responses ) ) 
			return;

		if ( current_user_can( 'install_plugins' ) ) {
			
			$next_prompt_time = get_user_meta( get_current_user_id(), 'RTLOSU_prompt_timeout', true );
			if ( ( time() > $next_prompt_time )) {
				$plugin_user_start_date = get_user_meta( get_current_user_id(), 'RTLOSU_start_date', true );
				?>
				<div class="update-nag">
					
					<p><?php esc_html(printf( __("You've been using <b>Login / Secret URL</b> for more than %s.  How about giving it a review by logging in at wordpress.org ?", 'redirect-to-login-or-secret-url'), human_time_diff( $plugin_user_start_date) )); ?>
				
					</p>
					<p>

						<?php echo '<a href="' .  esc_url(add_query_arg( array( RTLOSU_PROMPT_ARGUMENT => 'doing_now' )))  . '">' .  esc_html__( 'Yes, please take me there.', 'redirect-to-login-or-secret-url' ) . '</a> '; ?>
						
						| <?php echo ' <a href="' .  esc_url(add_query_arg( array( RTLOSU_PROMPT_ARGUMENT => 'not_now' )))  . '">' .  esc_html__( 'Not right now thanks.', 'redirect-to-login-or-secret-url' ) . '</a> ';?>
						
						<?php
						if ( in_array(  "not_now", $user_responses ) || in_array(  "doing_now", $user_responses )) { 
							echo '| <a href="' .  esc_url(add_query_arg( array( RTLOSU_PROMPT_ARGUMENT => 'done_now' )))  . '">' .  esc_html__( "I've already done this !", 'redirect-to-login-or-secret-url' ) . '</a> ';
						}?>

					</p>
				</div>
				<?php
			}
		}	
	}
	
	/**
	 * Store the user selection from the rate the plugin prompt.
	 *
	 * @access public
	 * @return null
	 */
	public function catch_hide_notice() {
	
		if ( isset($_GET[RTLOSU_PROMPT_ARGUMENT]) && $_GET[RTLOSU_PROMPT_ARGUMENT] && current_user_can( 'install_plugins' )) {
			
			$user_user_hide_message = array( sanitize_key( $_GET[RTLOSU_PROMPT_ARGUMENT] )) ;				
			$user_responses =  array_filter( (array)get_user_meta( get_current_user_id(), RTLOSU_PROMPT_ARGUMENT, true ));	

			if ( ! empty( $user_responses )) {
				$response = array_unique( array_merge( $user_user_hide_message, $user_responses ));
			} else {
				$response =  $user_user_hide_message;
			}
			
			check_admin_referer();	
			update_user_meta( get_current_user_id(), RTLOSU_PROMPT_ARGUMENT, $response );

			if ( in_array( "doing_now", (array_values((array)$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  RTLOSU_PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'RTLOSU_prompt_timeout' , $next_prompt_time );
				wp_redirect( 'http://wordpress.org/support/view/plugin-reviews/user-upgrade-capability' );
				exit;					
			}

			if ( in_array( "not_now", (array_values((array)$user_user_hide_message ))))  {
				$next_prompt_time = time() + ( 60*60*24*  RTLOSU_PROMPT_DELAY_IN_DAYS ) ;
				update_user_meta( get_current_user_id(), 'RTLOSU_prompt_timeout' , $next_prompt_time );		
			}
				
				
			wp_redirect( remove_query_arg( RTLOSU_PROMPT_ARGUMENT ) );
			exit;		
		}
	}
	
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }		
}

/**
 * Init RTLOSU class
 */
 
RTLOSU::get_instance();

register_deactivation_hook( __FILE__, array( 'redirect-to-login-or-secret-url', 'do_on_deactivation' ) );

?>