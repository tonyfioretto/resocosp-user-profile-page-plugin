<?php 
/*
	Plugin Name: ReSoCoSp User Profile Page
	Description: "ReSoCoSp User Profile Page" gestisce le operazioni degli utenti del sito relative alla propre ed altrui pagine del profilo.
	Version: 0.1
	Author: Progetto Resocosp
	Author URI: http://samuelestrappa.wordpress.com
*/


define( 'USERPROFILEPAGE_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once( USERPROFILEPAGE_PLUGIN_DIR . 'resocosp-user-profile-page.class.php' );

register_activation_hook( __FILE__, array( 'UserProfilePage', 'activation'));
register_deactivation_hook( __FILE__, array( 'UserProfilePge', 'deactivation'));

new UserProfilePage();

?>