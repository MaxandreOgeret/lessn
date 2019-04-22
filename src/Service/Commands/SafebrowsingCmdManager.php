<?php


namespace App\Service\Commands;

use App\Entity\SBLink;
use App\Entity\SBLinkMeta;
use Doctrine\ORM\EntityManagerInterface;
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

    public function curlExecAndSave($curlInstance, $path)
    {
        $result = curl_exec($curlInstance);
        file_put_contents($path.'/SBresponse.txt', $result);
        return $path.'/SBresponse.txt';
    }

    public function parseAndSave($filePath, $output)
    {
        $jsonString = file_get_contents($filePath);
        $jsonA = json_decode($jsonString, true);

        $rawHashes = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'];
        unset($jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes']);
        $checksum = $jsonA['listUpdateResponses'][0]['checksum']['sha256'];
        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];

        $this->em->getRepository(SBLinkMeta::class)->createMetaData($checksum, $newClientState, $prefixSize);
        $this->em->getRepository(SBLink::class)->createHashes($rawHashes, $prefixSize, $output);
    }

    public function parseAndSaveEdits($filePath, $output)
    {
        $jsonString = file_get_contents($filePath);
        $jsonA = json_decode($jsonString, true);

        $checksum = $jsonA['listUpdateResponses'][0]['checksum']['sha256'];
        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];
        $removals = $jsonA['listUpdateResponses'][0]['removals'][0]['rawIndices']['indices'];

        $output->writeln('Updating metadata...');
        $this->em->getRepository(SBLinkMeta::class)->createMetaData($checksum, $newClientState, $prefixSize);
        $output->writeln('Deleting old hashes and rebuilding ID...');
        $this->em->getRepository(SBLink::class)->deleteHashList($removals);
    }
}
