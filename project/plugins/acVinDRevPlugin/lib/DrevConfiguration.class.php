<?php

class DRevConfiguration {

    private static $_instance = null;
    protected $configuration;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new DRevConfiguration();
        }
        return self::$_instance;
    }

    public function __construct() {
        if(!sfConfig::has('drev_configuration_drev')) {
			throw new sfException("La configuration pour les drev n'a pas été défini pour cette application");
		}

        $this->configuration = sfConfig::get('drev_configuration_drev', array());
    }

    public function hasPrelevements() {

        return isset($this->configuration['prelevements']) && boolval($this->configuration['prelevements']);
    }

    public function hasImportWithMentionsComplementaire() {

        return isset($this->configuration['import_with_mentions_complementaire']) && boolval($this->configuration['import_with_mentions_complementaire']);
    }

    public function hasMentionsCompletaire() {

        return isset($this->configuration['mentions_complementaire']) && boolval($this->configuration['mentions_complementaire']);
    }

    public function hasDenominationAuto() {

      return isset($this->configuration['denomination_auto']) && boolval($this->configuration['denomination_auto']);
    }

    public function hasExploitationSave() {
      return isset($this->configuration['exploitation_save']) && boolval($this->configuration['exploitation_save']);
    }

    public function hasOdgProduits() {
      return isset($this->configuration['odg']) && count($this->configuration['odg']);
    }

    public function getOdgProduits($odgName) {
      if(!isset($this->configuration['odg']) || !array_key_exists($odgName,$this->configuration['odg']) || !isset($this->configuration['odg'][$odgName]['produits']) ){
        return array();
      }
      return $this->configuration['odg'][$odgName]['produits'];
    }

    public function getOdgINAOHabilitationFile($odgName) {
      if(!isset($this->configuration['odg']) || !array_key_exists($odgName,$this->configuration['odg']) || !isset($this->configuration['odg'][$odgName]['inao']) ){
        return null;
      }
      return $this->configuration['odg'][$odgName]['inao'];
    }

    public function getOdgRegions(){
      if(!$this->hasOdgProduits()){
        return array();
      }
      return array_keys($this->configuration['odg']);
    }

    public function hasHabilitationINAO() {
        return isset($this->configuration['habilitation_inao']) && ($this->configuration['habilitation_inao']);
    }

    public function getOdgRegionInfos($region){
        if(!isset($this->configuration['odg']) || !array_key_exists($region,$this->configuration['odg']) || !isset($this->configuration['odg'][$region]) ){
            return null;
        }
        $odgInfos = array();
        foreach ($this->configuration['odg'][$region] as $key => $value) {
          if(is_string($value) && preg_match("/^%.+%$/",$value)){
            $odgInfos[$key] = sfConfig::get(str_replace("%",'',$value), '');
          }else{
            $odgInfos[$key] = $value;
          }
        }
        return $odgInfos;
    }

    public function hasValidationOdg(){
      return isset($this->configuration['validation_odg']) && boolval($this->configuration['validation_odg']);
    }

    public function hasCgu(){
      return isset($this->configuration['cgu']) && boolval($this->configuration['cgu']);
    }

}
