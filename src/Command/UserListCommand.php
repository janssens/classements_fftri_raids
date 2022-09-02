<?php
// src/Command/ImportData.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Club;
use App\Entity\Ligue;
use App\Entity\OfficialTeam;
use App\Entity\Outsider;
use App\Entity\Registration;
use Doctrine\ORM\EntityManager;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Constraints\DateTime;

class UserListCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:list';

    protected function configure()
    {
        $this
            ->setDescription('List all user')
            ->setHelp('Will list all app user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $users = $em->getRepository(\App\Entity\User::class)->findAll();

        /** @var \App\Entity\User $user */
        foreach ($users as $user){
            $output->write($user->getId());
            $output->write(':');
            $output->write($user->getEmail());
            $output->write(':');
            $output->write($user->getConfirmationToken());
            $output->writeln('');
        }

        $output->writeln('');

    }

}
