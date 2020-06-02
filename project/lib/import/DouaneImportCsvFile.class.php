<?php

class DouaneImportCsvFile {

    protected $filePath = null;
    protected $doc = null;
    protected $campagne = null;
    protected $configuration = null;

    public function __construct($filePath, $doc = null) {
        $this->filePath = $filePath;
        $this->doc = $doc;
        $this->campagne = ($doc)? $doc->campagne : date('Y');
        $this->configuration = ConfigurationClient::getConfiguration();
        set_time_limit(30000);
    }

    public static function clean($array) {
      for($i = 0 ; $i < count($array) ; $i++) {
        $array[$i] = preg_replace("/[ ]+/", " ", preg_replace('/^ +/', '', preg_replace('/ +$/', '', $array[$i])));
      }
      return $array;
    }

    public static function numerizeVal($val, $nbDecimal = 2) {
    	return (is_numeric($val))? str_replace('.', ',', sprintf('%01.'.$nbDecimal.'f', $val)) : $val;
    }

    public static function cleanStr($val) {
    	return str_replace(';', ' - ', preg_replace('/^ */', '', preg_replace('/ *$/', '', str_replace(array("\r", "\r\n", "\n"), ' ', html_entity_decode($val)))));
    }

    public static function getNewInstanceFromType($type, $file, $doc = null)  {
        switch ($type) {
            case 'DR':
                return new DRDouaneCsvFile($file, $doc);
            case 'SV11':
                return new SV11DouaneCsvFile($file, $doc);
            case 'SV12':
                return new SV12DouaneCsvFile($file, $doc);
        }

        return null;
    }
    public static function getTypeFromFile($file)  {
      if (preg_match('/sv11/i', $file)) {
        return 'SV11';
      }
      if (preg_match('/sv12/i', $file)) {
        return 'SV12';
      }
      return 'DR';
    }

    public function getCsvType() {
      if (is_a($this, 'SV11DouaneCsvFile')) {
        return "SV11";
      }
      if (is_a($this, 'SV12DouaneCsvFile')) {
        return "SV12";
      }
      if (is_a($this, 'DRDouaneCsvFile')) {
        return "DR";
      }
    }

    public static function cleanRaisonSociale($s) {
      return '"'.preg_replace('/ -$/', '', trim(preg_replace('/  */', ' ', str_replace('"', ' - ', preg_replace('/"$/', '', preg_replace('/^"/', '', $s)))))).'"';
    }

    public function getEtablissementRows() {
      $doc = array();
      $doc[] = $this->getCsvType();
      $doc[] = $this->campagne;
      if (!isset($this->etablissement)) {
        $this->etablissement = null;
      }
      if (!$this->etablissement) {
        $this->etablissement = ($this->doc)? $this->doc->getEtablissementObject() : null;
      }
      if (!$this->etablissement && $this->cvi) {
        $this->etablissement = EtablissementClient::getInstance()->findByCvi($this->cvi);
      }
      $doc[] = ($this->etablissement)? $this->etablissement->identifiant : null;
      if ($this->etablissement) {
        $doc[] = $this->etablissement->cvi ;
        $doc[] = self::cleanRaisonSociale($this->etablissement->raison_sociale);
        $doc[] = null;
        $doc[] = $this->etablissement->siege->commune;
      }else {
        $doc[] = ($this->cvi) ? $this->cvi : null;
        $rs = (isset($this->raison_sociale)) ? $this->raison_sociale : null;
        $doc[] = self::cleanRaisonSociale($rs);
        $doc[] = null;
        $doc[] = (isset($this->commune)) ? $this->commune : null;
      }
      return $doc;
    }

    public function setCampagne($c){
      $this->campagne = $c;
    }

}
