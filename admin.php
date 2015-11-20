<?php

$pushup_configured = pushup_boolean_yesno(PUSHUP_OPTION_CONFIGURED);

if (!$pushup_configured) {
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

add_action('admin_menu', function () {
    add_plugins_page(__('Pushup Social'), __('Pushup Social'), 'manage_options', PUSHUP_PAGESLUG_CONFIG, function () {
        pushup_full_template("editor");
    });
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
