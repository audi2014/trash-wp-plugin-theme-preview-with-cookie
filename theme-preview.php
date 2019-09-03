<?php
/*
 * Plugin Name: Theme Preview With COOKIE
 * Description: Allows themes to be previewed without activation
 * Author: Dougal Campbell
 * Author URI: http://dougal.gunters.org/
 * Version: 1.4
 */

/*
 * USAGE:
 *
 * Add query variables 'preview_theme' and/or 'preview_css' to
 * your query string. Example:
 *  http://example.com/index.php?preview_theme=default&preview_css=my-theme
 * 
 * TODO: 
 * 
 *  * Add an options page with theme selection, and persistence setting.
 *  * Use cookies to allow persistent theme previews.
 *  * Child-theme friendliness.
 *
 * NOTES:
 *
 *  * preview_theme does not always work well with child themes.
 *
 * DEMO:
 *  http://dougal.gunters.org/blog/2005/03/09/theme-preview-plugin/
 */

/* Hook on setup_theme so we can modify things */
add_action('setup_theme', 'gr_preview_theme_init');

// globals
$gr_preview_theme = '';
$gr_preview_css = '';

function gr_preview_theme_init() {
    global $gr_preview_theme, $gr_preview_css;

    if (isset($_GET['preview_theme'])) {
        $gr_preview_theme = $_GET['preview_theme'];
        setcookie('preview_theme', $gr_preview_theme, time() + (1 * 60 * 60 * 24), COOKIEPATH, COOKIE_DOMAIN);
    } else if (isset($_COOKIE['preview_theme'])) {
        $gr_preview_theme = $_COOKIE['preview_theme'];
    }

    if (isset($_GET['preview_css'])) {
        $gr_preview_css = $_GET['preview_css'];
        setcookie('preview_css', $gr_preview_css, time() + (1 * 60 * 60 * 24), COOKIEPATH, COOKIE_DOMAIN);
    } else if (isset($_COOKIE['preview_css'])) {
        $gr_preview_css = $_GET['preview_css'];
    }

    /* Don't allow directory traversal */
    if (validate_file($gr_preview_theme) !== 0) {
        return;
    }

    if (validate_file($gr_preview_css) !== 0) {
        return;
    }

    if (!$gr_preview_css) {
        $gr_preview_css = $gr_preview_theme;
    }

    if ($gr_preview_theme && file_exists(get_theme_root() . "/${gr_preview_theme}")) {
        add_filter('template', 'use_preview_theme');
    }

    if ($gr_preview_css && file_exists(get_theme_root() . "/${gr_preview_css}")) {
        add_filter('stylesheet', 'use_preview_css');
    }
}

function use_preview_theme($themename) {
    global $gr_preview_theme;

    return $gr_preview_theme;
}

function use_preview_css($cssname) {
    global $gr_preview_css;

    return $gr_preview_css;
}

