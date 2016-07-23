<?php

/*
Plugin Name: 8bit  Video Game Ads
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
	
/**
 * The namespaces provided by the SDK.
 */
use \DTS\eBaySDK\Constants;
// use \DTS\eBaySDK\Shopping\Services;
// use \DTS\eBaySDK\Shopping\Types;
// use \DTS\eBaySDK\Shopping\Enums;

use \DTS\eBaySDK\Finding\Services;
use \DTS\eBaySDK\Finding\Types;
use \DTS\eBaySDK\Finding\Enums;


function video_game_ads( $atts ) {
    $a = shortcode_atts( array(
        'game' => 'tetris NES cartridge',
    ), $atts );


	global $game_pricing;
	
	$game_pricing['min'] = 9999.99;
	$game_pricing['max'] = 0;
		
	/**
	 * Include the configuration values.
	 *
	 * Ensure that you have edited the configuration.php file
	 * to include your application keys.
	 */
	$config = require( plugin_dir_path( __FILE__ ) . 'configuration.php');

	/**
	 * Create the service object.
	 */

	$service = new Services\FindingService([
	    'credentials' => $config['production']['credentials'],
	    'globalId'    => Constants\GlobalIds::US,
	    'trackingId' => get_option( 'vga_trackingid' ),
	    'trackingPartnerCode' => '9',
	    'affiliateUserId' => get_option( 'vga_trackingid' ),
	    // 'debug' => true,
	]);
	
	// var_dump($service->getConfig('trackingId'));
	// die();
	
	/**
	 * Create the request object.
	 */
	$request = new Types\FindItemsByKeywordsRequest();
	
	/**
	 * Create the request object.
	 */
	// $request = new Types\FindProductsRequestType();
	
	/**
	 * Create the service object.
	 */
	/*
	$service = new Services\ShoppingService([
	    'credentials' => $config['production']['credentials'],
	    'trackingId' => get_option( 'vga_trackingid' ),
	    'trackingPartnerCode' => '9',
	    'affiliateUserId' => get_option( 'vga_trackingid' ),
	    'apiVersion' => '957',
	    
	]);
*/
	
	/**
	 * Assign the keywords.
	 */
	$request->keywords = $a['game'];
	// $request->QueryKeywords = $a['game'];
	
	// var_dump($a['game']);
	// die();
	
	$request->paginationInput = new Types\PaginationInput();
	$request->paginationInput->entriesPerPage = 6;
	
	$request->affiliate = new Types\Affiliate( array( 'trackingId' => get_option( 'vga_trackingid'), 'networkId' => '9', 'customId' => get_post_meta( get_the_ID(), 'upc_code', true ) ) );
	
	
	/**
	 * Specify that additional fields need to be returned in the response.
	 */
	// $request->IncludeSelector = 'ItemSpecifics,Variations,Compatibility,Details';
	// $request->IncludeSelector = 'Details,Items,ItemSpecifics,SellerInfo';
	// DomainHistogram, Details, Items, ItemSpecifics or SellerInfo
	
	/**
	 * Send the request.
	 */
	$response = $service->findItemsByKeywords($request);
	// $response = $service->FindProducts($request);
	
	// var_dump($response);
	// die();
	
	/**
	 * Output the result of the search.
	 */ 
	if (isset($response->errorMessage)) {
	    foreach ($response->errorMessage->error as $error) {
	        printf(
	            "%s: %s\n\n",
	            $error->severity=== Enums\ErrorSeverity::C_ERROR ? 'Error' : 'Warning',
	            $error->message
	        );
	    }
	}
	if ($response->ack !== 'Failure') {
	
		$ads = '<div class="row small-up-2 medium-up-2 large-up-3">';
	
	    foreach ($response->searchResult->item as $item) {
	    
	    	$ads .= sprintf(
	            "<div class='column'>
	            	<div class='clearfix'>
	            		<div class='text-center'><img src='%s' class='thumbnail' alt='%s' /></div>
	            		<h5>%s</h5>
	            		<div class='text-center'>
	            			<a href='%s' class='button' target='_BLANK'>Buy Now $%.2f %s</a>
	            		</div>
	            	</div>
	            </div>\n",
	            $item->galleryURL,
	            $item->title,
	            $item->title,
	            $item->viewItemURL,
	            $item->sellingStatus->currentPrice->value,
	            $item->sellingStatus->currentPrice->currencyId
	        );
	
			if( $game_pricing['min'] > $item->sellingStatus->currentPrice->value ){
				$game_pricing['min'] = $item->sellingStatus->currentPrice->value;
			}
			
			if( $game_pricing['max'] < $item->sellingStatus->currentPrice->value ){
				$game_pricing['max'] = $item->sellingStatus->currentPrice->value;
			}
			
	
			// var_dump($item);
			
			// title
			// galleryURL
			// viewItemURL
			// sellingStatus->currentPrice->currencyId
			// sellingStatus->currentPrice->value
			
	    }
	    
	    $ads .= '</div>';
	} // end if !== Failure
	
	return $ads;
	
	
}

add_shortcode( 'video_game_ads', 'video_game_ads' );



// ------------------------------------------------------------------
 // Add all your sections, fields and settings during admin_init
 // ------------------------------------------------------------------
 //
 
 /*

return [
    'sandbox' => [
        'credentials' => [
            'devId' => 'YOUR_SANDBOX_DEVID_APPLICATION_KEY',
            'appId' => 'YOUR_SANDBOX_APPID_APPLICATION_KEY',
            'certId' => 'YOUR_SANDBOX_CERTID_APPLICATION_KEY',
        ],
        'authToken' => 'YOUR_SANDBOX_USER_TOKEN_APPLICATION_KEY'
    ],
    'production' => [
        'credentials' => [
            'devId' => 'YOUR_PRODUCTION_DEVID_APPLICATION_KEY',
            'appId' => 'YOUR_PRODUCTION_APPID_APPLICATION_KEY',
            'certId' => 'YOUR_PRODUCTION_CERTID_APPLICATION_KEY',
        ],
        'authToken' => 'YOUR_PRODUCTION_USER_TOKEN_APPLICATION_KEY'
    ]
];
*/
 

/*
return [
    'sandbox' => [
        'credentials' => [
            'devid' => 'your_sandbox_devid_application_key',
            'appid' => 'your_sandbox_appid_application_key',
            'certid' => 'your_sandbox_certid_application_key',
        ],
        'authtoken' => 'your_sandbox_user_token_application_key'
    ],
    'production' => [
        'credentials' => [
            'devid' => 'your_production_devid_application_key',
            'appid' => 'your_production_appid_application_key',
            'certid' => 'your_production_certid_application_key',
        ],
        'authtoken' => 'your_production_user_token_application_key'
    ]
];
*/ 
 
 function vga_admin_init() {
 	// Add the section to media settings so we can add our
 	// fields to it
 	add_settings_section(
		'vga_setting_section',
		'8bit Game Ad Settings',
		'vga_setting_section_callback_function',
		'media'
	);
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_trackingid',
		'eBay Tracking ID',
		'vga_trackingid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_trackingid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_sandbox_devid',
		'eBay Sandbox Dev ID',
		'vga_sandbox_devid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_sandbox_devid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_sandbox_appid',
		'eBay Sandbox App ID',
		'vga_sandbox_appid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_sandbox_appid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_sandbox_certid',
		'eBay Sandbox Cert ID',
		'vga_sandbox_certid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_sandbox_certid' );

 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_sandbox_auth_token',
		'eBay Sandbox Auth Token',
		'vga_sandbox_auth_token_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_sandbox_auth_token' ); 	
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_production_devid',
		'eBay Production Dev ID',
		'vga_production_devid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_production_devid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_production_appid',
		'eBay Production App ID',
		'vga_production_appid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_production_appid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_production_certid',
		'eBay Production Cert ID',
		'vga_production_certid_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_production_certid' );
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_production_auth_token',
		'eBay Production Auth Token',
		'vga_production_auth_token_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_production_auth_token' ); 	
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'vga_sandbox_or_production',
		'eBay Sandbox or Production',
		'vga_sandbox_or_production_setting_callback_function',
		'media',
		'vga_setting_section'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'media', 'vga_sandbox_or_production' ); 	 	

 } // vg_admin_init()
 
 add_action( 'admin_init', 'vga_admin_init' );
 
  
 // ------------------------------------------------------------------
 // Settings section callback function
 // ------------------------------------------------------------------
 //
 // This function is needed if we added a new section. This function 
 // will be run at the start of our section
 //
 
 function vga_setting_section_callback_function() {
 	echo '<p></p>';
 }

 // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_sandbox_devid_setting_callback_function() {
 	echo '<input name="vga_sandbox_devid" id="vga_sandbox_devid" class="regular-text" type="input" value="' . get_option( 'vga_sandbox_devid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }

 // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_sandbox_appid_setting_callback_function() {
 	echo '<input name="vga_sandbox_appid" id="vga_sandbox_appid" class="regular-text" type="input" value="' . get_option( 'vga_sandbox_appid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_sandbox_certid_setting_callback_function() {
 	echo '<input name="vga_sandbox_certid" id="vga_sandbox_certid" class="regular-text" type="input" value="' . get_option( 'vga_sandbox_certid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_sandbox_auth_token_setting_callback_function() {
 	echo '<textarea name="vga_sandbox_auth_token" id="vga_sandbox_auth_token" class="large-text code" type="input" class="code"  >' . get_option( 'vga_sandbox_auth_token' ). '</textarea>
 	 <p></p>
 	 ';
 }  
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_production_devid_setting_callback_function() {
 	echo '<input name="vga_production_devid" id="vga_production_devid" class="regular-text" type="input" value="' . get_option( 'vga_production_devid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_production_appid_setting_callback_function() {
 	echo '<input name="vga_production_appid" id="vga_production_appid" class="regular-text" type="input" value="' . get_option( 'vga_production_appid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_production_certid_setting_callback_function() {
 	echo '<input name="vga_production_certid" id="vga_production_certid" class="regular-text" type="input" value="' . get_option( 'vga_production_certid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }
 
  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_production_auth_token_setting_callback_function() {
 	echo '<textarea name="vga_production_auth_token" id="vga_production_auth_token" class="large-text code" type="input" class="code"  >' . get_option( 'vga_production_auth_token' ). '</textarea>
 	 <p></p>
 	 ';
 } 


  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_sandbox_or_production_setting_callback_function() {
 	echo '<select name="vga_sandbox_or_production" id="vga_sandbox_or_production" class="large-text code" type="input" class="code"  >';
	
	echo '<option value="sandbox" '.( ( get_option( 'vga_sandbox_or_production' ) == 'sandbox' )?'selected':''  ).'>Sandbox</option>'; 	
	echo '<option value="production" '.( ( get_option( 'vga_sandbox_or_production' ) == 'production' )?'selected':''  ).'>Production</option>'; 	
 	 
 	
 	echo '</select>
 	 <p></p>
 	 ';
 } 


  // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function vga_trackingid_setting_callback_function() {
 	echo '<input name="vga_trackingid" id="vga_trackingid" class="regular-text" type="input" value="' . get_option( 'vga_trackingid' ). '" class="code"  />
 	 <p></p>
 	 ';
 }