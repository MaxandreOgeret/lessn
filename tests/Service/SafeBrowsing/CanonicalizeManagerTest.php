<?php


namespace App\Service\SafeBrowsing;

use App\Service\SafeBrowsing\IpHost\IpHostConverter;
use App\Service\SafeBrowsing\IpHost\IpHostIdentifier;
use App\Service\SafeBrowsing\IpHost\IpHostManager;
use PHPUnit\Framework\TestCase;

class CanonicalizeManagerTest extends TestCase
{
    private $ipHostIdentifier;
    private $ipHostConverter;
    private $ipHostManager;
    private $canonicalizeManager;

    public function __construct()
    {
        parent::__construct();
        $this->ipHostIdentifier = new IpHostIdentifier();
        $this->ipHostConverter = new IpHostConverter();
        $this->ipHostManager = new IpHostManager($this->ipHostIdentifier, $this->ipHostConverter);
        $this->canonicalizeManager = new CanonicalizeManager($this->ipHostManager);
    }

    public function testUrl()
    {
        $actualUrl= 'http://host/%25%32%35';
        $expected= 'http://host/%25';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://host/%25%32%35%25%32%35';
        $expected= 'http://host/%25%25';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://host/%2525252525252525';
        $expected= 'http://host/%25';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://host/asdf%25%32%35asd';
        $expected= 'http://host/asdf%25asd';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://host/%%%25%32%35asd%%';
        $expected= 'http://host/%25%25%25asd%25%25';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://www.google.com/';
        $expected= 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://%31%36%38%2e%31%38%38%2e%39%39%2e%32%36/%2E%73%65%63%'.
            '75%72%65/%77%77%77%2E%65%62%61%79%2E%63%6F%6D/';
        $expected= 'http://168.188.99.26/.secure/www.ebay.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://195.127.0.11/uploads/%20%20%20%20/.verify/.eBaysecure='.
            'updateuserdataxplimnbqmn-xplmvalidateinfoswqpcmlx=hgplmcx/';
        $expected= 'http://195.127.0.11/uploads/%20%20%20%20/.verify/.eBaysecure='.
            'updateuserdataxplimnbqmn-xplmvalidateinfoswqpcmlx=hgplmcx/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl= 'http://host%23.com/%257Ea%2521b%2540c%2523d%2524e%25f%255E00%252611%252A22%252833%252944_55%252B';
        $expected= 'http://host%23.com/~a!b@c%23d$e%25f^00&11*22(33)44_55+';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://11111111.11111111.11111111.11111111/blah';
        $expected = 'http://255.255.255.255/blah';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://FFFFFFFF/blah';
        $expected = 'http://255.255.255.255/blah';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://3279880203/blah';
        $expected = 'http://195.127.0.11/blah';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/blah/../';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/blah/..';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/blah/..ABC';
        $expected = 'http://www.google.com/blah/..ABC';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'www.google.com/';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'www.google.com';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.evil.com/blah#frag';
        $expected = 'http://www.evil.com/blah';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.evil.com/blah#frag';
        $expected = 'http://www.evil.com/blah';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.GOOgle.com/';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com.../';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/foo\tbar\rbaz\n2';
        $expected = 'http://www.google.com/foobarbaz2';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/q?';
        $expected = 'http://www.google.com/q?';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.google.com/q?r?s';
        $expected = 'http://www.google.com/q?r?s';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://evil.com/foo#bar#baz';
        $expected = 'http://evil.com/foo';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://evil.com/foo;';
        $expected = 'http://evil.com/foo;';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://evil.com/foo?bar;';
        $expected = 'http://evil.com/foo?bar;';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://notrailingslash.com';
        $expected = 'http://notrailingslash.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://www.gotaport.com:1234/';
        $expected = 'http://www.gotaport.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = '  http://www.google.com/  ';
        $expected = 'http://www.google.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http:// leadingspace.com/';
        $expected = 'http://%20leadingspace.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://%20leadingspace.com/';
        $expected = 'http://%20leadingspace.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = '%20leadingspace.com/';
        $expected = 'http://%20leadingspace.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'https://www.securesite.com/';
        $expected = 'https://www.securesite.com/';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://host.com/ab%23cd';
        $expected = 'http://host.com/ab%23cd';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));

        $actualUrl = 'http://host.com//twoslashes?more//slashes';
        $expected = 'http://host.com/twoslashes?more//slashes';
        $this->assertEquals($expected, $this->canonicalizeManager->canonicalize($actualUrl));
    }

    public function testPercentEscape()
    {
        $expected = '%20%20%20%01abc%23';
        $actual = $this->canonicalizeManager->percentEscape("   \x01abc#");
        $this->assertEquals($expected, $actual);

        $expected = 'abc';
        $actual = $this->canonicalizeManager->percentEscape("abc");
        $this->assertEquals($expected, $actual);
    }

    public function testCanonicalizeHostname()
    {
        $expected = '';
        $actual = $this->canonicalizeManager->canonicalizeHostname('');
        $this->assertEquals($expected, $actual);

        $expected = '-';
        $actual = $this->canonicalizeManager->canonicalizeHostname('.-.');
        $this->assertEquals($expected, $actual);

        $expected = '-.-.-';
        $actual = $this->canonicalizeManager->canonicalizeHostname('-..-..-');
        $this->assertEquals($expected, $actual);

        $expected = 'abc';
        $actual = $this->canonicalizeManager->canonicalizeHostname('ABC');
        $this->assertEquals($expected, $actual);
    }

    public function testCanonicalizePath()
    {
        $expected = '/foo/bar/2000';
        $actual = $this->canonicalizeManager->canonicalizePath('/foo/./bar/./2000');
        $this->assertEquals($expected, $actual);

        $expected = '/2000';
        $actual = $this->canonicalizeManager->canonicalizePath('/foo/../bar/../2000');
        $this->assertEquals($expected, $actual);

        $expected = '/foo/bar/2000';
        $actual = $this->canonicalizeManager->canonicalizePath('/foo///bar///2000');
        $this->assertEquals($expected, $actual);
    }

    public function testPrepareUrl()
    {
        $expected = '';
        $actual = $this->canonicalizeManager->prepareForCanonicalizer("\x09\x0d\x0a");
        $this->assertEquals($expected, $actual);

        $expected = 'foo';
        $actual = $this->canonicalizeManager->prepareForCanonicalizer('foo#bar');
        $this->assertEquals($expected, $actual);
    }
}
