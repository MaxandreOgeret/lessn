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
use Monolog\Logger;
use function PhpParser\canonicalize;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SafebrowsingValidator extends ConstraintValidator
{
    private $em;
    private $canonicalizeManager;
    private $sbLinkMetaRepository;
    private $prefixManager;
    private $hashManager;
    private $sbLinkRepository;

    /**
     * SafebrowsingValidator constructor.
     * @param EntityManagerInterface $em
     * @param CanonicalizeManager $canonicalizeManager
     * @param SBLinkMetaRepository $sbLinkMetaRepository
     * @param SuffixPrefixManager $prefixManager
     * @param HashManager $hashManager
     * @param SBLinkRepository $sbLinkRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        CanonicalizeManager $canonicalizeManager,
        SBLinkMetaRepository $sbLinkMetaRepository,
        SuffixPrefixManager $prefixManager,
        HashManager $hashManager,
        SBLinkRepository $sbLinkRepository
    ) {
        $this->em = $em;
        $this->canonicalizeManager = $canonicalizeManager;
        $this->sbLinkMetaRepository = $sbLinkMetaRepository;
        $this->prefixManager = $prefixManager;
        $this->hashManager = $hashManager;
        $this->sbLinkRepository = $sbLinkRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        $canon = $this->canonicalizeManager->canonicalize($value);
        $hashLength = $this->sbLinkMetaRepository->getHashLength();
        $prefixes = $this->prefixManager->createSuffixPrefix($canon);

        $hashList = [];
        foreach ($prefixes as $prefix) {
            $hashList[] = $this->hashManager->truncatedHashUrl($prefix, $hashLength*8);
        }

        if ($this->sbLinkRepository->areBanned($hashList)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
