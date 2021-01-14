<?php

/**
 * App variables
 */

$_ENV["token_bot"] = "secret";
$_ENV["notifications_period"] = 8; //in hours

/**
 * Slack end-points
 */
$_ENV["url_messages"]               = "https://slack.com/api/chat.postMessage";
$_ENV["url_views.open"]             = "https://slack.com/api/views.open";
$_ENV["url_views.update"]           = "https://slack.com/api/views.update";
$_ENV["url_views.publish"]          = "https://slack.com/api/views.publish";
$_ENV["url_users.info"]             = "https://slack.com/api/users.info";
$_ENV["url_views.push"]             = "https://slack.com/api/views.push";
$_ENV["url_chat.postephemeral"]     = "https://slack.com/api/chat.postEphemeral";
$_ENV["url_conversations.history"]  = "https://slack.com/api/conversations.history";
$_ENV["url_chat.delete"]            = "https://slack.com/api/chat.delete";

/**
 * Database credentials
 */
