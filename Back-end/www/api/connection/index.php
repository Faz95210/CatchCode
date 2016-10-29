<?php

sendToRightMethod();

function sendToRightMethod(){
    switch ($_SERVER["REQUEST_METHOD"]) {
        case 'POST':
           $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);
            if (isset($data["login"]) && isset($data["password"])) {
                $response = checkCredentials($data['login'], $data['password']);
                echo json_encode($response);
            } else {
                echo json_encode(array('Error' => 'Incorrect Parameters', 'Expected Parameters' => 'login & pasword'));
            }
            break;
        
        default:
            echo json_encode(array('Error' => 'Only POST Request accepted'));
            break;
    }
}

function checkCredentials($login, $password){
    return (array('error'=>'WORK IN PROGRESS'));
}