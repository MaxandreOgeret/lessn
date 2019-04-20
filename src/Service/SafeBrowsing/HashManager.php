<?php


namespace App\Service\SafeBrowsing;

class HashManager
{
    public function truncatedHashUrl($url, $length = null)
    {
        return substr(hash('sha256', $url), 0, $length/4);
    }
}
