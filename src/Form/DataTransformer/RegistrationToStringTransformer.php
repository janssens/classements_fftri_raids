<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 03/03/19
 * Time: 09:17
 */

namespace App\Form\DataTransformer;

use App\Entity\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RegistrationToStringTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
    * Transforms an object (Registration) to a string (registration id).
    *
    * @param  Registration|null $registration
    * @return string
    */
    public function transform($registration)
    {
        if (null === $registration) {
            return '';
        }

        return $registration->getId();
    }

    /**
     * Transforms a string (registration id) to an object (Registration).
     *
     * @param  string $id
     * @return Registration
     * @throws TransformationFailedException if object (user) is not found
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return;
        }

        $registrationRepo = $this->entityManager->getRepository(Registration::class);
        /** @var Registration $registration */
        $registration = $registrationRepo->find($id);

        if (null === $registration) {
            throw new TransformationFailedException(sprintf(
                'Aucune licence trouvé avec ce numéro "%s" !',
                $id
            ));
        }

        return $registration;
    }
}
