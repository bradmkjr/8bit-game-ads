<?php
/**
 * Configuration settings used by all of the examples.
 *
 * Specify your eBay application keys in the appropriate places.
 *
 * Be careful not to commit this file into an SCM repository.
 * You risk exposing your eBay application keys to more people than intended.
 *
 * For more information about the configuration, see:
 * http://devbay.net/sdk/guides/sample-project/
 */
return [
    'sandbox' => [
        'credentials' => [
            'devId' =>  get_option( 'vga_sandbox_devid' ), // 'YOUR_SANDBOX_DEVID_APPLICATION_KEY',
            'appId' => get_option( 'vga_sandbox_appid' ), // 'YOUR_SANDBOX_APPID_APPLICATION_KEY',
            'certId' => get_option( 'vga_sandbox_certid' ), // 'YOUR_SANDBOX_CERTID_APPLICATION_KEY',
        ],
        'authToken' => get_option( 'vga_sandbox_auth_token' ), // 'YOUR_SANDBOX_USER_TOKEN_APPLICATION_KEY'
    ],
    'production' => [
        'credentials' => [
            'devId' => get_option( 'vga_production_devid' ), // 'YOUR_PRODUCTION_DEVID_APPLICATION_KEY',
            'appId' => get_option( 'vga_production_appid' ), // 'YOUR_PRODUCTION_APPID_APPLICATION_KEY',
            'certId' => get_option( 'vga_production_certid' ), // 'YOUR_PRODUCTION_CERTID_APPLICATION_KEY',
        ],
        'authToken' => get_option( 'vga_production_auth_token' ), // 'YOUR_PRODUCTION_USER_TOKEN_APPLICATION_KEY'
    ]
];