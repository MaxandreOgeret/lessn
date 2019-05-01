<?php


namespace App\Service\Commands;

use App\Entity\SBLink;
use App\Entity\SBLinkMeta;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;

class SafebrowsingCmdManager
{
    private $serializer;
    private $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    public function buildJsonBody($getState = false)
    {
        $state ='';
        if ($getState) {
            $state = $this->em->getRepository(SBLinkMeta::class)->getState();
        }

        $array = [
            'client' =>
                [
                    'clientId' => 'lessn',
                    'clientVersion' => exec('git describe --tags --abbrev=0'),
                ],
            'listUpdateRequests' =>
                [
                    0 =>
                        [
                            'threatType' => 'SOCIAL_ENGINEERING',
                            'platformType' => 'ANY_PLATFORM',
                            'threatEntryType' => 'URL',
                            'state' => $state,
                            'constraints' =>
                                [
                                    'region' => 'EU',
                                    'supportedCompressions' =>
                                        [
                                            0 => 'RAW',
                                        ],
                                ],
                        ],
                ],
        ];
        return json_encode($array);
    }

    public function curlInit($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        return $curl;
    }

    public function curlExecAndSave($curlInstance, $path, $filename = 'SBresponse.txt')
    {
        // todo : Remove comments
        $result = curl_exec($curlInstance);
        file_put_contents($path.'/'.$filename, $result);
        return $path.'/'.$filename;
    }

    public function parseAndProcess($filePath, OutputInterface $output)
    {
        $output->writeln('Parsing file...');
        $jsonString = file_get_contents($filePath);
        $jsonA = json_decode($jsonString, true);

        if ('FULL_UPDATE' === $jsonA['listUpdateResponses'][0]['responseType']) {
            $this->fullUpdate($output, $jsonA);
            return;
        }
        $this->partialUpdate($output, $jsonA);
    }

    private function fullUpdate(OutputInterface $output, $jsonA)
    {
        $output->writeln('This is a FULL UPDATE');
        $rawHashes = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'];
        unset($jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes']);
        $checksum = $jsonA['listUpdateResponses'][0]['checksum']['sha256'];
        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];

        $output->writeln('Updating metadata...');
        $this->em->getRepository(SBLinkMeta::class)->createMetaData($checksum, $newClientState, $prefixSize);
        $output->writeln('Truncating and filling table...');

        try {
            $this->em->getRepository(SBLink::class)->setupHashes($rawHashes, $prefixSize, $output);
        } catch (\Exception $e) {
            $output->writeln('ERROR : '.$e->getMessage());
        }
    }

    private function partialUpdate(OutputInterface $output, $jsonA)
    {
        $output->writeln('This is a PARTIAL UPDATE');

        $rawHashes = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'];
        unset($jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes']);
        $checksum = $jsonA['listUpdateResponses'][0]['checksum']['sha256'];
        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];
        $removals = $jsonA['listUpdateResponses'][0]['removals'][0]['rawIndices']['indices'];

        $output->writeln('Updating metadata...');
        $this->em->getRepository(SBLinkMeta::class)->createMetaData($checksum, $newClientState, $prefixSize);
        $output->writeln('Permorming deletions and additions...');

        try {
            $this->em->getRepository(SBLink::class)->addAndDel($rawHashes, $removals, $prefixSize);
        } catch (\Exception $e) {
            $output->writeln('ERROR : '.$e->getMessage());
        }
    }
}
