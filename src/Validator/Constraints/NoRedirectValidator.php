<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 17:10
 */

namespace App\Validator\Constraints;

use App\Entity\BannedLink;
use App\Entity\Link;
use App\Repository\SBLinkMetaRepository;
use App\Repository\SBLinkRepository;
use App\Service\SafeBrowsing\CanonicalizeManager;
use App\Service\SafeBrowsing\HashManager;
use App\Service\SafeBrowsing\SuffixPrefixManager;
use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Uri\Parser;
use function PhpParser\canonicalize;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoRedirectValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $value);
        $out = curl_exec($ch);

        $out = str_replace("\r", "", $out);
        $headers_end = strpos($out, "\n\n");
        if ($headers_end !== false) {
            $out = substr($out, 0, $headers_end);
        }

        $headers = explode("\n", $out);
        foreach ($headers as $header) {
            if (substr($header, 0, 10) == "Location: ") {
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        }
    }
}
