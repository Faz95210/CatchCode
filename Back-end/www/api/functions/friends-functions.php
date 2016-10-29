<?php

function checkIds($connection, $id) {
    $query = "SELECT id FROM " . USERS_TABLE . " WHERE id = $id";
    $result = database_select($connection, $query);
    if (count($result) > 0) {
        return true;
    }
    return false;
}