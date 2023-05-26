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

namespace rt\CamoProxy\Hooks;

use Exception;
use rt\CamoProxy\Camo;
use rt\CamoProxy\Core;

final class Frontend
{
    /**
     * Hook: parse_message_end
     *
     * @param $message
     * @return void
     */
    public function parse_message_end(&$message): void
    {
        if (Core::is_enabled() && Core::is_allowed_to_use())
        {
            $find_images = \rt\CamoProxy\extractImageUrls($message);

            if (!empty($find_images))
            {
                $images = $urls = [];
                foreach ($find_images as $i)
                {
                    $camo = new Camo($i);
                    $images[] = $i;
                    $urls[] = $camo->getProxiedImageUrl();
                }

                // Replace images in message
                $message = str_replace($images, $urls, $message);
            }
        }
    }

    /**
     * Hook: misc_start
     *
     * @throws Exception
     */
    public function misc_start(): void
    {
        global $mybb;

        if ($mybb->get_input('action') === 'rt_camo')
        {
            if (Core::is_enabled() && Core::is_allowed_to_use())
            {
                $image = $mybb->get_input('image');

                $decoded_image = \rt\CamoProxy\base64url_decode(urldecode($image));

                if (!empty($decoded_image))
                {
                    $camo = new Camo($decoded_image);
                    $camo->showImage((int) $mybb->settings['rt_camo_proxy_cache_time']);
                }
            }
        }
    }
}