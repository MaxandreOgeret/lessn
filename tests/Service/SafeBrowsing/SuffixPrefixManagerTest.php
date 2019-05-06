<?php


namespace App\Service\SafeBrowsing;

use App\Service\SafeBrowsing\IpHost\IpHostConverter;
use App\Service\SafeBrowsing\IpHost\IpHostIdentifier;
use App\Service\SafeBrowsing\IpHost\IpHostManager;
use PHPUnit\Framework\TestCase;

class SuffixPrefixManagerTest extends TestCase
{
    private $canonicalizeManager;
    private $suffixPrefixManager;

    public function __construct()
    {
        parent::__construct();
        $ipHostIdentifier = $ipHostIdentifier = new IpHostIdentifier();
        $ipHostConverter = $ipHostConverter = new IpHostConverter();
        $ipHostManager = $ipHostManager = new IpHostManager($ipHostIdentifier, $ipHostConverter);
        $this->canonicalizeManager = new CanonicalizeManager($ipHostManager);
        $this->suffixPrefixManager = new  SuffixPrefixManager($ipHostIdentifier, $this->canonicalizeManager);
    }
    
    public function testCreateSuffixPrefix()
    {
        $expected = [
            'a.b.c/1/2.html?param=1',
            'a.b.c/1/2.html',
            'a.b.c/',
            'a.b.c/1/',
            'b.c/1/2.html?param=1',
            'b.c/1/2.html',
            'b.c/',
            'b.c/1/',
        ];
        $actualUrl = 'http://a.b.c/1/2.html?param=1';
        $actual = $this->suffixPrefixManager->createSuffixPrefix($this->canonicalizeManager->canonicalize($actualUrl));
        $this->assertTrue(count(array_intersect($expected, $actual)) === count($expected));

        $expected = [
            'a.b.c.d.e.f.g/1.html',
            'a.b.c.d.e.f.g/',
            'c.d.e.f.g/1.html',
            'c.d.e.f.g/',
            'd.e.f.g/1.html',
            'd.e.f.g/',
            'e.f.g/1.html',
            'e.f.g/',
            'f.g/1.html',
            'f.g/',
        ];
        $actualUrl = 'http://a.b.c.d.e.f.g/1.html';
        $actual = $this->suffixPrefixManager->createSuffixPrefix($this->canonicalizeManager->canonicalize($actualUrl));
        $this->assertTrue(count(array_intersect($expected, $actual)) === count($expected));

        $expected = [
            '1.2.3.4/1/',
            '1.2.3.4/',
        ];
        $actualUrl = 'http://1.2.3.4/1/';
        $actual = $this->suffixPrefixManager->createSuffixPrefix($this->canonicalizeManager->canonicalize($actualUrl));
        $this->assertTrue(count(array_intersect($expected, $actual)) === count($expected));
    }
}
