<?php
/**
 * Handles messages/ephemeral messages methods
 */
require_once('./config.php');

//useless
function send_message($channel, $text){

    $url = $_ENV["url_messages"];
    $token_bot = $_ENV["token_bot"];

    $fields = [
        'token'      => $token_bot,
        'channel'    => $channel,
        'text'       => $text
    ];

    $fields_string = http_build_query($fields);

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);
}

/**
 * Search in message whether it contains NPS keyword with users mentioned
 */
function nps_detect_message($eventArray){

    $messageArray = $eventArray['event']['blocks'][0]['elements'][0]['elements'];
    $count = count($messageArray);

    for ($i = 0; $i < $count; $i++){ // iterate through array
        if ($messageArray[$i]['type']=="text"){ // check message type
            if (strpos($messageArray[$i]['text'], 'NPS') !== false){ // if contains "NPS"
                $usersArray = array();
                if (($i+5) <= $count) { //check if there are more text after NPS entry
                    for ($j = $i; $j < $i+5; $j++){ // cheeck if 5 next entries contain user IDs
                        if ($messageArray[$j]['type']=="user") { // if yes
                            array_push($usersArray, $messageArray[$j]['user_id']); // add to array
                        }
                    }
                } else {
                    for ($j = $i; $j < $count; $j++){ // check if left entries contain user IDs
                        if ($messageArray[$j]['type']="user") { // if yes
                            array_push($usersArray, $messageArray[$j]['user_id']); // add to array
                        }
                    }
                }
                if (!empty($usersArray)){ // if array not empty
                    //nps_get_users($userArray, $eventArray); // pass to another function
                    send_enabling_message($usersArray, $eventArray);
                }
            }
        }
    }
    return false;
}

/**
 * Search in message whether it contains NPS keyword with users mentioned and returns user array
 */
function nps_parse_message($messageArray){

    $count = count($messageArray);

    for ($i = 0; $i < $count; $i++){ // iterate through array
        if ($messageArray[$i]['type']=="text"){ // check message type
            if (strpos($messageArray[$i]['text'], 'NPS') !== false){ // if contains "NPS"
                $usersArray = array();
                if (($i+5) <= $count) { //check if there are more text after NPS entry
                    for ($j = $i; $j < $i+5; $j++){ // cheeck if 5 next entries contain user IDs
                        if ($messageArray[$j]['type']=="user") { // if yes
                            array_push($usersArray, $messageArray[$j]['user_id']); // add to array
                        }
                    }
                } else {
                    for ($j = $i; $j < $count; $j++){ // check if left entries contain user IDs
                        if ($messageArray[$j]['type']="user") { // if yes
                            array_push($usersArray, $messageArray[$j]['user_id']); // add to array
                        }
                    }
                }
                if (!empty($usersArray)){ // if array not empty
                    return $usersArray;
                }
            }
        }
    }
    $usersArray = array();
    return $usersArray;
}

/**
 * Get users by id if they exist (not used)
 */
function nps_get_users($usersArray){

    $url = $_ENV["url_users.info"];
    $token_bot = $_ENV["token_bot"];
    $usernameArray = array(); // empty array for usernames
    
    /*
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
    */
    foreach ($usersArray as $user){
        $fields = [
            'token'     => $token_bot,
            'user'      => $user
        ];
        /*
        $fields_string = http_build_query($fields);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        $response = json_decode(curl_exec($ch), true);
        */
        $response = http_POST_urlencoded($url, $fields, true);
        // if user exists in slack
        if ($response['ok']){
            array_push($usernameArray, $response['user']['name']); // add to array
        }
    }

    if (!empty($usernameArray)){ // if users exist in slack workspace
        return $usernameArray;
        //send_enabling_message($usernameArray, $usersArray, $eventArray);
    }
    return false;
}

/**
 * Send message to channel
 */
function send_enabling_message($usersArray, $eventArray){

    $url = $_ENV["url_chat.postephemeral"];
    $token_bot = $_ENV["token_bot"];

    $viewContents = file_get_contents("./blocks/message_enable_notifications.json");
    $view = json_decode($viewContents, true);

    foreach ($usersArray as $user){ // format text line with user mentions
        $view[0]['text']['text'] = $view[0]['text']['text'] . "<@" . $user . "> ";
    }

    $header = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Bearer ' . $token_bot
    );

    $fields = [
        'token'     => $token_bot,
        'channel'   => $eventArray['event']['channel'],
        'user'      => $eventArray['event']['user'],
        'blocks'    => $view
    ];

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

    //$response = json_decode(curl_exec($ch), true);
    curl_exec($ch);
}
