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
use App\Service\UriManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Uri\Parser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidLessnLinkValidator extends ConstraintValidator
{
    private $em;
    private $uriManager;
    private $parser;

    /**
     * ValidLessnLinkValidator constructor.
     * @param EntityManagerInterface $em
     * @param UriManager $uriManager
     */
    public function __construct(EntityManagerInterface $em, UriManager $uriManager)
    {
        $this->em = $em;
        $this->uriManager = $uriManager;
        $this->parser = new Parser();
    }

    public function validate($value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        if (!$this->uriManager->isLessnUrl($value)) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        $uuid = $this->uriManager->getUuidFromUrl($value);

        $link = $this->em->getRepository(Link::class)->findOneBy(['uuid' => $uuid]);
        if (is_null($link)) {
            $this->context->buildViolation($constraint->messageEmpty)->addViolation();
        }
    }
}
