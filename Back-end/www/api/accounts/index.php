<?php

include_once ("../config/config.php");
include_once ("../includes/database.php");
include_once ("../functions/misc-functions.php");

execRequest();

function execRequest(){
 switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = addAccount($data);
            break;
        case 'PUT':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = modifyAccount($data, $data["id"]);
            break;
        case 'GET':
            $response = getAccounts();
            break;
        case 'DELETE':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = deleteAccount($data["id"]);
            break;
        default:
            $response = 888;
            break;
    }
    if (is_array($response)) {
        sendResponse($response["code"], 'application/json; charset=utf-8', $response["result"]);
    } else if (is_integer($response)){
        sendResponse($response, 'application/json; charset=utf-8');
    }
}

function addAccount($inputs){
    if (!isset($inputs['id'])){
        return 671;
    } else {
        $inputs['user_id'] = $inputs['id'];
        unset($inputs['id']);
    }
    if (!isset($inputs['social_network_id'])){
        return 672;
    }
    if (!isset($inputs['login'])){
        return 680;
    }
    $queryIsFirst = "SELECT id FROM " . ACCOUNTS_TABLE . " WHERE social_network_id = " . $inputs['social_network_id'] . " AND user_id = " . $inputs['user_id'] . " AND actif = 1";
    $connection = database_connect();
    $result = database_select($connection, $queryIsFirst);
    if (count($result) >= 1) {
        $inputs["main_account"] = 0;
    } else {
        $inputs['main_account'] = 1;
    }
    $result = database_insert_pdo($connection, ACCOUNTS_TABLE, $inputs);
    return array('code' => 202, 'result'=>$result);
}

function getAccounts(){
    if (isset($_GET['social_network_id']) && isset($_GET['id']))
        $return_value = getAccountsByType($_GET['id'], $_GET['social_network_id']);
    else if (isset($_GET['id']))
        $return_value = getAccountsByUser($_GET['id']);
    else 
        $return_value = 601;
    if (is_array($result)) {
        if (count($result) > 0) {
            $return_value =  array('code' => 202, 'result' => $result);
        } else {
            $return_value = 891;
        }
    } 
    return $return_value;
}

function modifyAccount($data, $id){
    if (!isset($id)){
        return 670; 
    }
    $connection = database_connect();
    $result = database_update_pdo($connection, ACCOUNTS_TABLE, $data, array('id' => $id, 'actif' => 1));
    if ($result["message"] == 1){
        $return_value =  200;
    } else if ($result["status"] == -1) {
        $return_value = array('code' => 889, 'result' => $result["error"]);
    } else {
        $return_value = 890;
    }
    return $return_value;
}

/**
    Misc Functions
*/

function getAccountsByUser($id){
    $query = "SELECT * FROM " . ACCOUNTS_TABLE . " WHERE user_id = $id";
    $connection = database_connect();
    $result = database_select($connection, $query);
    if (count($result) > 0){
        $return_value = array('code' => 202, 'result' => $result);
    } else {
        $return_value = 891;
    }
    return $return_value;
}

function getAccountsByType($user_id, $social_network_id){
    $query = "SELECT * FROM " . ACCOUNTS_TABLE . " WHERE user_id = $user_id AND social_network_id = $social_network_id AND actif = 1";
    $connection = database_connect();
    $result = database_select($connection, $query);
    if (count($result) > 0){
        $return_value = array('code' => 202, 'result' => $result);
    } else {
        $return_value = 891;
    }
    return $return_value;
}