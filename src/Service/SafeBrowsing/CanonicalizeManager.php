<?php


namespace App\Service\SafeBrowsing;


class CanonicalizeManager
{
    /**
     * Canonicalize function realized according to https://developers.google.com/safe-browsing/v4/urls-hashing#canonicalization
     *
     * @param $url
     */
    public function canonicalize($url)
    {
        $hostname = $this->extractHostname($url);
        $path = $this->extractPath($url);

        $hostname = $this->canonicalizeHostname($hostname);
    }

    private function canonicalizeHostname($hostname)
    {
        $hostname = $this->removeTrailingLeading($hostname, '.');
        $hostname = $this->replaceConsecutive($hostname, '.');
        $hostname = mb_strtolower($hostname);
        return $hostname;
    }

    private function extractHostname()
    {

    }

    private function removeTrailingLeading($url, $char)
    {
        return trim($url, '.');
    }

    private function replaceConsecutive($url, $char)
    {
        return preg_replace("/($char)\\1+/", $char, $url);
    }
}