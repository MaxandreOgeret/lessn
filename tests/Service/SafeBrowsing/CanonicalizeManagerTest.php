<?php


namespace App\Service\SafeBrowsing;


use League\Uri\Components\DataPath;
use League\Uri\Modifiers\Formatter;
use League\Uri\Parser;
use PHPUnit\Framework\TestCase;

class CanonicalizeManagerTest extends TestCase
{
    public function testCanonicalize()
    {
        $parser = new Parser();
        $formatter = new Formatter();
        $subj = 'http://%31%36%38%2e%31%38%38%2e%39%39%2e%32%36/%2E%73%65%63%75%72%65/%77%77%77%2E%65%62%61%79%2E%63%6F%6D/';
        $test = new DataPath($subj);

        dump($formatter->format($test));
    }

}