<?php
/**
 * Created by PhpStorm.
 * User: xianqiu
 * Date: 12/9/19
 * Time: 12:38 PM
 */

return [
    'client_id' => env('PAYPAL_CLIENT_ID',''),
    'secret' => env('PAYPAL_SECRET',''),
    'accept_url'=>env('APP_URL').'/callback',
    'settings' => array(
        'mode' => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut' => 30,
        'log.LogEnabled' => true,
        'log.FileName' => storage_path() . '/logs/paypal.log',
        'log.LogLevel' => 'ERROR'
    ),
];