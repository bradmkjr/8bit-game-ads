<?php

/*
Plugin Name: 8bit Game Ads
Plugin URI: https://github.com/bradmkjr/8bit-game-ads/
Description: Add 8bit Game Ads
Version: 1.8.6
Author: Bradford Knowlton
Author URI: http://bradknowlton.com/
License:           GNU General Public License v2
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
Domain Path:       /languages
Text Domain:       8bit-game-ads
*/

// set_include_path(get_include_path() . PATH_SEPARATOR . plugin_dir_path( __FILE__ ) . 'vendor/google/apiclient/src' );

include_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php');


use \DTS\eBaySDK\Shopping\Services;
use \DTS\eBaySDK\Shopping\Types;

// Create the service object.
$service = new Services\ShoppingService();

// Create the request object.
$request = new Types\GeteBayTimeRequestType();

// Send the request to the service operation.
$response = $service->geteBayTime($request);

// Output the result of calling the service operation.
printf("The official eBay time is: %s\n", $response->Timestamp->format('H:i (\G\M\T) \o\n l jS Y'));

die();