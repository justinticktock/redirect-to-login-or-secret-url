<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Append new links to the Plugin admin side
add_filter( 'plugin_action_links_' . RTLOSU::get_instance()->plugin_file , 'RTLOSU_plugin_action_links');

function RTLOSU_plugin_action_links( $links ) {

	if ( current_user_can( 'manage_options') ) {
		$RTLOSU = RTLOSU::get_instance();
		
		$settings_link = '<a href="options-general.php?page=' . $RTLOSU->menu . '">' . __( 'Settings', 'redirect-to-login-or-secret-url' ) . "</a>";
		
		array_push( $links, $settings_link );
	}
	return $links;	
}


	

// add action after the settings save hook.
add_action( 'tabbed_settings_after_update', 'RTLOSU_after_settings_update' );

function RTLOSU_after_settings_update( ) {

	flush_rewrite_rules();	
	
}


/**
 * RTLOSU_Settings class.
 *
 * Main Class which inits the CPTs and plugin
 */
class RTLOSU_Settings {

	// Refers to a single instance of this class.
    private static $instance = null;
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {
	}

		
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return   A single instance of this class.
     */
    public static function get_instance() {
		
		$RTLOSU = RTLOSU::get_instance();
		
		$config = array(
				'default_tab_key' => 'rtlosu_general',					// Default settings tab, opened on first settings page open.
				'menu_parent' => 'options-general.php',    								// menu options page slug name.( 'Null' to remove from the menu )
				'menu_access_capability' => 'manage_options',    				// menu options page access required capability
				'menu' => $RTLOSU->menu,    								// menu options page slug name.
				'menu_title' => $RTLOSU->menu_title,    					// menu options page slug name.
				'page_title' => $RTLOSU->page_title,    		// menu options page title.
				);

		$settings = 	apply_filters( 'RTLOSU_settings', 
										array(
											'rtlosu_general' => array(
												'access_capability' => 'manage_options',
												'title'             => __( 'General :', 'RTLOSU' ),
                                                                                                'description'       => __('Once enabled this plugin will redirect all non-logged in users to the login prompt. To enable '
                                                                                                                            . 'the secret pass through URL use these settings.', 'RTLOSU' ),
												'settings' 		=> array(														

																		array(
																			'name' 		=> 'RTLOSU_secret_key',
																			'std' 		=> md5( __FILE__ . get_current_blog_id( ) ),
																			'label' 	=> __( 'Define a secret key:', 'RTLOSU' ),
																			'desc'		=> __( 'Enter a unique secret key for use as a url argument.', 'RTLOSU' ),
																			),
																		array(
																			'name'              => 'RTLOSU_example_url',
																			'std'               => get_site_url(),
                                                                                                                                                        //'sanitize_callback' => 'esc_url',
																			'label'             => __( 'Calculate a new URL:', 'RTLOSU' ),
																			'desc'              => __( 'Enter the URL here that you want to secretly allow access to.</br></br>Now use the following URL with secret key added..</br><strong>' . add_query_arg( array( 'secret' => get_option('RTLOSU_secret_key') ), get_option('RTLOSU_example_url') ) . '</strong>', 'RTLOSU' ),
																			'type'              => 'field_textarea_option',
                                                                                                                                                        'columns'           => "80",
																			),
																		array(
																			'name'              => 'RTLOSU_enable_secret_url',
																			'std'               => false,
																			'label'             => __( 'Enable Secret URL(s):', 'RTLOSU' ),
																			'desc'              => __( 'Select this option to allow URLs with the secret argument to pass through.', 'RTLOSU' ),
																			'type'              => 'field_checkbox_option',
																			),                                 
																		),			
											),
											'RTLOSU_plugin_extension' => array(
													'access_capability' => 'install_plugins',
													'title' 		=> __( 'Plugin Suggestions', 'role_excluder' ),
													'description' 	=> __( 'Any of the following plugins will allow you to define new roles and capabilties for the site, only use one of these.  Selection of a plugin will prompt you through the installation and the plugin will be forced active while this is selected; deselecting will not remove the plugin, you will need to manually deactivate and un-install from the site/network.', 'role_excluder' ),					
													'settings' 		=> array(
																			array(		
																				),
																			),
												)
										
											)
									);
			
        if ( null == self::$instance ) {
            self::$instance = new Tabbed_Settings( $settings, $config );
        }

        return self::$instance;
 
    } 
}

/**
 * RTLOSU_Settings_Additional_Methods class.
 */
class RTLOSU_Settings_Additional_Methods {

}
		


// Include the Tabbed_Settings class.
if ( ! class_exists( 'Extendible_Tabbed_Settings' ) ) { 
	require_once( dirname( __FILE__ ) . '/class-tabbed-settings.php' );
}

// Create new tabbed settings object for this plugin..
// and Include additional functions that are required.
RTLOSU_Settings::get_instance()->registerHandler( new RTLOSU_Settings_Additional_Methods() );







		
?>