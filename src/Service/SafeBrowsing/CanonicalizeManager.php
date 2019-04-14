<?php


namespace App\Service\SafeBrowsing;


use League\Uri\Parser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CanonicalizeManager
{
    const PERCENT_ESCAPE_PATTERN = '/[\x00-\x20]|[\x7F-\xFF]|(?!%25)%|#/';

    private $parser;
    private $validator;
    private $ipHostManager;

    public function __construct(IpHostManager $ipHostManager)
    {
        $this->parser = new Parser();
        $this->ipHostManager = $ipHostManager;
    }

    /**Canonicalize function realized according to https://developers.google.com/safe-browsing/v4/urls-hashing#canonicalization
     *
     * @param $url
     * @return mixed
     */
    public function canonicalize($url)
    {
        $parsedUrl = $this->parser->parse($url);
        $scheme = $parsedUrl['scheme'];
        $hostname = $parsedUrl['host'];
        $path = $parsedUrl['path'];

        $scheme = $this->prepareForCanonicalizer($scheme);
        $hostname = $this->prepareForCanonicalizer($hostname);
        $path = $this->prepareForCanonicalizer($path);

        $hostname = $this->canonicalizeHostname($hostname);
        $path = $this->canonicalizePath($path);

        $url = "$scheme://$hostname$path";

        $url = $this->percentEscape($url);
        return $url;
    }

    /**Remove forbidden chars + repeatedly percent escape string
     *
     * @param $string
     * @return mixed|string|string[]|null
     */
    public function prepareForCanonicalizer($string)
    {
        $string = str_replace(["\x09", "\x0d", "\x0a"], '', $string); // Remove forbidden chars
        $string = preg_replace('/#.*$/', '', $string);

        do {
            $prevString = $string;
            $string = rawurldecode($string);
        } while ($string !== $prevString);

        return $string;
    }

    public function canonicalizeHostname($hostname)
    {
        $hostname = $this->removeTrailingLeading($hostname, '.');
        $hostname = $this->replaceConsecutive($hostname, '.');
        $hostname = mb_strtolower($hostname);
        $hostname = $this->ipHostManager->handleIfIp($hostname);

        return $hostname;
    }

    public function canonicalizePath($path)
    {
        $path = str_replace('/./', '/', $path);  // replace '/./' with '/'
        $path = preg_replace('/\/.+\/\.\.\//', '/', $path); // Remove '/../' along with the preceding path component
        $path = preg_replace('/\/{2,}/', '/', $path);
        return $path;
    }

    public function percentEscape($url)
    {
        $matches = [];
        // Match char <= ASCII 32, >= 127, "#", or "%"
        if(!preg_match_all(self::PERCENT_ESCAPE_PATTERN, $url, $matches))
        {
            return $url;
        }

        $matches = array_unique($matches[0]);
        $translate = array_combine(
          $matches,
          array_map(
              function ($value)
              {
                  return sprintf('%%%02s', dechex(ord($value)));
              },
              $matches
          )
        );

        $url = strtr($url, $translate);
        return $url;
    }

    private function removeTrailingLeading($url, $char)
    {
        return trim($url, '.');
    }

    private function replaceConsecutive($url, $char)
    {
        return preg_replace("/\\$char{2,}/", $char, $url);
    }
}