<?php

$filter;

$filter = array(
    'login' => '',
    'name' => array('filter' => FILTER_CALLBACK,
                'options' => 'cleanString'),
    'firstname' => array('filter' => FILTER_CALLBACK,
                  'options' => 'cleanString'),
    'email' => array('filter' => FILTER_VALIDATE_EMAIL),
    'phone_number' => '',
    'password' => '',
    'second_password'=> ''
);