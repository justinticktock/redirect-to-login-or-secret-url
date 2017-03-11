<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include the TGM_Plugin_Activation class.
require_once( dirname( __FILE__ ) . '/class-tgm-plugin-activation.php' );

add_action( 'tgmpa_register', 'RTLOSU_tgmpa_register' );


function RTLOSU_tgmpa_register( ) {

	$plugins = RTLOSU_Settings::get_instance()->selected_plugins( 'RTLOSU_plugin_extension' );
        $plugins = array_filter( $plugins ); // Remove any empty array items.
        
        if ( ! $plugins ) {
            return;
        }
	
	$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
					'page_title'                      => __( 'Install Required Plugins', 'user-upgrade-capability' ),
					'menu_title'                      => __( 'Install Plugins', 'user-upgrade-capability' ),
					'installing'                      => __( 'Installing Plugin: %s', 'user-upgrade-capability' ), // %s = plugin name.
					'oops'                            => __( 'Something went wrong with the plugin API.', 'user-upgrade-capability' ),
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'user-upgrade-capability' ), // %1$s = plugin name(s).
					'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'user-upgrade-capability' ),
					'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'user-upgrade-capability' ),
					'return'                          => __( 'Return to Required Plugins Installer', 'user-upgrade-capability' ),
					'dashboard'                      => __( 'Return to the dashboard', 'user-upgrade-capability' ),
					'plugin_activated'                => __( 'Plugin activated successfully.', 'user-upgrade-capability' ),
					'activated_successfully'         => __( 'The following plugin was activated successfully:', 'user-upgrade-capability' ),
					'complete'                        => __( 'All plugins installed and activated successfully. %s', 'user-upgrade-capability' ), // %s = dashboard link.
					'dismiss'                        => __( 'Dismiss this notice', 'user-upgrade-capability' ),
					'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
	);


	tgmpa( $plugins, $config );

}



?>