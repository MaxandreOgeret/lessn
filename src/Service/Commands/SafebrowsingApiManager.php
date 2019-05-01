<?php


namespace App\Service\Commands;

use App\Entity\SBLinkMeta;
use Doctrine\ORM\EntityManagerInterface;

class SafebrowsingApiManager
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
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
        $result = curl_exec($curlInstance);
        file_put_contents($path.'/'.$filename, $result);
        return $path.'/'.$filename;
    }
}
