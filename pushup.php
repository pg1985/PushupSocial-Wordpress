<?php
/*
Plugin Name: Pushup Social
Plugin URI: http://pushup.com/
Description: The easiest way to add a social network to your WordPress site. Simply register a community and enter the ID on the settings page.
Version: 0.1.3
Author: Pushup Social
Author URI: http://pushup.com/
License: BSD 3 Clause
*/

/*
 * Copyright (c) 2014, Pushup Social
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

if (!function_exists("add_action")) {
    echo "The Pushup Social WordPress plugin allows you to easily integrate a social network into your existing site.";
    exit;
}


define("PUSHUP_VERSION", "0.1.3");
define("PUSHUP_PLUGIN_URL", plugin_dir_url(__FILE__));
define("PUSHUP_PLUGIN_DIRECTORY", dirname(__FILE__));
define("PUSHUP_PLUGIN_BASENAME", plugin_basename(__FILE__));
define("PUSHUP_OPTIONS_GROUP", "pushupOptions");
define("PUSHUP_OPTIONS_NAME", "pushupOptionsFields");
define("PUSHUP_OPTION_COMMUNITY", "pushupCommunityId");
define("PUSHUP_OPTION_CONFIGURED", "pushupConfigured");
define("PUSHUP_OPTION_CONFIGURED_CORRECTLY", "pushupConfiguredCorrectly");
define("PUSHUP_OPTION_ENABLED", "pushupEnabled");


require_once(PUSHUP_PLUGIN_DIRECTORY . "/Pushup-Common.php");


$pushup_configured = pushup_boolean_yesno(PUSHUP_OPTION_CONFIGURED);
$pushup_configured_correctly = pushup_boolean_yesno(PUSHUP_OPTION_CONFIGURED_CORRECTLY);


add_action("admin_init", function () {
    register_setting(PUSHUP_OPTIONS_GROUP, PUSHUP_OPTIONS_NAME, function ($input) {
        $output = get_option(PUSHUP_OPTIONS_GROUP);

        $validCommunityId = PushupSocial::validateCommunityID($input[PUSHUP_OPTION_COMMUNITY], get_site_url());

        $output[PUSHUP_OPTION_COMMUNITY] = $input[PUSHUP_OPTION_COMMUNITY];

        $output[PUSHUP_OPTION_CONFIGURED] = "yes";
        $output[PUSHUP_OPTION_CONFIGURED_CORRECTLY] = pushup_yesno($validCommunityId);
        $output[PUSHUP_OPTION_ENABLED] = pushup_validate_yesno($input[PUSHUP_OPTION_ENABLED]);

        return $output;
    });
});

add_action('wp_head', function () use ($pushup_configured_correctly) {
    if (pushup_boolean_yesno(PUSHUP_OPTION_ENABLED) && $pushup_configured_correctly) {
        $communityId = pushup_get_option(PUSHUP_OPTION_COMMUNITY);
        include(PUSHUP_PLUGIN_DIRECTORY . '/html/snippet.phtml');
    }
});

function pushup_get_option($name) {
    $opts = get_option(PUSHUP_OPTIONS_NAME);
    return $opts[$name];
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


if (is_admin())
    require_once(PUSHUP_PLUGIN_DIRECTORY . "/admin.php");
