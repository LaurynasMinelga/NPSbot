<?php
/**
 * Handles HTTP requests methods
 */
require_once('./config.php');

$token_bot = $_ENV["token_bot"];

/**
 * HTTP post request application/json
 * 
 * @param string $url http url
 * @param array $fields json_encode(array) - data to be posted
 * @param bool $return - false to echo response
 * @return response - HTTP response
 */
function http_POST_json($url, $fields, $return) {
    
    global $token_bot;

    $header = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, $return);

    $response = json_decode(curl_exec($ch), true);

    return $response;
}

/**
 * HTTP post request application/x-www-form-urlencoded
 * 
 * @param url string - http url
 * @param fields json_encode(array) - data to be posted
 * @param return bool - false to echo response
 */
function http_POST_urlencoded($url, $fields, $return) {
    
    global $token_bot;

    $header = array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );

    $fields_string = http_build_query($fields);
    
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, $return);

    $response = json_decode(curl_exec($ch), true);

    return $response;
}