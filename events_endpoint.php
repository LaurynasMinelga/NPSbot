<?php
/**
 * Simple NPS notifications integration using Slack Events and Web APIs
 *
 * For all api methods, refer to https://api.slack.com/
 *
 * @author  Laurynas Minelga <laurynas.minelga@hostinger.com>
 * @version  0.0.15
 */
require_once('config.php');
require_once('controllers/messages_controller.php');
require_once('./controllers/home_controller.php');

/**
 * Catch Event calls
 */
$event = file_get_contents("php://input");
$eventArray = json_decode($event, TRUE); //true = associative arrays (obj['data']['data'])

if (isset($eventArray['challenge'])){
    echo $eventArray['challenge'];
}

switch ($eventArray['event']['type']) {
    
    case "message":
        messages_endpoint($eventArray);
    break;

    case "app_home_opened":
        home_endpoint($eventArray);
    break;
    
}

function messages_endpoint($eventArray) {

    if (isset($eventArray['event']['subtype'])){
        switch ($eventArray['event']['subtype']) {
    
            case "message_deleted":
                //echo "message_deleted";
            break;
            
        }
    } else { // its a simple just posted message
        nps_detect_message($eventArray); 
    }
}

function home_endpoint($eventArray) {
    home_initiate($eventArray);
}