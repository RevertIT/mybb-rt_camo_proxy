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

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use MyBB;

class Camo
{
    public string $imageUrl;
    private string $camoProxyUrl;
    private string $camoProxyKey;
    private string $digest;
    private string $camoImageUrl;
    private MyBB $mybb;

    public function __construct(string $imageUrl)
    {
        global $mybb;

        $this->imageUrl = $imageUrl;
        $this->mybb = $mybb;

        $this->camoProxyUrl = $mybb->settings['rt_camo_proxy_url'];
        $this->camoProxyKey = $mybb->settings['rt_camo_proxy_key'];

        $this->digest = hash_hmac('sha256', $this->camoProxyUrl, $this->camoProxyKey);
        $this->camoImageUrl = bin2hex(RFC3986_urlencode($imageUrl));
    }

    /**
     * Get image extension extracted from URL
     *
     * @return string
     */
    public function getImageExtension(): string
    {
        return pathinfo($this->imageUrl, PATHINFO_EXTENSION);
    }

    /**
     * Get Image Mime Type
     *
     * @return string
     */
    public function getImageMimeType(): string
    {
        $allowed_mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        $extension = $this->getImageExtension();

        return $allowed_mimes[$extension] ?? '';
    }

    /**
     * Finally show the image if everything went fine
     *
     * @param int $cache_for In hours how long should browser cache image for
     *
     * @return void
     * @throws Exception
     */
    public function showImage(int $cache_for = 4): void
    {
        // Set the cache control header
        $cache_time = 60 * 60 * $cache_for;
        header("Cache-Control: public, max-age={$cache_time}");

        $expires = new DateTimeImmutable("now +{$cache_for} hours", new DateTimeZone('GMT'));
        header('Expires: ' . $expires->format('D, d M Y H:i:s') . ' GMT');

        // Check if everything is fine
        if (!$this->isValidUrl() || !$this->isValidMime())
        {
            header("Content-Type: image/gif");
            echo file_get_contents($this->mybb->settings['rt_camo_proxy_default_image']);
            die;
        }

        header("Content-Type: {$this->getImageMimeType()}");

        try
        {
            echo $this->getImageSource();
        }
        catch (Exception $e)
        {
            header("Content-Type: image/gif");
            echo $e->getMessage();
        }
        die;
    }

    /**
     * Get Proxied Image Url
     *
     * @return string
     */
    public function getProxiedImageUrl(): string
    {
        return "{$this->camoProxyUrl}&amp;digest={$this->digest}&amp;image={$this->camoImageUrl}";
    }

    /**
     * Check if Mime type is valid
     *
     * @return bool
     */
    private function isValidMime(): bool
    {
        return !empty($this->getImageMimeType());
    }

    /**
     * Check if URL is valid by validating hash_hmac values
     *
     * @return bool
     */
    private function isValidUrl(): bool
    {
        return hash_equals($this->digest, $this->mybb->get_input('digest')) &&
            $this->camoImageUrl === $this->mybb->get_input('image');
    }

    /**
     * Get image source string
     *
     * @return string
     * @throws Exception
     */
    private function getImageSource(): string
    {

        $imageData = file_get_contents($this->imageUrl);

        if (!$imageData)
        {
            throw new Exception(file_get_contents($this->mybb->settings['rt_camo_proxy_default_image']));
        }

        return $imageData;
    }
}