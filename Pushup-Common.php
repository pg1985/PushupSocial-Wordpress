<?php
/*
 * Shared class that implements functions common to all official Pushup Social plugins.
 * File: Pushup-Common.php
 * Version: 1.0
 * Author: Pushup Social
 */

//Define internal configuration

//And now for the actual class
class PushupSocial {
    const LINK_JOIN = "http://pushup.com/get-started.html";
    const LINK_FIND = "http://pushup.com/";

    //This checks if the Community ID it is passed is valid (optionally for a given domain)
    static function validateCommunityID($var, $currentDomain = false) {
        if (strlen($var) != 24 || !ctype_xdigit($var))
            return false;

        if ($currentDomain === false)
            return true;

        //This will check against the server if it is valid for $currentDomain in the future
        return true;
    }
}