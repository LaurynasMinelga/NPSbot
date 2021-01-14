<?php
/**
 * The slash commands endpoint
 * Commands:
 * 1. /adminpanel
 */
require_once('./controllers/slash_commands_controller.php');

/**
 * Parse Payload of slash commands
 */
$event = parse_str(urldecode(file_get_contents("php://input")),$eventArray);

/**
 * Route commands
 */
switch ($eventArray['command']) {
    
    case "/adminpanel":
        admin_panel_open($eventArray);
    break;
    
    case "view_submission": 
        echo "";
    break;

    case "block_actions": 
        echo "";
    break;
}
