<?php


namespace App\Service\Commands;

use Symfony\Component\Console\Output\OutputInterface;

class SafebrowsingFileManager
{
    public function decodeBinaryBase64($base64String)
    {
        return bin2hex(base64_decode($base64String));
    }

    public function parse($filePath, OutputInterface $output)
    {
        $output->writeln('Parsing file...');
        $jsonString = file_get_contents($filePath);
        return json_decode($jsonString, true);
    }
}
