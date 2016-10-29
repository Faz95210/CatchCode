<?php

function checkLoginIsCorrect($login){
    return ctype_alnum($login);
}

function checkLoginIsTaken($connection, $login){
    $query = "SELECT count(*) AS count FROM " . USERS_TABLE . " WHERE login = '$login';";
    $result = database_select($connection, $query);
    return ($result["0"]["count"] == 0);   
}

function checkPassword($password, $second_password){
    return (strcmp($password, $second_password) == 0);
}

function checkName($name){
    return ctype_alnum($name);
}

function checkFirstname($firstname){
    return ctype_alnum($firstname);
}

function checkEmail($connection, $email){
    $query = "SELECT count(*) AS count FROM " . USERS_TABLE . " WHERE email = '$email';";
    $result = database_select($connection, $query);
    return ($result["0"]["count"] == 0);   
}

function checkPhoneNumber($connection, $phonenumber){
    $query = "SELECT count(*) AS count FROM " . USERS_TABLE . " WHERE phone_number = '$phonenumber';";
    $result = database_select($connection, $query);
    return ($result["0"]["count"] == 0);   
}