<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Championship;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220811172734 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $championships = $em->getRepository(Championship::class)->findAll();

        $secrets = [];
        /** @var Championship $championship */
        foreach ($championships as $championship){
            if (!$championship->getSecret()){
                do {
                    $secret = self::getRandomString(5);
                } while (in_array($secret,$secrets));
                $secrets[] = $secret;
                $championship->setSecret($secret);
                $em->persist($championship);
            }
        }
        $em->flush();
    }

    public function down(Schema $schema): void
    {
    }

    static function  getRandomString($n=5) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
