<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:34
 */

namespace App\Service;

use App\Entity\Link;
use App\Entity\Log;
use League\Uri\Parser;

class UriManager
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function format($uri)
    {
        if (mb_substr($uri, 0, 7 ) === "http://" ||
            mb_substr($uri, 0, 8 ) === "https://"
        ) {
            return $uri;
        }

        return 'https://'.$uri;
    }

    public function explodeUrl($uri)
    {
        return preg_split( "/(http:\/\/www.|https:\/\/www.|http:\/\/|https:\/\/)/", $uri , -1, PREG_SPLIT_NO_EMPTY);
    }

    public function getHost($uri)
    {
        if (mb_substr($uri, 0, 11) === "http://www." ||
            mb_substr($uri, 0, 12) === "https://www."
        ) {
            $uri = $this->parser->parse($uri)['host'];
        }

        elseif  (mb_substr($uri, 0, 7 ) === "http://") {
            $uri = mb_substr($uri, 7);
            $uri = 'http://www.'.$uri;
            $uri = $this->parser->parse($uri)['host'];
        }

        elseif (mb_substr($uri, 0, 8 ) === "https://") {
            $uri = mb_substr($uri, 8);
            $uri = 'https://www.'.$uri;
            $uri = $this->parser->parse($uri)['host'];
        }

        $uri = 'https://www.'.$uri;
        $host = $this->parser->parse($uri)['host'];

        if (mb_substr($host, 0, 4) === 'www.') {
            $host = mb_substr($host, 4 );
        }

        return $host;
    }

    public function getDomainName($host)
    {
        return implode('.', array_slice(explode('.', $host), -2, 2));
    }

    function getUuidFromUrl($url) {
        $parsed = $this->parser->parse($url);
        $explodedPath = array_filter(explode('/', $parsed['path']));
        return trim($explodedPath[1], '/');
    }

    function isLessnUrl($url) {
        $parsed = $this->parser->parse($url);
        $explodedPath = array_filter(explode('/', $parsed['path']));

        return !($parsed['scheme'] !== 'https' ||
            ($parsed['host'] !== 'lessn.io' && $parsed['host'] !== 'www.lessn.io') ||
            sizeof($explodedPath) !== 1 ||
            mb_strlen(trim($explodedPath[1], '/')) !== 8);
    }
}