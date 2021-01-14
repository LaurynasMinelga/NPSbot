<?php
/**
 * Handles modal methods
 */
require_once('./config.php');
require_once('users_controller.php');
require_once('messages_controller.php');
require_once('http_requests_controller.php');

/**
 * Returns current date plus set period for notifications
 * @return date $end_date
 */
function calculate_date(){
    $hours = $_ENV["notifications_period"];
    $now = new DateTime(); //current date/time
    $now->add(new DateInterval("PT{$hours}H")); // add hours
    $end_date = $now->format('Y-m-d H:i:s');
    return $end_date;
}

/**
 * Initiate global modal
 */
function shortcut_config($trigger_id, $user_id){

    $user_exist = users_select_one($user_id); // check if user exists

    if ($user_exist) {
        $viewContents = file_get_contents("./blocks/configuration_modal_disable.json");
    } else {
        $viewContents = file_get_contents("./blocks/configuration_modal_enable.json");
    }
    $view = json_decode($viewContents, true);

    $url = $_ENV["url_views.open"];
    $fields = [
        'trigger_id'    => $trigger_id,
        'view'          => $view
    ];

    http_POST_json($url, $fields,true);
}

/**
 * Enable notifications for user without time restriction (temporary - false) (modal)
 */
function enable_forever($eventArray){

    $view_id = $eventArray['container']['view_id'];
    $hash = $eventArray['view']['hash'];

    $viewContents = file_get_contents("./blocks/configuration_modal_disable.json");
    $view = json_decode($viewContents, true);

    $url = $_ENV["url_views.update"];
    $fields = [
        'view_id'       => $view_id,
        'hash'          => $hash,
        'view'          => $view
    ];
    
    http_POST_json($url,$fields,true);

    //add to database
    $user_id = $eventArray['user']['id'];
    $username = $eventArray['user']['username'];
    $temporary = 0;
    $end_date = date('Y-m-d H:i:s');

    $users = users_select_one($user_id); // check if user exists
    if (!$users) {
        // if user does not exist, insert user
        users_insert_to_database($user_id,$username,$temporary,$end_date);
    }
}

/**
 * Enable notifications for user with time restriction (temporary - true) (modal)
 */
function enable_8hours($eventArray){

    $view_id = $eventArray['container']['view_id'];
    $hash = $eventArray['view']['hash'];

    $viewContents = file_get_contents("./blocks/configuration_modal_disable.json");
    $view = json_decode($viewContents, true);

    $url = $_ENV["url_views.update"];
    $fields = [
        'view_id'       => $view_id,
        'hash'          => $hash,
        'view'          => $view
    ];

    http_POST_json($url, $fields,true);

    //add to database
    $user_id = $eventArray['user']['id'];
    $username = $eventArray['user']['username'];
    $temporary = 1;
    //$end_date = date('Y-m-d H:i:s');
    $end_date = calculate_date();

    $users = users_select_one($user_id); // check if user exists
    if (!$users) {
        // if user does not exist, insert user
        users_insert_to_database($user_id,$username,$temporary,$end_date);
    }
}

/**
 * Disables notifications for user (delete from database) (modal)
 */
function disable_forever($eventArray){
    $view_id = $eventArray['container']['view_id'];
    $hash = $eventArray['view']['hash'];

    $viewContents = file_get_contents("./blocks/configuration_modal_enable.json");
    $view = json_decode($viewContents, true);

    $url = $_ENV["url_views.update"];
    $fields = [
        'view_id'       => $view_id,
        'hash'          => $hash,
        'view'          => $view
    ];

    http_POST_json($url, $fields,true);

    //delete from database
    $user_id = $eventArray['user']['id'];
    users_delete_one($user_id);
}

/**
 * Enables notifications for other users specified in user-select field (temporary - true) (modal)
 */
function enable_8hours_for_others($eventArray){

    http_response_code(200); // close modal
    
    $usersArray = $eventArray['view']['state']['values']['enablenotificationsforother']['enable8hoursforother']['selected_users'];
    $url = $_ENV["url_users.info"];

    // get user info by slack id
    foreach ($usersArray as $user){
        $fields = [
            'user'      => $user
        ];

        $response = http_POST_urlencoded($url, $fields,true);
        // if user exists in slack
        if ($response['ok']){
            //add user to database
            $users = users_select_one($user); // check if user exists
            if (!$users) {
                // if user does not exist, insert user
                users_insert_to_database($user,$response['user']['name'],1,calculate_date());
            } 
        }
    } 
}

/**
 * Enable notifications for other users (temporary - true) (message)
 */
function enable_8hours_message($eventArray){
    $url = $_ENV["url_conversations.history"];
    $token_bot = $_ENV["token_bot"];

    /*
    $header = array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );
    */
    $fields = [
        'token'     => $token_bot,
        'channel'   => $eventArray['container']['channel_id'],
        'latest'    => $eventArray['container']['message_ts'],
        'limit'     => 1
    ];

    $response = http_POST_urlencoded($url, $fields, true);
/*
    $fields_string = http_build_query($fields);

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch), true);*/
    $usersArray = nps_parse_message($response['messages'][0]['blocks'][0]['elements'][0]['elements']);
    enable_8hours_message_add_todatabase($usersArray);
    
}

function enable_8hours_message_add_todatabase($usersArray){
    
    $usernameArray = nps_get_users($usersArray);
    $iterator = 0;

    foreach ($usersArray as $user){
        $users = users_select_one($user); // check if user exists
            if (!$users) {
                // if user does not exist, insert user
                users_insert_to_database($user,$usernameArray[$iterator++],1,calculate_date());
            } 
    }
    /*
    $url = $_ENV["url_users.info"];
    $token_bot = $_ENV["token_bot"];
    
    $header = array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );

    // get user info by slack id
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    foreach ($usersArray as $user){
        $fields = [
            'token'     => $token_bot,
            'user'      => $user
        ];

        $fields_string = http_build_query($fields);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        $response = json_decode(curl_exec($ch), true);
        
        // if user exists in slack
        if ($response['ok']){
            //add user to database
            $users = users_select_one($user); // check if user exists
            if (!$users) {
                // if user does not exist, insert user
                users_insert_to_database($user,$response['user']['name'],1,calculate_date());
            } 
        }
    }

    //get usernames
    //check if users exist in database
    //create $data 
    //insert via transaction
    */
}

/**
 * Close ephemeral message (message)
 */
function close_message($eventArray){
    $url = $eventArray['response_url']; //$_ENV["url_chat.delete"];
    $token_bot = $_ENV["token_bot"];

    $header = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );

    $fields = [
        'delete_original'=> "true"
    ];

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, false);

    curl_exec($ch);
}