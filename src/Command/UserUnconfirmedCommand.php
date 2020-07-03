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
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\TwigSwiftMailer;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Constraints\DateTime;

class UserUnconfirmedCommand extends ContainerAwareCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:unconfirmed';

    protected function configure()
    {
        $this
            ->setDescription('List and send a mail to all unconfirmed users')
            ->setHelp('List and send a mail to all unconfirmed users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit= $input->getOption('limit');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        $users = $em->getRepository(\App\Entity\User::class)->findAll();

        /** @var \App\Entity\User $user */
        foreach ($users as $user){
            if ($user->getConfirmationToken()){
                $output->write($user->getId());
                $output->write(':');
                $output->write($user->getEmail());
                $output->write(':');
                $output->write($user->getConfirmationToken());
                $output->writeln('');
                $mailer = new TwigSwiftMailer(
                    $this->getContainer()->get('mailer'),
                    $this->getContainer()->get('router'),
                    $this->getContainer()->get('twig'),
                    array('from_email'=>array('confirmation'=>$_ENV['FROM_EMAIL']),'template'=>array('confirmation'=>'bundles/FOSUserBundle/Registration/email.txt.twig')));
                $mailer->sendConfirmationEmailMessage($user);

            }
        }

        $output->writeln('');

    }

}
