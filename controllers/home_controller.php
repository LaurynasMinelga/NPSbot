<?php
/**
 * Handles app home interactions
 */
require_once('./config.php');
require_once('./controllers/http_requests_controller.php');

function home_initiate($eventArray){
    $url = $_ENV['url_views.publish'];
    $bot_token = $_ENV['token_bot'];

    $viewContents = file_get_contents("./blocks/home_app_initiate.json");
    $view = json_decode($viewContents, true);
    
    $fields = [
        'token' => $bot_token,
        'user_id' => $eventArray['event']['user'],
        'view' => $view
    ];

    http_POST_json($url, $fields, true);
}

function NPS_initiate(){
    
}