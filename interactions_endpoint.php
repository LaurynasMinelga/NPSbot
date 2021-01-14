<?php
/**
 * Listen to user interaction calls
 * Ex.: shortcuts, modals, interactive components are received here.
 */
require_once('controllers/modal_controller.php');

/**
 * Parse Payload of interactions
 */
$event = substr(urldecode(file_get_contents("php://input")), 8);
$eventArray = json_decode($event, TRUE); //true = associative arrays (obj['data']['data'])
//echo json_encode(array()); // respond with 200 OK

/**
 * Route interactions by type
 */
switch ($eventArray['type']) {
    
    case "shortcut":
        shortcut_endpoint($eventArray);
    break;
    
    case "view_submission": 
        view_submissions_endpoint($eventArray);
    break;

    case "block_actions": 
        block_actions_endpoint($eventArray);
    break;
}

/**
 * Handles shortcut requests
 */
function shortcut_endpoint($eventArray){
    if ($eventArray['callback_id'] == "shortcut_config"){
        shortcut_config($eventArray['trigger_id'], $eventArray['user']['id']);
    }
}

/**
 * Handles response to modal action-components submission (buttons, datepickers, etc)
 */
function block_actions_endpoint($eventArray){

    switch ($eventArray['actions'][0]['action_id']) {
    
        case "enableforever":
            enable_forever($eventArray);
        break;  

        case "enable8hours": 
            enable_8hours($eventArray);
        break;

        case "disableforever": 
            disable_forever($eventArray);
        break;

        case "enablefor8hours_message": 
            enable_8hours_message($eventArray);
            close_message($eventArray);
        break;

        case "close_message": 
            close_message($eventArray);
        break;

        case "initiateNPS":

        break;

        case "setNPSchannel":
            admin_panel_set_nps_channel($eventArray);
        break;

        case "setAdminUsers":
            admin_panel_set_admins($eventArray);
        break;
    }
}

/**
 * Handles response to modal input-component submissions (multi-select, textarea, submit buttons, etc)
 */
function view_submissions_endpoint($eventArray){
    
    enable_8hours_for_others($eventArray);
    
}