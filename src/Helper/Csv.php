<?php
namespace App\Helper;


class Csv
{
    private $_neededFields;
    private $_delimiter = ';';

    /**
     * @param string $file
     * @param string $delimiter
     */
    public function checkDelimiter($file,$delimiter,$nbOfColumn = 2){
        $f = fopen($file, 'r');
        $line = fgets($f);
        fclose($f);
        $count = substr_count($line, $delimiter);
        if ($count <= $nbOfColumn){
            return false;
        }
        $this->setDelimiter($delimiter);
        return $delimiter;
    }

    /**
     * @param string $file
     * @param string $delimiter
     */
    static function guessDelimiter($file){
        $f = fopen($file, 'r');
        $line = fgets($f);
        fclose($f);

        $d = false;
        $delimiters = [',',';'];
        $count = 0;
        foreach ($delimiters as $delimiter){
            $c = substr_count($line, $delimiter);
            if ($c>$count){
                $count = $c;
                $d = $delimiter;
            }
        }
        return $d;
    }

    static function isMale($label){
        $label = trim(strtolower($label));
        return in_array($label,['h','male','homme','m']);
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter($delimiter){
        $this->_delimiter = $delimiter;
    }

    /**
     * @param string $delimiter
     */
    public function getDelimiter(){
        return $this->_delimiter;
    }

    /**
     * @param $array
     */
    public function setNeededFields($array){
        $this->_neededFields = $array;
    }

    /**
     * @return array
     */
    public function getNeededFields(){
        return $this->_neededFields;
    }

    public function getMap(){
        $fields = $this->getNeededFields();
        $map = array();
        foreach ($fields as $key=>$value){
            $map[] = $value['index'];
        }
        return $map;
    }

    /**
     * @param array $map
     */
    public function setMap($map){
        $fields = $this->getNeededFields();
        $index = 0;
        foreach ($fields as $key=>$value){
            $fields[$key]['index'] = $map[$index++];
        }
        $this->setNeededFields($fields); // "save"
    }

    /**
     * @param integer $key
     * @param array $data
     * @return null|string
     */
    public function getField($key,$data){
        if (isset($this->getNeededFields()[$key]['index']) && $this->getNeededFields()[$key]['index'] >= 0)
            return trim(strtolower($data[$this->getNeededFields()[$key]['index']]));
        else{
            if (isset($this->getNeededFields()[$key]['default'])){
                return $this->getNeededFields()[$key]['default'];
            }else{
                return null;
            }
        }
    }

    /**
     * @param string $file
     * @return int number of line
     */
    static function getLines($file)
    {
        $f = fopen($file, 'rb');
        $lines = 0;

        while (!feof($f)) {
            $lines += substr_count(fread($f, 8192), "\n");
        }

        fclose($f);

        return $lines;
    }
}