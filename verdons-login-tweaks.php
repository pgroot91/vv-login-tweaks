<?php

/* 
Plugin Name: Verdon's Login Tweaks
Description: A few tweaks to the default login form
*/



/* change the login message */
function vv_login_message( $message ) {
    if ( empty($message) ){
        return "<p><strong>Welcome. Please be careful with your login. <span style='color:#f00;'>Three consecutive failures will result in a firewall block.</span> If you have forgotten your password, please use the 'Lost your password' link below to reset your password before your trigger a block.</strong></p>";
    } else {
        return $message;
    }
}
add_filter( 'login_message', 'vv_login_message' );



/* change the login logo */
function vv_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo plugins_url(); ?>/vv-login-tweaks/img/nu_80x80.png);
            padding-bottom: 5px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'vv_login_logo' );



/* change the login logo url */
function vv_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'vv_login_logo_url' );



/* redirect to home after login for non-admins */
function vv_admin_login_redirect( $redirect_to, $request, $user ) {
	global $user;
	if( isset( $user->roles ) && is_array( $user->roles ) ) {
		if( in_array( "administrator", $user->roles ) ) {
			return $redirect_to;
		} else {
			return home_url();
		}
	} else {
		return $redirect_to;
	}
}
add_filter("login_redirect", "vv_admin_login_redirect", 10, 3);



/* THE FOLLOWING STUFF WAS SCOOPED FROM THIS PLUGIN AND FUNCTIONS CHANGED TO MY NAMESPACE */

/**
 * Plugin Name: Multisite: Passwort Reset on Local Blog
 * Plugin URI:  https://gist.github.com/eteubert/293e07a49f56f300ddbb
 * Description: By default, WordPress Multisite uses the main blog for passwort resets. This plugin enables users to stay in their blog during the whole reset process.
 * Version:     1.0.0
 * Author:      Eric Teubert
 * Author URI:  http://ericteubert.de
 * License:     MIT
 */



// fixes "Lost Password?" URLs on login page
function vv_fix_lostpass_url($url, $redirect) { 
    $args = array( 'action' => 'lostpassword' );
    if (!empty($redirect) ) {
        $args['redirect_to'] = $redirect;
    }
    return add_query_arg( $args, site_url('wp-login.php') );
}
add_filter("lostpassword_url", "vv_fix_lostpass_url", 10, 2);



// fixes other password reset related urls
function vv_fix_passreset_links($url, $path, $scheme) {
    if (stripos($url, "action=lostpassword") !== false) {
        return site_url('wp-login.php?action=lostpassword', $scheme);
    }
    if (stripos($url, "action=resetpass") !== false) {
        return site_url('wp-login.php?action=resetpass', $scheme);
	}
    return $url;
}
add_filter( 'network_site_url', "vv_fix_passreset_links", 10, 3 );



// fixes URLs in email that goes out.
function vv_fix_pass_email_urls($message, $key) {
    return str_replace(get_site_url(1), get_site_url(), $message);
}
add_filter("retrieve_password_message", "vv_fix_pass_email_urls", 10, 2);



// fixes email title
function vv_fix_pass_email_title($title) {
    return "[" . wp_specialchars_decode(get_option('blogname'), ENT_QUOTES) . "] Password Reset";
}
add_filter("retrieve_password_title", "vv_fix_pass_email_title");


?>