<?php
define('PUSHUP_PAGESLUG_CONFIG', 'pushupCommunityConfig');
define('PUSHUP_PAGESLUG_SPLASH', 'pushupCommunitySplash');

if (!$pushup_configured_correctly) {
    add_action('admin_notices', "pushup_unconfigured_action");
}

function pushup_unconfigured_action() {
    $screen = get_current_screen();
    $screenId = $screen->id;
    if ($screenId == "plugins" || $screenId == "dashboard") {
        pushup_template("unconfigured");
    }
}


add_action('admin_menu', "pushup_add_menu");

function pushup_add_menu () {
    global $pushup_configured;
    if ($pushup_configured) {
        add_plugins_page(__('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_CONFIG, "pushup_editor_action");
    } else {
        add_plugins_page(__('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_SPLASH, "pushup_splash_action");
        add_submenu_page(null, __('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_CONFIG, "pushup_editor_action");
    }
}

function pushup_editor_action() {
   pushup_full_template("editor");
}

function pushup_splash_action() {
    pushup_full_template("splash");
}


add_filter('plugin_action_links_' . PUSHUP_PLUGIN_BASENAME, "pushup_plugin_action_links");

function pushup_plugin_action_links($links) {
    $links[] = '<a href="' . menu_page_url(PUSHUP_PAGESLUG_CONFIG, false) . '">Settings</a>';
    $links[] = '<a href="' . PushupSocial::LINK_JOIN . '" target="_blank">Sign Up</a>';
    return $links;
}


function pushup_full_template($name) {
    wp_enqueue_style("pushup.css", PUSHUP_PLUGIN_URL . '/css/pushup.css');
    get_screen_icon();
    pushup_template($name);
}

function pushup_template($name) {
    include(PUSHUP_PLUGIN_DIRECTORY . "/html/{$name}.phtml");
}
