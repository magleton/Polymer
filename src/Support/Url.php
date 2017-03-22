<?php

namespace Polymer\Support;

/**
 * Class Url.
 */
class Url
{
    /**
     * Get current url.
     *
     * @return string
     */
    public static function current()
    {
        $protocol = (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || (int)$_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    /**
     * URL-encodes string.
     *
     * @param $url
     *
     * @return string
     */
    public static function encode($url)
    {
        return urlencode($url);
    }
}
