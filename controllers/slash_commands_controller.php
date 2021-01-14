<?php
/**
 * Handles slash commands interactions
 */
require_once('./config.php');
require_once('./controllers/http_requests_controller.php');
require_once('./controllers/users_controller.php');
require_once('./controllers/admin_controller.php');

/**
 * Initiate admin panel modal
 */
function admin_panel_open($eventArray){

    $url = $_ENV['url_views.open'];
    $trigger_id = $eventArray['trigger_id'];

    //neveikia cia
    if (!empty(admins_select_one($eventArray['user_id']))){
        $viewContents = file_get_contents("./blocks/admin_panel_initiate.json");
    } else {
        $viewContents = file_get_contents("./blocks/admin_panel_denied.json");
    }
    $view = json_decode($viewContents, true);

    $fields = [
        'trigger_id'    => $trigger_id,
        'view'          => $view
    ];

    http_POST_json($url, $fields, true);
}

/**
 * open default channel for NPS notifications modal
 */
function admin_panel_set_nps_channel($eventArray){
    $url = $_ENV['url_views.push'];

    $viewContents = file_get_contents("./blocks/admin_panel_initiate.json");
    $view = json_decode($viewContents, true);
}

/**
 * Add/remove admins modal
 */
function admin_panel_set_admins($eventArray){

}