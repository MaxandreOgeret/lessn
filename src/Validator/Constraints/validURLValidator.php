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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class validURLValidator extends ConstraintValidator
{
    const VALIDATOR_URL_REGEX = '_^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}(:[0-9]{1,6})?(\/.*)?$_iuS';
    private $em;

    /**
     * validURLValidator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match(self::VALIDATOR_URL_REGEX, $value, $matches)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }

        if (
            substr( $value, 0, mb_strlen("http://lessn.io") ) === "http://lessn.io" ||
            substr( $value, 0, mb_strlen("http://www.lessn.io") ) === "http://www.lessn.io" ||
            substr( $value, 0, mb_strlen("https://lessn.io") ) === "https://lessn.io" ||
            substr( $value, 0, mb_strlen("https://www.lessn.io") ) === "https://www.lessn.io"
        ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }


        // Phishing test

        $link = str_replace('https://', 'http://', $value);
        $exploded = array_filter(explode('http://', $link));

        foreach ($exploded as $link) {

            if (substr($link, 0, 4) === 'www.') {
                $link = substr($link, 4 );
            }

            $link = parse_url('http://www.'.$link)['host'];

            if (substr($link, 0, 4) === 'www.') {
                $link = substr($link, 4 );
            }

            // get most generic host
            $link = implode('.', array_slice(explode('.', $link), -2, 2));


            if ($this->em->getRepository(BannedLink::class)->isBanned($link)) {
                $this->context->buildViolation($constraint->banMessage)->addViolation();
                break;
            }
        }
    }
}