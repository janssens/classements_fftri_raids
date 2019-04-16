<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Outsider;
use AppBundle\Entity\User;
use AppBundle\EventListener\SetFirstPasswordListener;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416065616 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $query = $em->getRepository(Outsider::class)->createQueryBuilder('o')
            ->where('o.uid IS NULL')
            ->getQuery();

        $outsiders = $query->getResult();

        foreach ($outsiders as $outsider){
            $outsider->setInitialUid();
            $em->persist($outsider);
        }
        $em->flush();

    }

    public function down(Schema $schema) : void
    {

    }
}
