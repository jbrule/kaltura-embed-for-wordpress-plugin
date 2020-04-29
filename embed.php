<?php

//Load Kaltura SDK
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'kaltura-sdk'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');

use Kaltura\Client\Configuration as KalturaConfiguration;
use Kaltura\Client\Client as KalturaClient;
use Kaltura\Client\Enum\SessionType;
use Kaltura\Client\Type\{AppToken,FilterPager};
use Kaltura\Client\ApiException;

class KalturaEmbed{
    const option_name = "kaltura_embed_environments";
    
    public function __construct(){
        $option_environments = get_option(self::option_name);
        
        if($option_environments !== false && is_array($option_environments)){
            foreach($option_environments as $environment){
                $this->registerEmbed($environment);
            }
        }
    }
    
    private function registerEmbed($configSetting){
        
        wp_embed_register_handler($configSetting['name'],"#{$configSetting['urlPattern']}#i",function( $matches, $attr, $url, $rawattr ) use ($configSetting){
            
            if(array_key_exists("entryId",$matches)){
                $entryId = $matches["entryId"];
            }else{
                return "Kaltura Embed Error: &quot;entryId&quot; not found";
            }
            
            $embedSettings = [];
            $embedSettings['targetId'] = "kaltura_player_{$entryId}";
            $embedSettings['wid'] = "_{$configSetting['partnerId']}";
            $embedSettings['uiconf_id'] = $configSetting['playerId'];
            $embedSettings['flashvars'] = new stdClass();
            $embedSettings['entry_id'] = $entryId;
            
            if(!empty($configSetting['appTokenSecret'])){
                $sessionInfo = $this->getSessionViaAppToken($configSetting);
                if($sessionInfo){
                    $embedSettings['flashvars']->ks = $sessionInfo->ks;
                }
            }
                        
            $embed = sprintf('<script src="https://cdnapisec.kaltura.com/p/%1$s/sp/%1$s00/embedIframeJs/uiconf_id/%2$s/partner_id/%1$s"></script>'.
                             '<div style="max-width:720px;"><div style="position:relative;padding-bottom:56.25%%">'.
                             '<div id="kaltura_player_%3$s" style="position:absolute;top:0;left:0;width:100%%;height:100%%"></div>'.
                             '</div>'.
                             '<script>'.
                             'kWidget.embed(%4$s);'.
                             '</script>',
                             $configSetting['partnerId'],$configSetting['playerId'],$entryId,json_encode($embedSettings,JSON_PRETTY_PRINT));
                        
            return apply_filters( "embed_{$configSetting['name']}", $embed, $matches, $attr, $url, $rawattr );
        });
    }
        
    private function getSessionViaAppToken($configSetting){
                
        //Initialize Client
        $kConfig = new KalturaConfiguration();
        $client = new KalturaClient($kConfig);
        
        //Setup widgetId. This value is the Kaltura partnerID prefixed with an underscore.
        $widgetId = "_{$configSetting['partnerId']}";
        //KS Time To Live. The AppToken is set to 24hrs. Setting here can override the appToken setting.
        $expiry = $configSetting['appTokenSessionDuration'];
        
        try {
            $widgetSession = $client->getSessionService()->startWidgetSession($widgetId, $expiry);
            $widgetKs = $widgetSession->ks;
            
            $client->setKS($widgetKs);
            $tokenHash = hash('sha256', $widgetKs . $configSetting['appTokenSecret']);
                    
            try{
                //Start AppToken based session. Passing in our desired userId.
                $result = $client->appToken->startSession($configSetting['appTokenId'], $tokenHash);
                return $result;
            }
            catch(Exception $e){
                //Log/Handle
                echo $e->getMessage();
                return false;
            }
            
        } catch (Exception $e) {
            //Log/Handle
            echo $e->getMessage();
            return false;
        }
    }
}