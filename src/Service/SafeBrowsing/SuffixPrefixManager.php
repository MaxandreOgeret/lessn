<?php


namespace App\Service\SafeBrowsing;

use App\Service\SafeBrowsing\IpHost\IpHostIdentifier;
use App\Service\SafeBrowsing\IpHost\IpHostManager;
use League\Uri\Parser;

class SuffixPrefixManager
{
    private $parser;
    private $ipHostIdentifier;
    private $canonicalizeManager;

    public function __construct(IpHostIdentifier $ipHostIdentifier, CanonicalizeManager $canonicalizeManager)
    {
        $this->parser = new Parser();
        $this->ipHostIdentifier = $ipHostIdentifier;
        $this->canonicalizeManager = $canonicalizeManager;
    }

    public function createSuffixPrefix($canonUrl)
    {
        $parsedUrl = $this->parser->parse($canonUrl);
        $host = $parsedUrl['host'];
        $path = $parsedUrl['path'];
        $query = $parsedUrl['query'];

        $hostStrings = $this->getHostStrings($host);
        $pathStrings = $this->getPathStrings($path, $query);

        return $this->combine($hostStrings, $pathStrings);
    }

    /** Generate at least 4 different strings based on hostname.
     *
     * @param $host
     * @return array
     */
    private function getHostStrings($host)
    {
        $host = preg_replace('/^www./', '', $host);
        $strings = [$host];

        if ($this->ipHostIdentifier->detecIpFormat($host) !== IpHostManager::NOT_IP) {
            return $strings;
        }

        $subHosts = [];
        while (substr_count($host, '.') > 1) {
            $host = preg_replace('/^[^.]+\.+/', '', $host);
            $subHosts[] = $host;
        }

        if ($arraySize = sizeof($subHosts) >= 5) {
            $subHosts = array_slice($subHosts, $arraySize-5);
        }
        $strings = array_merge($strings, $subHosts);

        return $strings;
    }

    private function getPathStrings($path, $query)
    {
        $strings = [substr($path, -1) !== '/' ? $path.'/' : $path];

        if ($query !== null) {
            $strings[] = $this->canonicalizeManager->rebuildPath($path, $query);
        }

        if ($path === '/') {
            return $strings;
        }
        $strings[] = '/';
        $path = substr($path, 1);

        while (substr_count($path, '/') > 0 and sizeof($strings) < 6) {
            $match = null;
            $path = preg_replace_callback('/^[^\/]+\/+/', function ($m) use (&$strings) {
                $strings[] = end($strings).$m[0];
            }, $path, 1);
        }
        return $strings;
    }

    private function combine($hostStrings, $pathStrings)
    {
        $strings = [];
        foreach ($hostStrings as $hostString) {
            foreach ($pathStrings as $pathString) {
                $strings[] = $hostString.$pathString;
            }
        }
        return $strings;
    }
}
