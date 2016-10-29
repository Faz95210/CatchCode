<?php

include_once ("../config/config.php");
include_once ("../includes/database.php");
include_once ("../includes/filters/users.php");
include_once ("../functions/misc-functions.php");
include_once ("../functions/users-functions.php");

execRequest();

function execRequest() {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = createUser($data);
            break;
        case 'PUT':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = updateUser($data, $data["id"]);
            break;
        case 'GET':
            $response = getUser();
            break;
        case 'DELETE':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = deleteUser($data["id"]);
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

function createUser($inputs){
    $connection = database_connect();
    global $filter;
    $data = filter_var_array($inputs, $filter, true);
    if (count($data) == 7){
        if (!checkLoginIsCorrect($data['login'])){
            $return_value = 610;
        } else if(!checkLoginisTaken($connection, $data['login'])){
            $return_value = 611;
        } else if (!checkPassword($data['password'], $data["second_password"])){
            $return_value = 620;
        } else if (!checkName($data['name'])){
            $return_value = 660;
        } else if (!checkFirstName($data['firstname'])){
            $return_value = 650;
        } else if (!checkEmail($connection, $data['email'])){
            $return_value = 631;
        } else if (!checkPhoneNumber($connection, $data['phone_number'])){
            $return_value = 641;            
        } else {
            unset($data['second_password']);
            $result = database_insert_pdo($connection, USERS_TABLE, $data);
            if ($result["status"] == 1){
                $return_value = array('code' => 201, 'result' => $result['id']);
            } else{
                $return_value = array('code' => 889, 'result' => $result['error']);
            }
        }
    } else {
        if ($data['name'] == false){
            $return_value = 660;            
        } else if ($data['firstname'] == false){
            $return_value = 650;
        } else if ($data['login'] == false){
            $return_value = 610;
        } else if ($data['password'] == false){
            $return_value = 620;
        } else if ($data['second_password'] == false){
            $return_value = 621;
        } else if ($data['email'] == false){
            $return_value = 630;
        } else if ($data['phone_number'] == false){
            $return_value = 640;
        } else{
            $return_value = 600;
        }
    }
    return $return_value;
}

function updateUser($inputs, $id){
    if (!isset($id)){
        return 670; 
    }
    $connection = database_connect();
    global $filter;
    $data = filter_var_array($inputs, $filter, false);
        if (isset($data['login']) && !checkLoginIsCorrect($data['login'])){
            $return_value = 610;
        } else if(isset($data['login']) && !checkLoginisTaken($connection, $data['login'])){
            $return_value = 611;
        } else if (isset($data['password']) && isset($data['second_password']) && !checkPassword($data['password'], $data["second_password"])){
            $return_value = 620;
        } else if (isset($data['name']) && !checkName($data['name'])){
            $return_value = 660;
        } else if (isset($data['firstname']) && !checkFirstName($data['firstname'])){
            $return_value = 650;
        } else if (isset($data['email']) && !checkEmail($connection, $data['email'])){
            $return_value = 631;
        } else if (isset($data['phone_number']) && !checkPhoneNumber($connection, $data['phone_number'])){
            $return_value = 641;            
        } else {
            unset($data['second_password']);
            $result = database_update_pdo($connection, USERS_TABLE, $data, array('id' => $id, 'actif' => 1));
            if ($result["message"] == 1){
                $return_value =  200;
            } else if ($result["status"] == -1) {
                $return_value = array('code' => 889, 'result' => $result["error"]);
            } else {
                $return_value = 890;
            }
        }
    return $return_value;
}

function getUser(){
    $connection = database_connect();
    if (!isset($_GET["id"])){
        return 670; 
    }
    $id = $_GET["id"];
    $query = "SELECT * FROM users WHERE id = $id AND actif = 1;";
    $result = database_select($connection, $query);
    if (count($result) > 0){
        $return_value = array('code' => 202, 'result' => $result);
    } else {
        $return_value = 700;
    }
    return $return_value;
}

function deleteUser($id){
    if (!isset($id)){
        return 670; 
    }
    $connection = database_connect();
    $result = database_update_pdo($connection, USERS_TABLE, array('actif' => 0), array('id' => $id));
    if ($result["message"] == 1){
        $response =  200;
    } else if ($result["status"] == -1) {
        $response = array('code' => 889, 'result' => $result["error"]);
    } else {
        $response = array('code' => 890, 'result' => 'This user has already been deleted');
    }
    return $response;
}