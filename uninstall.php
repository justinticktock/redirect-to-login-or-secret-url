<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ( );
}
	
if ( is_multisite( ) ) {

    $blogs = wp_list_pluck( wp_get_sites(), 'blog_id' );

    if ( $blogs ) {
        foreach( $blogs as $blog ) {
            switch_to_blog( $blog );
            RTLOSU_clean_database( );
            //RTLOSU_clean_database_roles( );
        }
        restore_current_blog( );
    }
} else {

        RTLOSU_clean_database( );
        //RTLOSU_clean_database_roles( );
}
		
// remove all database entries for currently active blog on uninstall.
function RTLOSU_clean_database( ) {
		
		delete_option( 'RTLOSU_plugin_version' );
		delete_option( 'RTLOSU_install_date' );

		// plugin specific database entries
		delete_option( 'RTLOSU_enable_secret_url' );
		delete_option( 'RTLOSU_example_url' );
		delete_option( 'RTLOSU_secret_key' );
		
		// user specific database entries
		delete_user_meta( get_current_user_id( ), 'RTLOSU_prompt_timeout', $meta_value );
		delete_user_meta( get_current_user_id( ), 'RTLOSU_start_date', $meta_value );
		delete_user_meta( get_current_user_id( ), 'RTLOSU_hide_notice', $meta_value );

		// plugin licensing database entries
		//delete_option( 'RTLOSU_licensing_activation_button' );
		//delete_option( 'RTLOSU_license_status' );
		//delete_option( 'RTLOSU_license_key' );
}

// loop all roles that could have options
function RTLOSU_clean_database_roles( ) {

    global $wp_roles;

    if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles( );
    }

    $roles = $wp_roles->get_names( );
    unset( $wp_roles );
    asort( $roles );

    $option_default = array();
    foreach( $roles as $role_key=>$role_name )
    {
        delete_option( 'RTLOSU_roles_' . $role_key );
    }
    return $option_default;
}

?>