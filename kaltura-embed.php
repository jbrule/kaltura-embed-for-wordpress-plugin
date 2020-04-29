<?php
/*
Plugin Name: Kaltura Embed
Description: Adds Embed support for Kaltura Video Players. Includes support for secured content
Version: 1.0
Author: Joshua Brule
Network: false
*/

require_once('embed.php');

function kaltura_embed_activate($plugin, $network_wide){
    
    if($plugin === "kaltura-embed/kaltura-embed.php" && $network_wide){
        error_log( 'Plugin does not support network activation' );      
        $args = var_export( func_get_args(), true );
        error_log( $args );
        wp_die( 'Plugin does not support network activation' );
    }
    
}
add_action( 'activate_plugin', 'kaltura_embed_activate' , 10, 2);

function kaltura_embed_initialize(){
        
    new KalturaEmbed();
    
    if(is_admin()){//Only load settings in admin to lighten memory impact.
        require_once('settings.php');
        new KalturaEmbedSettings();
    }
}
add_action('plugins_loaded','kaltura_embed_initialize');
