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

namespace rt\CamoProxy;

class Core
{
    public static array $PLUGIN_DETAILS = [
        'name' => 'RT Camo Proxy',
        'website' => 'https://github.com/RevertIT/mybb-rt_camo_proxy',
        'description' => 'Camouflage images inside posts and prevent 3rd-party image hosting obtaining visitors IP addresses, instead your server will get image and serve it directly (and ask browser to cache it)',
        'author' => 'RevertIT',
        'authorsite' => 'https://github.com/RevertIT/',
        'version' => '1.2',
        'compatibility' => '18*',
        'codename' => 'rt_camo_proxy',
        'prefix' => 'rt_camo_proxy',
    ];

    /**
     * Get plugin info
     *
     * @param string $info
     * @return string
     */
    public static function get_plugin_info(string $info): string
    {
        return self::$PLUGIN_DETAILS[$info] ?? '';
    }

    /**
     * Check if plugin is installed
     *
     * @return bool
     */
    public static function is_installed(): bool
    {
        global $mybb;

        if (isset($mybb->settings['rt_camo_proxy_enabled']))
        {
            return true;
        }

        return false;
    }

    /**
     * Check if plugin is enabled
     *
     * @return bool
     */
    public static function is_enabled(): bool
    {
        global $mybb;

        return isset($mybb->settings['rt_camo_proxy_enabled']) && (int) $mybb->settings['rt_camo_proxy_enabled'] === 1 && !empty($mybb->settings['rt_camo_proxy_key']);
    }

    /**
     * Check if user can use Camo Proxy
     *
     * @return bool
     */
    public static function is_allowed_to_use(): bool
    {
        global $mybb;

        return  (isset($mybb->settings['rt_camo_proxy_allowed_usergroups']) &&
            (in_array((int) $mybb->user['usergroup'], \rt\CamoProxy\get_settings_values('allowed_usergroups')) || (string) $mybb->settings['rt_camo_proxy_allowed_usergroups'] === '-1'));
    }

    /**
     * Add settings
     *
     * @return void
     */
    public static function add_settings(): void
    {
        global $PL, $mybb;

        $PL->settings(self::$PLUGIN_DETAILS['prefix'],
            "RT Camo Proxy",
            "Setting group for the RT Camo Proxy plugin.",
            [
                "enabled" => [
                    "title" => "Enable Discord Webhooks plugin?",
                    "description" => "Enable Discord Webhooks.",
                    "optionscode" => "yesno",
                    "value" => 1
                ],
                "url" => [
                    "title" => "Proxy url",
                    "description" => "You can use subdomains (for example: camo.mydomain.com which points to your default mybb domain. eg: CNAME => mydomain.com), or url rewriting to make url better looking, but for sake of simplicity we will use misc.php page as default",
                    "optionscode" => "text",
                    "value" => "{$mybb->settings['bburl']}/misc.php?action=rt_camo"
                ],
                "key" => [
                    "title" => "Camo Private key",
                    "description" => "Enter a random string so we can generate secure hashed values for proxied images",
                    "optionscode" => "text",
                    "value" => ""
                ],
                "allowed_usergroups" => [
                    "title" => "Allowed usergroups",
                    "description" => "Which usergroups are allowed to use this feature?",
                    "optionscode" => "groupselect",
                    "value" => "-1"
                ],
                "default_image" => [
                    "title" => "Default Not-found image",
                    "description" => "You should use a absolute path to the image (eg. " . MYBB_ROOT . "images/not_found_image.png" . ")",
                    "optionscode" => "text",
                    "value" => MYBB_ROOT . "images/spinner_big.gif",
                ],
                "cache_time" => [
                    "title" => "Browser Cache Time (in hours)",
                    "description" => "How long should the browser cache the image for? (Prevents using additional resources)",
                    "optionscode" => "numeric",
                    "value" => "4"
                ]
            ],
        );
    }

    public static function remove_settings(): void
    {
        global $PL;

        $PL->settings_delete(self::$PLUGIN_DETAILS['prefix'], true);
    }

    public static function add_database_modifications(): void
    {
    }

    public static function remove_database_modifications(): void
    {
    }

    /**
     * Set plugin cache
     *
     * @return void
     */
    public static function set_cache(): void
    {
        global $cache;

        if (!empty(self::$PLUGIN_DETAILS))
        {
            $cache->update(self::$PLUGIN_DETAILS['prefix'], self::$PLUGIN_DETAILS);
        }
    }

    /**
     * Remove plugin cache
     *
     * @return void
     */
    public static function remove_cache(): void
    {
        global $cache;

        $cache->delete(self::$PLUGIN_DETAILS['prefix']);
    }
}