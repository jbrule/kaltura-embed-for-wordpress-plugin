<?php

class KalturaEmbedSettings{
    
    const page_title = "Kaltura Embed";
    const page_name = "kaltura_embed_settings";
    const option_group = "kaltura_embed_options";
    const option_section = "kaltura_embed_env_section";
    const option_name = "kaltura_embed_environments";
    
    public function __construct(){
        add_action('admin_init', array($this,'init'));//Initialize plugin settings pages
        add_action('admin_menu', array($this,'plugin_admin_add_page'));//Add the admin page to the dashboard
        add_action('wp_ajax_get_new_environment', array('KalturaEmbedSettings','get_new_environment_control_row_via_ajax'));
        wp_enqueue_script( 'settings-helper', plugins_url('js/settings-helper.js',__FILE__), [ 'wp-util' ] );
    }
    
    public function init(){
        add_settings_section(self::option_section, '', array($this,'environment_settings_text'), self::page_name);
        add_settings_field('kaltura_embed_env', __('Kaltura Environments'), array($this,'environment_settings_render'), self::page_name, self::option_section);
        register_setting( self::option_group, self::option_name);
    }
    
    //Adds menu item for dashboard gettings. Only accessible to those with manage sites privs (superadmins)
    public function plugin_admin_add_page() {
        add_options_page(self::page_title, self::page_title, 'manage_sites', self::page_name, array($this,'options_page'));
    }
    
    private static function get_environments(){
        return get_option(self::option_name);
    }
    
    public function environment_settings_text(){
        ?>
        <style>
            .kaltura-env-form label{
                display:inline-block;
                width:200px;
                text-align: right;
            }
            .kaltura-env-form input[type='text']{
                width:500px;
                margin:3px;
            }
            .kaltura-env-form div.env{
                padding:10px;
            }
            .kaltura-env-form div.env:not(:last-child){
                border-bottom:1px solid #ccc;
            }
        </style>
        <?php
    }
    
    public function environment_settings_render() {
        
        $option_environments = self::get_environments();
        
        if($option_environments == false || count($option_environments) == 0){
            $option_environments = [];
            //$option_environments[] = self::get_new_environment();
        }
        
        $index = 0;
        foreach($option_environments as $environment){
            echo self::get_environment_control_row($environment, $index);
            $index++;
        }
    }
    
    public function options_page() {
    ?>
        <div>
        <h2><?php echo self::page_title ?></h2>
        
        <form class="kaltura-env-form" action="options.php" method="post">
        <?php settings_fields(self::option_group); ?>
        <div id="kaltura-environments">
            <?php do_settings_sections(self::page_name); ?>
        </div>
        <input name="Add Another Environment" type="button" class="button" value="Add Another Environment" style="float:right" />
        <input name="Submit" class="button-primary" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form></div>
        <?php
    }
    
    public static function get_new_environment_control_row_via_ajax(){
        $environment = self::get_new_environment();
        
        $max_index = $_POST['max_index'];
        
        if(is_numeric($max_index)){
            $new_index = intval($max_index);
            $new_index++;
            
            wp_send_json_success(self::get_environment_control_row($environment,$new_index));
        }
    }
    
    private static function get_new_environment(){
        return ['name'=>'','urlPattern'=>'','partnerId'=>'','playerId'=>'','appTokenId'=>'','appTokenSecret'=>'','appTokenSessionDuration'=>''];
    }
    
    private static function get_environment_control_row($environment, $index){
        
        $output = "\n";
        
        $output .= "<div class='env' data-index='".$index."'>";
        $output .= "<input name='Remove' type='button' class='button' value='Remove' style='float:right'>";
        $output .= "<label>Name</label> <input id='".self::option_name."_".$index."_name' name='".self::option_name."[".$index."][name]' type='text' value='".$environment['name']."' /> No spaces<br />";
        $output .= "<label>UrlPattern</label> <input id='".self::option_name."_".$index."_urlPattern' name='".self::option_name."[".$index."][urlPattern]' type='text' value='{$environment['urlPattern']}' /> (Regular Expression) Must contain &quot;entryId&quot; named capture group<br />";
        $output .= "<label>Partner ID</label> <input id='".self::option_name."_".$index."_partnerId' name='".self::option_name."[".$index."][partnerId]' type='text' value='{$environment['partnerId']}' /><br />";
        $output .= "<label>Player ID</label> <input id='".self::option_name."_".$index."_playerId' name='".self::option_name."[".$index."][playerId]' type='text' value='{$environment['playerId']}' /><br />";
        $output .= "<label>AppToken ID</label> <input id='".self::option_name."_".$index."_appToken_id' name='".self::option_name."[".$index."][appTokenId]' type='text' value='{$environment['appTokenId']}' /><br />";
        $output .= "<label>AppToken Secret</label> <input id='".self::option_name."_".$index."_appToken_secret' name='".self::option_name."[".$index."][appTokenSecret]' type='text' value='{$environment['appTokenSecret']}' /><br />";
        $output .= "<label>AppToken Session Duration</label> <input id='".self::option_name."_".$index."_appToken_sessionduration' name='".self::option_name."[".$index."][appTokenSessionDuration]' type='text' value='{$environment['appTokenSessionDuration']}' /> (seconds)<br />";
        $output .= "</div>";
        
        return $output;
    }
}