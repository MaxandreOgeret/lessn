<?php


namespace App\Service\Commands;

use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;

class SafebrowsingCmdManager
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function buildJsonBody()
    {
        return
            '{
  "client": {
    "clientId":       "lessn",
    "clientVersion":  "'.exec('git describe --tags --abbrev=0').'"
  },
  "listUpdateRequests": [{
    "threatType":      "SOCIAL_ENGINEERING",
    "platformType":    "ANY_PLATFORM",
    "threatEntryType": "URL",
    "state":           "",
    "constraints": {
      "region":                "EU",
      "supportedCompressions": ["RAW"]
    }
  }]
}';
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
        //todo: remove comments
//        $result = curl_exec($curlInstance);
//        file_put_contents($path.'/SBresponse.txt', $result);
        return $path.'/SBresponse.txt';
    }

    public function parseAndSave($filePath)
    {
        $jsonString = file_get_contents($filePath);
        $jsonA = json_decode($jsonString, true);


        $newClientState = $jsonA['listUpdateResponses'][0]['newClientState'];
        $prefixSize = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['prefixSize'];
        $rawHashes = $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'];
        $jsonA['listUpdateResponses'][0]['additions'][0]['rawHashes']['rawHashes'] = '';

        dump($jsonA);
    }
}