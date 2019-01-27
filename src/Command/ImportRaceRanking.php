<?php
// src/Command/ImportData.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Outsider;
use App\Entity\Race;
use App\Entity\Registration;
use App\Entity\Team;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints\DateTime;

class ImportRaceRanking extends CsvCommand
{

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import:race_ranking';

    protected function configure()
    {
        $this
            ->setDescription('Import / update data')
            ->setHelp('This command allows you to import updated data from fftri...')
            ->addArgument('file', InputArgument::REQUIRED, 'Csv file source')
            ->addOption('delimiter','d',InputOption::VALUE_OPTIONAL,'csv delimiter',';')
            ->addOption('default_mapping',null,InputOption::VALUE_NONE,'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $delimiter= $input->getOption('delimiter');
        $default_mapping= $input->getOption('default_mapping');

        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'import data',
            '============',
        ]);

        $lines = $this->getLines($file) - 1;
        $output->writeln("<info>Dealing with $lines lines</info>");

        $progress = new ProgressBar($output);
        $progress->setMaxSteps($lines);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $question = new Question('Race name : ', explode('.',$file)[0]);
        $helper = $this->getHelper('question');
        $raceName = $helper->ask($input, $output, $question);

        $race = $em->getRepository(Race::class)->findOneBy(array('name'=>$raceName));

        if (!$race){
            $question = new Question('Number of athletes per team : ', 2);
            $helper = $this->getHelper('question');
            $athlete_per_team = $helper->ask($input, $output, $question);

            $race = new Race();
            $race->setName($raceName);
            $race->setDate(new \DateTime());
            $race->setLat(0);
            $race->setLon(0);
            $race->setAthletesPerTeam($athlete_per_team);

            $em->persist($race);
            $em->flush();
        }

        $neededFields = array(
            'position' => array('label' => 'Classement scratch','index'=>0),
            'team_name' => array('label' => 'Nom d\'équipe','index'=>1),
        );
        for ($i = 0; $i < $race->getAthletesPerTeam() ; $i++){
            $neededFields['firstname_'.$i] = array('label' => 'Prénom athlète #'.($i+1),'index'=>$i*4+2);
            $neededFields['lastname_'.$i] = array('label' => 'Nom athlète #'.($i+1),'index'=>$i*4+3);
            $neededFields['number_'.$i] = array('label' => 'Licence athlète #'.($i+1),'index'=>$i*4+4);
            $neededFields['gender_'.$i] = array('label' => 'Sexe athlète #'.($i+1),'index'=>$i*4+5);
        }
        $this->setNeededFields($neededFields);

        if (!$default_mapping)
            $this->mapField($file,$delimiter,$input,$output);

        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $row++;
                if ($row > 1) { //skip first line
                    $progress->advance();

                    $team = $em->getRepository(Team::class)->findOneBy(array('name' => $this->getField('team_name', $data), 'race' => $race));
                    if (!$team) {
                        $team = new Team();
                        $team->setName($this->getField('team_name', $data));
                    }
                    $team->setPosition(intval($this->getField('position', $data)));

                    for ($i = 0; $i < $race->getAthletesPerTeam(); $i++) {
                        if (strlen($this->getField('number_' . $i, $data)) > 3) {
                            $registration = $em->getRepository(Registration::class)->findOneByLicence($this->getField('number_' . $i, $data));
                            if ($registration) {
                                $team->addRegistration($registration);
                            } else {
                                $outsider = new Outsider();
                                $outsider->setFirstname($this->getField('firstname_' . $i, $data));
                                $outsider->setLastname($this->getField('lastname_' . $i, $data));
                                $outsider->setGender(($this->getField('gender_' . $i, $data) == 'm') ? Athlete::MALE : Athlete::FEMALE);
                                $outsider->setNumber($this->getField('number_' . $i, $data));
                                $em->persist($outsider);
                                $team->addOutsider($outsider);
                            }
                        } else {
                            $outsider = new Outsider();
                            $outsider->setFirstname($this->getField('firstname_' . $i, $data));
                            $outsider->setLastname($this->getField('lastname_' . $i, $data));
                            $outsider->setGender(($this->getField('gender_' . $i, $data) == 'm') ? Athlete::MALE : Athlete::FEMALE);
                            $outsider->setNumber(null);
                            $em->persist($outsider);
                            $team->addOutsider($outsider);
                        }
                    }
                    $team->setRace($race);

                    $em->persist($team);
                }
            }
            $em->flush();
            fclose($handle);
        }
        $progress->finish();
        $output->writeln('');

    }
}