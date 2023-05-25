<?php
/**
 * RT Camo Proxy
 *
 * Camouflage images inside posts and prevent 3rd-party image hosting obtaining visitors IP addresses, instead your server will get image and serve it directly (and ask browser to cache it).
 *
 * @package rt_camo_proxy
 * @author  RevertIT <https://github.com/revertit>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 */

declare(strict_types=1);

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Autoload classes
require_once MYBB_ROOT . 'inc/plugins/rt/vendor/autoload.php';

\rt\Autoload\psr4_autoloader(
    'rt',
    'src',
    'rt\\CamoProxy\\',
    [
        'rt/CamoProxy/functions.php',
    ]
);

// Autoload plugin hooks
\rt\CamoProxy\autoload_plugin_hooks([
    '\rt\CamoProxy\Hooks\Frontend',
    '\rt\CamoProxy\Hooks\Backend',
]);

// Health checks
\rt\CamoProxy\load_plugin_version();
\rt\CamoProxy\load_pluginlibrary();

function rt_camo_proxy_info(): array
{
    return \rt\CamoProxy\Core::$PLUGIN_DETAILS;
}

function rt_camo_proxy_install(): void
{
    \rt\CamoProxy\check_php_version();
    \rt\CamoProxy\check_pluginlibrary();

    \rt\CamoProxy\Core::add_database_modifications();
    \rt\CamoProxy\Core::add_settings();
    \rt\CamoProxy\Core::set_cache();
}

function rt_camo_proxy_is_installed(): bool
{
    return \rt\CamoProxy\Core::is_installed();
}

function rt_camo_proxy_uninstall(): void
{
    \rt\CamoProxy\check_php_version();
    \rt\CamoProxy\check_pluginlibrary();

    \rt\CamoProxy\Core::remove_database_modifications();
    \rt\CamoProxy\Core::remove_settings();
    \rt\CamoProxy\Core::remove_cache();
}

function rt_camo_proxy_activate(): void
{
    \rt\CamoProxy\check_php_version();
    \rt\CamoProxy\check_pluginlibrary();

    \rt\CamoProxy\Core::add_settings();
    \rt\CamoProxy\Core::set_cache();
}

function rt_camo_proxy_deactivate(): void
{
    \rt\CamoProxy\check_php_version();
    \rt\CamoProxy\check_pluginlibrary();
}