<?php
/**
 * Users model
 * ID - Auto increment
 * user_id - slack user id [varchar](60)
 * username - slack username [varchar](255)
 * temporary - has user enabled notifications temporary [boolean]
 * end_date - notifications end date [datetime]
 */
require_once('./dbconnect.php');

/**
 * Insert users to database
 */
function admins_insert_to_database($user_id, $username){
    global $pdo;

    $data = [
        'user_id'   => $user_id,
        'username'  => $username
    ];
    $sql = "INSERT INTO admins (user_id, username) VALUES (:user_id, :username)";
    $pdo->prepare($sql)->execute($data);
}

/**
 * Select all users
 */
function admins_select_all() {
    global $pdo;

    $users = $pdo->query("SELECT * FROM admins")->fetchAll();

    return $users;
}

/**
 * Select one user by slack id
 */
function admins_select_one($user_id){
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE user_id=:user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    return $user;
}

/**
 * Delete from users by slack id
 */
function admins_delete_one($user_id){
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM admins WHERE user_id=:user_id");
    $stmt->execute(['user_id' => $user_id]);
}