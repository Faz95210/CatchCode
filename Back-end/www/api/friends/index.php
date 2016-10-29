<?php

include_once ("../config/config.php");
include_once ("../includes/database.php");
include_once ("../functions/misc-functions.php");
include_once ("../functions/friends-functions.php");

execRequest();

function execRequest(){
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = addFriendship($data);
            break;
        case 'GET':
            $response = getFriends();
            break;
        case 'DELETE':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            $response = deleteFriendship($data["id"]);
            break;
        default:
            $response = 886;
            break;
    }
    if (is_array($response)) {
        sendResponse($response["code"], 'application/json; charset=utf-8', $response["result"]);
    } else if (is_integer($response)){
        sendResponse($response, 'application/json; charset=utf-8');
    }
}


function addFriendship($data){
    $connection = database_connect();
    if (!isset($data['id1']) || !isset($data['id2'])){
        return 601;
    }
    if (checkIds($connection, $data["id1"]) == false || checkIds($connection, $data["id2"]) == false){
        return 673;
    }
    $result = database_insert_pdo($connection, CONTACTS_TABLE, array('user1_id' => $data['id1'], 'user2_id' => $data['id2']));
    return array('code' => 202, 'result'=>$result);
}

function getFriends(){
    $connection = database_connect();
    if (isset($_GET['id'])) {
        $query = "SELECT * FROM " . CONTACTS_TABLE . " WHERE user1_id = "
        . $_GET['id'] . " OR user2_id = " . $_GET['id'];
        $result = database_select($connection, $query);
        if (count($result) <= 0) {
            $response = 891;
        } else {
            $response = array('code' => 202, 'result'=>$result);
        }
    } else {
        $response = 670;
    }
    return $response;
}

function deleteFriendship($id){
    $connection  = database_connect();
    if (!isset($id)){
        return 670;
    }
    $query = "DELETE FROM " . CONTACTS_TABLE . " WHERE id = $id";
    $result = database_delete($connection, $query);
    if ($result["status"] == 1) {
        $response = array('code' => 202, 'result'=>$result);
    } else {
        $response = array('code' => 400, 'result'=>$result);
    }
    return $response;
}