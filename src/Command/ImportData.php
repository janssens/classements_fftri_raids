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
        $default_mapping= $input->getOption('default_mapping');

        $output->writeln([
            '====================================',
            'Import registrations data from a csv',
            '====================================',
        ]);

        $this->setNeededFields(array(
            'number' => array('label' => 'numero de licence','index'=>1),
            'firstname' => array('label' => 'prénom','index'=>2),
            'lastname' => array('label' => 'nom','index'=>3),
            'dob' => array('label' => 'date de naissance','index'=>4),
            'sex' => array('label' => 'genre (homme/femme)','index'=>5),
            'email' => array('label' => 'e-mail','index'=>6),
            'ligue' => array('label' => 'nom de la ligue','index'=>7),
            'club' => array('label' => 'club','index'=>8),
            'type' => array('label' => 'type de licence','index'=>9),
            'date' => array('label' => 'date de validation de la licence','index'=>-1,"required" => false,"default" => date_create_from_format('d/m/Y H:i:s','01/01/2019 00:00:00')),
            'ask_date' => array('label' => 'date de demande de la licence','index'=>-1,"required" => false,"default" => date_create_from_format('d/m/Y H:i:s','01/01/2019 00:00:00')),
        ));

        if (!$default_mapping)
            $this->mapField($file,$delimiter,$input,$output);

        $lines = $this->getLines($file) - 1;
        if ($limit){
            $lines = min($lines,$limit);
        }
        $output->writeln("<info>Dealing with $lines lines</info>");

        $progress = new ProgressBar($output);
        $progress->setMaxSteps($lines);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $row = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                $row++;
                if ($limit and $limit <= $row){
                    break;
                }
                if ($row > 1) { //skip first line
                    $progress->advance();

                    $date = date_create_from_format('d/m/Y',$this->getField('date',$data));
                    $number = strtoupper($this->getField('number',$data));

                    if ($date && $number) {
                        $registration_exist = $em->getRepository(Registration::class)
                            ->findOneBy(array('number'=>$number),array('date'=>'DESC'));
                            //->findOneByDateAndNumber($date, $number);
                        if ($registration_exist && $registration_exist->getId()) {
                            continue;
                        }

                        $ask_date = date_create_from_format('d/m/Y',$this->getField('ask_date',$data));

                        //new registration
                        $registration = new Registration();
                        $registration->setDate($date);
                        $registration->setAskDate($ask_date);
                        $registration->setNumber($number);
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
                            case 'h':
                                $registration->setType(Registration::TYPE_H);
                                break;
                            default:
                                $registration->setType(Registration::TYPE_NULL);
                        }

                        $club_name = $this->getField('club',$data);
                        if ($club_name) {
                            //club
                            $club_exist = $em->getRepository(Club::class)
                                ->findOneByName($club_name);
                            if ($club_exist && $club_exist->getId()) {
                                $club = $club_exist;
                            } else {
                                $club = new Club();
                                $club->setName($club_name);
                                $em->persist($club);
                            }
                            $registration->setClub($club);
                        }

                        $ligue_name = $this->getField('ligue',$data);
                        if ($ligue_name) {
                            //club
                            $ligue_exist = $em->getRepository(Ligue::class)
                                ->findOneByName($ligue_name);
                            if ($ligue_exist && $ligue_exist->getId()) {
                                $ligue = $ligue_exist;
                            } else {
                                $ligue = new Ligue();
                                $ligue->setName($ligue_name);
                                $em->persist($ligue);
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
                            //todo update date if new
                            $athlete = $athlete_exist;
                            $athlete->addRegistration($registration);
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
            }
            fclose($handle);
            $output->writeln("");
            $output->writeln("<info>Flush</info>");
            $em->flush();
        }
        //$progress->finish();
        $output->writeln('');

    }
}