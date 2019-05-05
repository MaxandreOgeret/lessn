<?php


namespace App\Service\SafeBrowsing;

use PHPUnit\Framework\TestCase;

class HashManagerTest extends TestCase
{
    private $hashManager;

    public function __construct()
    {
        $this->hashManager = new HashManager();
        parent::__construct();
    }

    public function testHash()
    {
        $expected = 'ba7816bf';
        $actual = $this->hashManager->truncatedHashUrl('abc', 32);
        $this->assertEquals($expected, $actual);

        $expected = '248d6a61d206';
        $actual = $this->hashManager->truncatedHashUrl('abcdbcdecdefdefgefghfghighijhijkijkljklmklmnlmnomnopnopq', 48);
        $this->assertEquals($expected, $actual);

        $expected = 'cdc76e5c9914fb9281a1c7e2';
        $actual = $this->hashManager->truncatedHashUrl(str_repeat('a', 1000000), 96);
        $this->assertEquals($expected, $actual);
    }
}
