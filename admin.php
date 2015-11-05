<?php
define('PUSHUP_PAGESLUG_CONFIG', 'pushupCommunityConfig');
define('PUSHUP_PAGESLUG_SPLASH', 'pushupCommunitySplash');

if (!$pushup_configured_correctly) {
    $unconfigured_action = function () {
        pushup_template("unconfigured");
    };
    add_action('admin_head-plugins.php', function () use ($unconfigured_action) {
        add_action('admin_notices', $unconfigured_action);
    });
    add_action('admin_head-index.php', function () use ($unconfigured_action) {
        add_action('admin_notices', $unconfigured_action);
    });
}

add_action('admin_menu', function () use ($pushup_configured) {
    if ($pushup_configured) {
        add_plugins_page(__('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_CONFIG, function () {
            pushup_full_template("editor");
        });
    } else {
        add_plugins_page(__('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_SPLASH, function () {
            pushup_full_template("splash");
        });

        add_submenu_page(null, __('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_CONFIG, function () {
            pushup_full_template("editor");
        });
    }
});


add_filter('plugin_action_links_' . PUSHUP_PLUGIN_BASENAME, function ($links) {
    $links[] = '<a href="' . menu_page_url(PUSHUP_PAGESLUG_CONFIG, false) . '">Settings</a>';
    return $links;
});



function pushup_full_template($name) {
    wp_enqueue_style("pushup.css", PUSHUP_PLUGIN_URL . '/css/pushup.css');
    get_screen_icon();
    pushup_template($name);
}

function pushup_template($name) {
    include(PUSHUP_PLUGIN_DIRECTORY . "/html/{$name}.phtml");
}
