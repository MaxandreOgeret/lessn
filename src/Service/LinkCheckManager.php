<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 09:34
 */

namespace App\Service;

use App\Entity\Log;

class LinkCheckManager
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }


}