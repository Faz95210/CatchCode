<?php

include_once ("../config/config.php");
include_once ("../includes/database.php");
include_once ("../functions/misc-functions.php");

execRequest();

function execRequest(){
    if (strcmp($_SERVER["REQUEST_METHOD"], "GET") == 0){
        $response = getSocialNetworksList();
    } else {
        sendResponse(887);
        return;
    }
    sendResponse($response["code"], 'application/json; charset=utf-8', $response["result"]);
}

function getSocialNetworksList(){
    $connection = database_connect();
    $query = "SELECT * FROM " . SOCIAL_NETWORKS_TABLE;
    $result = database_select($connection, $query);
    if (count($result) > 0){
        $return_value = array('code' => 202, 'result' => $result);
    } else {
        $return_value = 700;
    }
    return $return_value;}