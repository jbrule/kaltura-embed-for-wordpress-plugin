# Kaltura Embed for WordPress Plugin
Contributors: Joshua Brule (https://github.com/jbrule)

Requires at least: 5.1

Tested up to: 5.2

Requires PHP: 7.2

## Description
Adds Embed support for Kaltura Video Players. Includes support for secured content via injection of Kaltura Session token (ks). Requires a playback appToken.

Network Activation is not supported at this time.

Only supports Saas hosted Kaltura at this time.

## Installation
1. Clone repo to a location on local workstation. `git clone --recurse-submodules https://github.com/jbrule/kaltura-embed-for-wordpress-plugin kaltura-embed`
2. Install autoloader for Kaltura SDK by navigating to `kaltura-sdk` directory and run `composer install`
3. Upload to plugins on WordPress server.
4. Activate on Site

## Configuration
1. Configuration Settings can be found under Settings -> Kaltura Embed in site dashboard.
2. Fill in settings with known values. The UrlPattern should be a reqular expression that matches the path of your MediaSpace instance. It also needs to capture the entryId of the video in a named capture group. Example `https?://mymediaspace\.school\.edu/(?:media|id)/.*(?P<entryId>\d_[a-z0-9]{8}).*`
3. To support *Require KS* type access control. You will need to [generate an appToken](https://developer.kaltura.com/api-docs/VPaaS-API-Getting-Started/application-tokens.html) with `setrole:PLAYBACK_BASE_ROLE,widget:1,sview:*` sessionPrivileges. Making sure sessionDuration is at least as long as server/client site page caching.
