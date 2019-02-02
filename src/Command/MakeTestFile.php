<?php
// src/Command/ImportData.php
namespace App\Command;

use App\Entity\Athlete;
use App\Entity\Club;
use App\Entity\Ligue;
use App\Entity\OfficialTeam;
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

class MakeTestFile extends CsvCommand
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:export:test_data';

    protected function configure()
    {
        $this
            ->setDescription('Filter input data to keep only racing athlete')
            ->setHelp('Filter input data to keep only racing athlete')
            ->addArgument('file', InputArgument::REQUIRED, 'Csv file source')
            ->addArgument('output_file', InputArgument::REQUIRED, 'Csv file output')
            ->addOption('delimiter','d',InputOption::VALUE_OPTIONAL,'csv delimiter',';')
            ->addOption('limit','l',InputOption::VALUE_OPTIONAL,'limit')
            ->addOption('dry_run',null,InputOption::VALUE_NONE,'dry run')
            ->addOption('default_mapping',null,InputOption::VALUE_NONE,'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $output_file= $input->getArgument('output_file');
        $delimiter= $input->getOption('delimiter');
        $limit= $input->getOption('limit');
        $default_mapping= $input->getOption('default_mapping');

        $delimiter = $this->checkDelimiter($file,$delimiter,$input,$output);

        $output->writeln([
            '=======================================',
            ' Export used registrations to csv test ',
            '=======================================',
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
        if ($limit){
            $lines = min($lines,$limit);
        }
        $output->writeln("<info>Dealing with $lines lines</info>");





        $em = $this->getContainer()->get('doctrine')->getManager();
        $ots = $em->getRepository(OfficialTeam::class)
            ->findAll();
        $emails = array();

        $progress = new ProgressBar($output);
        $progress->setMaxSteps(count($ots));

        foreach ($ots as $ot){
            foreach ($ot->getTeam()->getRegistrations() as $registration){
                $emails[] = $registration->getAthlete()->getEmail();
                $progress->advance();
            }
        }

        $progress->finish();


        $progress = new ProgressBar($output);
        $progress->setMaxSteps($lines);

        $row = 0;

        $output_file_handler = fopen($output_file, "w");

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                $row++;
                if ($limit and $limit <= $row){
                    break;
                }
                $data = array_map("utf8_encode", $data); //utf8
                if ($row > 1) { //skip first line
                    $progress->advance();

                    $email = $this->getField('email',$data);

                    $athlete = $em->getRepository(Athlete::class)
                        ->findOneBy(array('email'=>$email));

                    if ($athlete){
                        fputcsv($output_file_handler,$data,$delimiter);
                    }

                }else{
                    fputcsv($output_file_handler,$data,$delimiter);
                }
            }
            fclose($handle);
            fclose($output_file_handler);
        }
        //$progress->finish();
        $output->writeln('');

    }

}
