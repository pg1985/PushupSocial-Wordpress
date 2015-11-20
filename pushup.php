<?php
/*
Plugin Name: Pushup Social
Plugin URI: http://pushup.com/
Description: The easiest way to add a social network to your WordPress site. Simply create a new community from the panel, or link an existing community.
Version: 1.5.0
Author: Pushup Social
Author URI: http://pushup.com/
License: BSD 3 Clause
*/

/*
 * Copyright (c) 2014, 2015, Pushup Social
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of Pushup Social nor the names of its contributors may be
 *   used to endorse or promote products derived from this software without
 *   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 */

define("PUSHUP_PLUGIN_URL", plugin_dir_url(__FILE__));
define("PUSHUP_PLUGIN_DIRECTORY", dirname(__FILE__));
define("PUSHUP_PLUGIN_BASENAME", plugin_basename(__FILE__));

require_once(PUSHUP_PLUGIN_DIRECTORY . "/config.php");

if (!function_exists("add_action")) {
    echo "The easiest way to add a social network to your WordPress site. Simply create a new community from the panel, or link an existing community.";
    exit;
}


/**
 * GLOBAL FUNCTIONS: Available throughout the application
 */

function pushup_get_option($name) {
    $opts = get_option(PUSHUP_OPTIONS_NAME);
    return $opts[$name];
}

function pushup_set_option($name, $value) {
    $opts = get_option(PUSHUP_OPTIONS_NAME);
    if($opts){
        $opts[$name] = $value;
        update_option(PUSHUP_OPTIONS_NAME, $opts, '', 'yes');
    } else {
        $opts[$name] = $value;
        add_option(PUSHUP_OPTIONS_NAME, $opts, '', 'yes');
    }
}

function pushup_boolean_yesno($name) {
    return (pushup_get_option($name) === "yes");
}

function pushup_yesno($bool) {
    return ($bool) ? "yes" : "no";
}

function pushup_validate_yesno($val) {
    return (($val === "yes") ? $val : "no");
}

function load_scripts () {
    wp_enqueue_script('$', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js');
}


/**
 * Initialize the Pushup Application
 */

require_once(PUSHUP_PLUGIN_DIRECTORY . "/Pushup-Common.php");

$pushupApp = new PushupSocial();
$pushup_configured = pushup_boolean_yesno(PUSHUP_OPTION_CONFIGURED);

add_action("admin_init", function () use ($pushupApp) {
    register_setting(PUSHUP_OPTIONS_GROUP, PUSHUP_OPTIONS_NAME, function ($input) use ($pushupApp) {
        $output = get_option(PUSHUP_OPTIONS_GROUP);
        $output[PUSHUP_OPTION_CONFIGURED] = $input[PUSHUP_OPTION_CONFIGURED];
        $output[PUSHUP_OPTION_SAVED_COMMUNITY] = $input[PUSHUP_OPTION_SAVED_COMMUNITY];
        if($input[PUSHUP_OPTION_SAVED_COMMUNITY]){
            if($pushupApp->validateCommunityID($input[PUSHUP_OPTION_SAVED_COMMUNITY])){
                $output[PUSHUP_OPTION_CONFIGURED] = "yes";
                $output[PUSHUP_OPTION_COMMUNITY] = $input[PUSHUP_OPTION_SAVED_COMMUNITY];
            }
            else {
                $output[PUSHUP_OPTION_CONFIGURED] = "no";
                $output[PUSHUP_OPTION_COMMUNITY] = "";
            }
        } else if($input[PUSHUP_OPTIONS_SITE_ID]){
            $community_id = $pushupApp->getSiteCommunityID($input[PUSHUP_OPTIONS_SITE_ID]);
            if($community_id != ""){
                $output[PUSHUP_OPTION_CONFIGURED] = "yes";
                $output[PUSHUP_OPTION_COMMUNITY] = $community_id;
                $output[PUSHUP_OPTION_SAVED_COMMUNITY] = $community_id;
            }
            else {
                $output[PUSHUP_OPTION_CONFIGURED] = "no";
                $output[PUSHUP_OPTION_COMMUNITY] = "";
            }
            $output[PUSHUP_OPTIONS_SITE_ID] = $input[PUSHUP_OPTIONS_SITE_ID];
        }

        return $output;
    });

});

add_action('wp_head', function () use ($pushup_configured) {
    if ($pushup_configured) {
        include(PUSHUP_PLUGIN_DIRECTORY . '/html/snippet.phtml');
    }
});

add_action('admin_enqueue_scripts', 'load_scripts');

if (is_admin())
    require_once(PUSHUP_PLUGIN_DIRECTORY . "/admin.php");
