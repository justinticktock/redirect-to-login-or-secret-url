<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * RTLOSU_CLASS class.
 */
class RTLOSU_CLASS {

	// Refers to a single instance of this class.
    private static $instance = null;

	
    /**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		add_action( 'parse_request', array( $this, 'redirect_to_login_if_not_member_of_site' ), 1 );
		
		add_action( 'init', array( $this, 'secret_url_request' ) );

		add_filter( 'login_url', array( $this, 'strip_loggedout' ), 1, 1 ); 
	

	}
		
	/**
	 * Redirects a user to the login page if not logged in to the current site
	 *
	 * @author Daan Kortenbach
	 */
	public function redirect_to_login_if_not_member_of_site() {

                $user = wp_get_current_user( );
                // if no cababilities assigned to the user for this site dropout
                if ( ! $user->allcaps ) {
                    //die(var_dump( $user->allcaps ));
                    
                    // clean up full removal from site
                    $user->remove_all_caps();
                    wp_die("You don't have permission to access this site !");
                }

/*
                if ( ! is_user_member_of_blog( ) ) { //&& ! ( $GLOBALS['pagenow'] === 'wp-login.php' ) ) {
                    
                        wp_die("You don't have permission to access this site !");
                      
                                                                // if not a member on this site the user can still be logged in 
                                                                // if they are already logged in elsewhere in a network
                                                                if ( is_user_logged_in( ) ) {




                                                                    // redirect the logged in user to their primary newtwork site.

                                                                    global $blog_id;
                                                                    $user = wp_get_current_user();

                                                                    if ( !is_wp_error( $user ) && $user->ID != 0 ) {
                                        //die(var_dump($user->primary_blog)); 
                                                                        if ( $user->primary_blog )
                                                                        {
                                                                            $primary_url = get_blogaddress_by_id( $user->primary_blog );
                                                                            $user_blogs = get_blogs_of_user( $user->ID );


                                                                            //Loop and see if user has access
                                                                            $allowed = false;
                                                                            foreach( $user_blogs as $user_blog )
                                                                            {
                                                                                if( $user_blog->userblog_id == $blog_id )
                                                                                {
                                                                                    $allowed = true;
                                                                                    break;
                                                                                }
                                                                            }

                                                                            //Let users login to others blog IF we can get their primary blog URL and they are not allowed on this blog
                                                                            if ( $primary_url && !$allowed )
                                                                            {
                                                                                wp_redirect( $primary_url );
                                                                                die();
                                                                            }
                                                                        }
                                                                    }

                                                                } else {

                                                                        // otherwise the user is not logged in 
                                                                        // 
                                                                    //if ( true || ! ( $GLOBALS['pagenow'] === 'wp-login.php' ) ) {
                                                                        global $wp;
                                                                        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

                                                                       // $login_url = wp_login_url( );


                                                                        // un-register action hook into  'parse_request' stops an infinite cycle.
                                                                        //remove_action( 'parse_request', array( $this, 'redirect_to_login_if_not_member_of_site' ), 1 );
                                                                        wp_redirect( wp_login_url( $current_url ) );
                                                                      //  remove_action( $tag, $function_to_remove, $priority ); 

                                                                            //die( $login_url );
                                                                        exit;

                                                                }
                       
                 
                }*/
	}
	
		
	/**
	 * Strips '?loggedout=true' from redirect url after login.
	 *
	 * @author Daan Kortenbach
	 *
	 * @param  string $login_url
	 * @return string $login_url
	 */
	public function strip_loggedout( $login_url ) {
		return str_replace( '%3Floggedout%3Dtrue', '', $login_url );
	}



	/**
	 * if not logged in however has the secrete argument in the url then allow to pass
	 */
	public function secret_url_request() {

    
            // if a secret string is provided in the url then remove the requirement to be logged in
            if ( isset( $_GET['secret'] ) ) {  
                    
                $secret = $_GET['secret'];

                if ( ( $_GET['secret'] === get_option('RTLOSU_secret_key') ) && get_option('RTLOSU_enable_secret_url') ) {
                    remove_action( 'parse_request', array( $this, 'redirect_to_login_if_not_member_of_site' ), 1 );
                }
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
 * Init URE_OVERRIDE class
 */
 
RTLOSU_CLASS::get_instance();


?>