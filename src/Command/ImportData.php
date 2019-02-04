<?php
// src/Command/ImportData.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Club;
use App\Entity\Ligue;
use App\Entity\Outsider;
use App\Entity\Registration;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Constraints\DateTime;

class ImportData extends CsvCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import:registrations';

    protected function configure()
    {
        $this
            ->setDescription('Import / update data')
            ->setHelp('This command allows you to import updated data from fftri...')
            ->addArgument('file', InputArgument::REQUIRED, 'Csv file source')
            ->addOption('delimiter','d',InputOption::VALUE_OPTIONAL,'csv delimiter',';')
            ->addOption('limit','l',InputOption::VALUE_OPTIONAL,'limit')
            ->addOption('start','s',InputOption::VALUE_OPTIONAL,'start at',1)
            ->addOption('dry_run',null,InputOption::VALUE_NONE,'dry run')
            ->addOption('default_mapping',null,InputOption::VALUE_NONE,'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $delimiter= $input->getOption('delimiter');
        $dry_run= $input->getOption('dry_run');
        $limit= $input->getOption('limit');
        $start=$input->getOption('start');
        $default_mapping= $input->getOption('default_mapping');

        $delimiter = $this->checkDelimiter($file,$delimiter,$input,$output);

        $output->writeln([
            '====================================',
            'Import registrations data from a csv',
            '====================================',
        ]);

        $this->setNeededFields(array(
            'number' => array('label' => 'Numero de licence','index'=>2),
            'firstname' => array('label' => 'Prénom','index'=>3),
            'lastname' => array('label' => 'Nom','index'=>4),
            'dob' => array('label' => 'Date de naissance (d/m/Y)','index'=>5),
            'sex' => array('label' => 'Genre (f/m)','index'=>6),
            'email' => array('label' => 'Email','index'=>17),
            'ligue' => array('label' => 'Ligue','index'=>32),
            'club' => array('label' => 'Club','index'=>33),
            'type' => array('label' => 'Type de licence','index'=>34),
            'is_long' => array('label' => 'Licence longue (Oui/Non)','index'=>35,"required" => false,"default" =>'non'),
            'date' => array('label' => 'Date de validation de la licence (d/m/Y)','index'=>39,"required" => false,"default" => '01/01/2019'),
            'ask_date' => array('label' => 'Date de demande de la licence (d/m/Y)','index'=>40,"required" => false,"default" => '01/01/2019'),
        ));

        if (!$default_mapping)
            $this->mapField($file,$delimiter,$input,$output);

        $lines = $this->getLines($file) - 1;
//        if ($limit){
//            $lines = min($lines,$limit);
//        }
        $output->writeln("<info>Dealing with $lines lines</info>");
        if ($start<1 or $start > $lines) {
            $start = 1;
        }
        if ($start != 1){
            $output->writeln("<info>Starting at $start</info>");
        }

        $progress = new ProgressBar($output);
        $progress->setMaxSteps($lines);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $processed = 0;

        $index = $start;
        $progress->advance($start-1);
        while ($index < $lines){
            if (($handle = fopen($file, "r")) !== FALSE) {
                $row = $start-1;
                $goto = $start;
                while ((--$goto > 0) && (fgets($handle, 10000) !== FALSE)) { }

                while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                    $row++;
                    if ($limit and $limit <= $processed){
                        break;
                    }
                    $data = array_map("utf8_encode", $data); //utf8
                    if ($row > $start+1) { //skip first line
                        $progress->advance();

                        $date = date_create_from_format('d/m/Y',$this->getField('date',$data));
                        $number = strtoupper($this->getField('number',$data));

                        $output->writeln('',OutputInterface::VERBOSITY_DEBUG);
                        if ($date && $number) {
                            $registration_exist = $em->getRepository(Registration::class)
                                ->findOneByDateAndNumber($date,$number);
                            if ($registration_exist) { //update
                                $registration = $registration_exist;
                                $output->writeln('<info>Registration exist, update</info>',OutputInterface::VERBOSITY_DEBUG);
                            }else{//new registration
                                $registration = new Registration();
                                $registration->setDate($date);
                                $registration->setNumber($number);
                            }

                            $ask_date = date_create_from_format('d/m/Y',$this->getField('ask_date',$data));
                            $registration->setAskDate($ask_date);

                            $registration->setDate($date);
                            $registration->setStartDate($date);

                            switch ($this->getField('type',$data)[0]){
                                case 'a':
                                    $registration->setType(Registration::TYPE_A);
                                    break;
                                case 'b':
                                    $registration->setType(Registration::TYPE_B);
                                    break;
                                case 'c':
                                    $registration->setType(Registration::TYPE_C);
                                    break;
                                case 'd':
                                    $registration->setType(Registration::TYPE_D);
                                    break;
                                case 'e':
                                    $registration->setType(Registration::TYPE_E);
                                    break;
                                case 'f':
                                    $registration->setType(Registration::TYPE_F);
                                    break;
                                case 'g':
                                    $registration->setType(Registration::TYPE_G);
                                    break;
                                case 'h':
                                    $registration->setType(Registration::TYPE_H);
                                    break;
                                case 'i':
                                    $registration->setType(Registration::TYPE_I);
                                    break;
                                case 'j':
                                    $registration->setType(Registration::TYPE_J);
                                    break;
                                case 'k':
                                    $registration->setType(Registration::TYPE_K);
                                    break;
                                case 'l':
                                    $registration->setType(Registration::TYPE_L);
                                    break;
                                case 'm':
                                    $registration->setType(Registration::TYPE_M);
                                    break;
                                default:
                                    $registration->setType(Registration::TYPE_NULL);
                            }

                            $registration->setIsLong(($this->getField('is_long',$data) == "oui"));

                            $club_name = $this->getField('club',$data);
                            if ($club_name) {
                                //club
                                $slug = $this->createSlug($club_name);
                                $club_exist = $em->getRepository(Club::class)
                                    ->findOneBySlug($slug);
                                if ($club_exist && $club_exist->getId()) {
                                    $club = $club_exist;
                                } else {
                                    $club = new Club();
                                    $club->setSlug($slug);
                                    $club->setName($club_name);
                                    $em->persist($club);
                                    $em->flush(); //persist new club
                                }
                                $registration->setClub($club);
                            }

                            $ligue_name = $this->getField('ligue',$data);
                            if ($ligue_name) {
                                //ligue
                                $slug = $this->createSlug($ligue_name);
                                $ligue_exist = $em->getRepository(Ligue::class)
                                    ->findOneBySlug($slug);
                                if ($ligue_exist && $ligue_exist->getId()) {
                                    $ligue = $ligue_exist;
                                } else {
                                    $ligue = new Ligue();
                                    $ligue->setName($ligue_name);
                                    $ligue->setSlug($slug);
                                    $em->persist($ligue);
                                    $em->flush(); //persist new ligue
                                }
                                $registration->setLigue($ligue);
                            }

                            $email = $this->getField('email',$data);
                            $lastname = $this->getField('lastname',$data);
                            $firstname = $this->getField('firstname',$data);
                            $dob = date_create_from_format('d/m/Y', $this->getField('dob',$data));
                            $dob->setTime(0,0,0);
                            $athlete_exist = $em->getRepository(Athlete::class)
                                ->findOneByLastnameAndDob($lastname, $dob);
                            if ($athlete_exist) {
                                $athlete = $athlete_exist;
                                //already up to date at registration date ?
                                $same_year_registration_exist = $em->getRepository(Registration::class)
                                    ->findOneByYearAndAthlete(intval($date->format('Y')),$athlete);
                                if ($same_year_registration_exist && $same_year_registration_exist->getType() == $registration->getType() && !$registration_exist) {
                                    $registration->setStartDate(date_create_from_format('d/m/Y H:i:s','01/01/'.(intval($date->format('Y'))+1).' 00:00:00'));
                                }else{
                                    if (!$athlete->getRegistrations()){ //first registration ever
                                        if ($registration->getDate()>date_create_from_format('d/m/Y H:i:s','01/09/'.(intval($date->format('Y'))).' 00:00:00'))
                                            $registration->setIsLong(true); //primo
                                    }
                                }
                                $athlete->addRegistration($registration);
                                $athlete->setEmail($email); //change email (maybe new ?)
                            } else {
                                $athlete = new Athlete();
                                $athlete->setEmail($email);
                                $athlete->setDob($dob);
                                $athlete->setLastname($lastname);
                                $athlete->setFirstname($firstname);
                                $athlete->setGender(($this->getField('sex',$data) == 'm') ? Athlete::MALE : Athlete::FEMALE);
                                $athlete->addRegistration($registration);

                                $related_outsiders = $em->getRepository(Outsider::class)->findByRegistration($registration);
                                foreach ($related_outsiders as $outsider){
                                    $team = $outsider->getTeam();
                                    $team->addRegistration($registration);
                                    $team->removeOutsider($outsider);
                                    $em->remove($outsider);
                                    $em->persist($team);
                                }
                            }
                            $em->persist($athlete);
                        } else {
                            $output->writeln('');
                            if (!$date) {
                                $output->writeln('<error>No registration date</error>');
                            }
                            if (!$number) {
                                $output->writeln('<error>No registration number</error>');
                            }
                        }
                    }
                    $processed++;
                }
                fclose($handle);
                unset($handle);
                $em->flush();
                $output->writeln("",OutputInterface::VERBOSITY_VERBOSE);
                $output->writeln("flushing",OutputInterface::VERBOSITY_VERBOSE);
            }
            $index += $processed;
            $output->writeln($index,OutputInterface::VERBOSITY_VERBOSE);
            $start = $index;
            $processed = 0;
        }


        //$progress->finish();
        $output->writeln('');

    }



    public static function createSlug($str, $delimiter = '-'){

        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
        return $slug;

    }
}
