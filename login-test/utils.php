<?php
function isValidInput($str) {
    return preg_match('/^[\x20-\x7E]+$/', $str); // ASCII only
}

function findUser($username, $password) {
    $users = json_decode(file_get_contents("users.json"), true);
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return true;
        }
    }
    return false;
}
?>
