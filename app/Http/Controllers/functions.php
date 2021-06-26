<?php

function authCheck($typesAllowed) {

    safelyStartSession();

    if ( isset($_SESSION['user_type']) ) {
        $userType = $_SESSION['user_type'];

        if ( gettype($typesAllowed) == "array" ) {
            foreach ($typesAllowed as $allowedType) {
                if ($userType == $allowedType) return true;
            }
        } else {
            if ($userType == $typesAllowed) return true;
        }
    }

    // otherwise
    return false;
}

function safelyStartSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function getPropertySafely($class, $property) {
    if (property_exists ($class, $property))
        return $class->$property;
    else 
        return '';
}

?>