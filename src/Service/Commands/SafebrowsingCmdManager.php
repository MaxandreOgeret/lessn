<?php


namespace App\Service\Commands;

use App\Entity\SBLink;
use App\Entity\SBLinkMeta;
use Doctrine\ORM\EntityManagerInterface;
use function PHPSTORM_META\type;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;

class SafebrowsingCmdManager
{
    const FULL_UPDATE = 'FULL_UPDATE';
    const PARTIAL_UPDATE = 'PARTIAL_UPDATE';

    private $serializer;
    private $em;
    private $kernel;
    private $sbFileManager;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        KernelInterface $kernel,
        SafebrowsingFileManager $sbFileManager
    ) {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->kernel = $kernel;
        $this->sbFileManager = $sbFileManager;
    }

    public function process(OutputInterface $output, $jsonA)
    {
        if ($jsonA['listUpdateResponses'][0]['responseType'] === self::FULL_UPDATE) {
            $this->update($output, $jsonA, self::FULL_UPDATE);
            return;
        }
        $this->update($output, $jsonA, self::PARTIAL_UPDATE);
    }

    private function update(OutputInterface $output, $jsonA, $status)
    {
        $output->writeln("This is a ".$status);

        $rawHashes = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'];
        unset($jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes']);
        $checksum = $jsonA['listUpdateResponses'][0]['checksum']['sha256'];
        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];

        if ($status === self::PARTIAL_UPDATE) {
            $removals = $jsonA['listUpdateResponses'][0]['removals'][0]['rawIndices']['indices'];
        }

        $output->writeln('Updating metadata...');
        $this->em->getRepository(SBLinkMeta::class)->createMetaData($checksum, $newClientState, $prefixSize);

        if ($status === self::FULL_UPDATE) {
            try {
                $output->writeln('Truncating and filling table...');
                $this->em->getRepository(SBLink::class)->setupHashes($rawHashes, $prefixSize, $output);
            } catch (\Exception $e) {
                $output->writeln('ERROR : '.$e->getMessage());
            }
            return;
        }

        try {
            $output->writeln('Performing additions and deletions...');
            $dbChecksum = $this->em->getRepository(SBLink::class)->addAndDel($rawHashes, $removals, $prefixSize);
        } catch (\Exception $e) {
            $output->writeln('ERROR : '.$e->getMessage());
            return;
        }

        if (!$this->validateDatabase($this->sbFileManager->decodeBinaryBase64($checksum), $dbChecksum, $output)) {
            $this->recoverCorruption($output);
        }
    }

    private function recoverCorruption(OutputInterface $output)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'lessn:safebrowsing:setup',
        ]);

        $nestedOutput = new BufferedOutput();
        $application->run($input, $output);
        $output->write($nestedOutput->fetch());
    }

    private function validateDatabase($expected, $actual, OutputInterface $output)
    {
        $output->writeln('Checking database for corruption...');
        if ($expected === $actual) {
            $output->writeln('Database in sync.');
            return true;
        }
        $output->writeln("Database corrupted. Performing SETUP command.\n");
        return false;
    }
}
