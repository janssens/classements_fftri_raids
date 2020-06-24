<?php
// src/Command/ImportData.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Championship;
use App\Entity\Club;
use App\Entity\Ligue;
use App\Entity\Outsider;
use App\Entity\Race;
use App\Entity\Registration;
use App\Entity\Team;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints\DateTime;

class ExportChampionshipRacers extends CsvCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:export:championship_racers';

    protected function configure()
    {
        $this
            ->setDescription('Export championship racer data')
            ->setHelp('This command allows you to export data of racer who race one or more race on a particular championship')
            ->addArgument('file', InputArgument::REQUIRED, 'Csv file destination')
            ->addOption('values',null,InputOption::VALUE_OPTIONAL,'comma separated values to export','firstname,lastname,email')
            ->addOption('delimiter','d',InputOption::VALUE_OPTIONAL,'csv delimiter',';')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output_file = $input->getArgument('file');
        $delimiter = $input->getOption('delimiter');
        $values = $input->getOption('values');
        $data_names = explode(',',$values);

        $output->writeln([
            '====================================',
            'Export racer of championship to csv ',
            '====================================',
        ]);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $championships = $em->getRepository(Championship::class)->findAll();
        $chooses = array();
        /** @var Championship $championship */
        foreach ($championships as $championship){
            $chooses[$championship->getId()] = $championship->getSeason()->__toString() . ' - ' . $championship->getName();
        }
        //$chooses[0] = 'All';

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select your championship',
            $chooses,
            0
        );
        $question->setErrorMessage('Championship %s do not exist.');

        $race = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected championship: '.$race);

        $championship_id = array_search($race,$chooses);
        $championship = $em->getRepository(Championship::class)->find($championship_id);

        $racers = array();

        foreach ($championship->getRaces() as $race){
            /** @var Team $team */
            foreach ($race->getTeams() as $team){
                foreach ($team->getRegistrations() as $registration){
                    $racers[$registration->getAthlete()->getId()] = $registration->getAthlete();
                }
            }
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $output_file_handler = fopen($output_file, "w");
        fputcsv($output_file_handler,$data_names,$delimiter);
        foreach ($racers as $racer){
            $data = array();
            foreach ($data_names as $key){
                $data[] = $propertyAccessor->getValue($racer,$key);
            }
            fputcsv($output_file_handler,$data,$delimiter);
        }
        fclose($output_file_handler);


        $output->writeln(count($racers).' athletes found');
        $output->writeln('');

    }

}
