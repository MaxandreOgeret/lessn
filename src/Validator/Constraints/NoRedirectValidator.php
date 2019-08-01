<?php
/**
 * Created by PhpStorm.
 * User: m.ogeret
 * Date: 10/04/2018
 * Time: 17:10
 */

namespace App\Validator\Constraints;

use League\Uri\Parser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoRedirectValidator extends ConstraintValidator
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $urlHost = $this->parser->parse($value)['host'];

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
                $redirectUrl = str_replace('Location: ', '', $header);
                $redirectHost = $this->parser->parse($redirectUrl)['host'];

                // Build violation if the website redirects to another website.
                if (!in_array($redirectHost, ['www.'.$urlHost, $urlHost])) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }
        }
    }
}
