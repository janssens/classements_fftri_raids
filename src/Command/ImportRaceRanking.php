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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
            ->addOption('map','m',InputOption::VALUE_OPTIONAL,'map','')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $delimiter= $input->getOption('delimiter');
        $default_mapping= $input->getOption('default_mapping');
        $map = $input->getOption('map');

        $delimiter = $this->checkDelimiter($file,$delimiter,$input,$output);

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

        $races = $em->getRepository(Race::class)->findAll();
        $chooses = array();
        /** @var Race $race */
        foreach ($races as $race){
            $chooses[$race->getId()] = $race->getDate()->format('Y') . ' - ' . $race->getName();
        }
        $chooses[0] = 'New';

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select your race',
            $chooses,
            0
        );
        $question->setErrorMessage('Race %s do not exist.');

        $race = $helper->ask($input, $output, $question);
        $output->writeln('You have just selected race: '.$race);

        $race_id = array_search($race,$chooses);

        if ($race_id){
            $race = $em->getRepository(Race::class)->find($race_id);
        }else{
            $question = new Question('Race name: ', explode('.',$file)[0]);
            $raceName = $helper->ask($input, $output, $question);
            $question = new Question('Number of athletes per team: ', 2);
            $athlete_per_team = $helper->ask($input, $output, $question);
            $question = new Question('Date (d/m/Y): ');
            $date_string = $helper->ask($input, $output, $question);

            $race = new Race();
            $race->setName($raceName);
            $race->setDate(date_create_from_format('d/m/Y',$date_string));
            $race->setAthletesPerTeam($athlete_per_team);
            $race->setLat(null);
            $race->setLon(null);

            $em->persist($race);
            $em->flush();
        }

        $neededFields = array(
            'position' => array('label' => 'Classement scratch','index'=>0),
            'team_name' => array('label' => 'Nom d\'équipe','index'=>1),
        );
        for ($i = 0; $i < $race->getAthletesPerTeam() ; $i++){
            $neededFields['firstname_'.$i] = array('label' => 'Prénom athlète #'.($i+1),'index'=>$i*4+2,"required" => false,"default" =>'Équipier');
            $neededFields['lastname_'.$i] = array('label' => 'Nom athlète #'.($i+1),'index'=>$i*4+3,"required" => false,"default" =>'#'.($i+1));
            $neededFields['number_'.$i] = array('label' => 'Licence athlète #'.($i+1),'index'=>$i*4+4);
            $neededFields['gender_'.$i] = array('label' => 'Sexe athlète #'.($i+1),'index'=>$i*4+5);
        }
        $this->setNeededFields($neededFields);

        if (!$map && $this->loadSavedMap($file)){

        }else{
            $this->checkDelimiter($file,$delimiter,$input,$output);
            if (!$default_mapping&&!$map) {
                $this->mapField($file, $input, $output);
                $this->saveMap($file);
            }else{
                $this->setMap(explode(',',$map));
            }
            $confirm = new ConfirmationQuestion('Save this map as default ?', true);
            if ($this->getHelper('question')->ask($input, $output, $confirm)) {
                $this->saveMap($file);
            }
        }
        $output->writeln("<info>MAP : </info>");
        $this->displayMap($output);

        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $row++;
                if ($row > 1) { //skip first line
                    $progress->advance();

                    $team_name = $this->getField('team_name', $data);
                    if ($team_name and strlen($team_name)>1){
                        $team = $em->getRepository(Team::class)->findOneBy(array('name' => $team_name, 'race' => $race));
                        if (!$team) {
                            $team = new Team();
                            $team->setName($team_name);
                        }
                        $team->setPosition(intval($this->getField('position', $data)));
                        foreach ($team->getOutsiders() as $outsider){
                            $team->removeOutsider($outsider);
                            $em->remove($outsider);
                        }
                        foreach ($team->getRegistrations() as $registration){
                            $team->removeRegistration($registration);
                        }

                        for ($i = 0; $i < $race->getAthletesPerTeam(); $i++) {
                            $number = substr($this->getField('number_' . $i, $data),0,6);
                            if (strlen($number) == 6) {
                                $output->writeln("",OutputInterface::VERBOSITY_VERBOSE);
                                $output->writeln("Search athlete with licence :",OutputInterface::VERBOSITY_VERBOSE);
                                $output->writeln($number,OutputInterface::VERBOSITY_VERBOSE);
                                $registration = $em->getRepository(Registration::class)->findOneByLicenceAndRace($number,$race);
                                /** @var Registration $registration */
                                if ($registration) {
                                    $output->writeln("found #".$registration->getId(),OutputInterface::VERBOSITY_VERBOSE);
                                    $output->writeln(
                                        $registration->getAthlete()->getFirstname().' '.
                                        $registration->getAthlete()->getLastname().' '.
                                        $registration->getNumber().' '.
                                        $registration->getDate()
                                            ->format('Y-m-d'). '->' .
                                        $registration->getEndDate()
                                            ->format('Y-m-d'),OutputInterface::VERBOSITY_VERBOSE);
                                    $team->addRegistration($registration);
                                    $registration->addTeam($team);
                                } else {
                                    $output->writeln("not found",OutputInterface::VERBOSITY_VERBOSE);
                                    $outsider = new Outsider();
                                    $outsider->setFirstname($this->getField('firstname_' . $i, $data));
                                    $outsider->setLastname($this->getField('lastname_' . $i, $data));
                                    $outsider->setGender((($this->getField('gender_' . $i, $data) == 'm')||($this->getField('gender_' . $i, $data) == 'h')) ? Athlete::MALE : Athlete::FEMALE);
                                    $outsider->setNumber($this->getField('number_' . $i, $data));
                                    $outsider->setTeam($team);
                                    $em->persist($outsider);
                                    $team->addOutsider($outsider);
                                }
                            } else {
                                $outsider = new Outsider();
                                $outsider->setFirstname($this->getField('firstname_' . $i, $data));
                                $outsider->setLastname($this->getField('lastname_' . $i, $data));
                                $outsider->setGender((($this->getField('gender_' . $i, $data) == 'm')||($this->getField('gender_' . $i, $data) == 'h')) ? Athlete::MALE : Athlete::FEMALE);
                                $outsider->setNumber(null);
                                $outsider->setTeam($team);
                                $em->persist($outsider);
                                $team->addOutsider($outsider);
                            }
                        }
                        $team->setRace($race);
                        $em->persist($team);
                        $output->writeln("Team #".$team->getId()." saved.",OutputInterface::VERBOSITY_VERBOSE);
                    }
                }
            }
            $em->flush();
            fclose($handle);
        }
        $progress->finish();
        $output->writeln('');

    }
}