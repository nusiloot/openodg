<?php
/**
 * Model for DR
 *
 */

class DR extends BaseDR implements InterfaceMouvementDocument {

	protected $mouvement_document = null;

	public function __construct() {
		parent::__construct();
	}

	public function __clone() {
		parent::__clone();
	}

	protected function initDocuments() {
		parent::initDocuments();
        $this->mouvement_document = new MouvementDocument($this);
    }

	public function constructId() {
		$this->set('_id', 'DR-' . $this->identifiant . '-' . $this->campagne);
	}

	public function getConfiguration() {

		return ConfigurationClient::getConfiguration($this->campagne.'-12-10');
	}

    public static function isPieceEditable($admin = false) {
    	return ($admin)? true : false;
    }

	/**** MOUVEMENTS ****/

    public function getTemplateFacture() {
        return TemplateFactureClient::getInstance()->find("TEMPLATE-FACTURE-AOC-".$this->getCampagne());
    }

    public function getMouvements() {

        return $this->_get('mouvements');
    }

    public function getMouvementsCalcule() {
      $templateFacture = $this->getTemplateFacture();

      if(!$templateFacture) {
          return array();
      }

			$drev = DRevClient::getInstance()->findMasterByIdentifiantAndCampagne($this->identifiant, $this->getCampagne());
			if($this->getTotalValeur("15") && !$drev){
			 	throw new FacturationPassException("L15 et pas de Drev : ".$this->_id." on skip la facture");
			}

      $cotisations = $templateFacture->generateCotisations($this);

      if($this->hasVersion()) {
          $cotisationsPrec = $templateFacture->generateCotisations($this->getMother());
      }

      $identifiantCompte = $this->getIdentifiant();

      $mouvements = array();

      $rienAFacturer = true;

      foreach($cotisations as $cotisation) {
          $mouvement = DRMouvement::freeInstance($this);
          $mouvement->fillFromCotisation($cotisation);
          $mouvement->facture = 0;
          $mouvement->facturable = 1;
          $mouvement->date = $this->getCampagne().'-12-10';
          $mouvement->date_version = $mouvement->date;
          $mouvement->version = $this->version;

          if(isset($cotisationsPrec[$cotisation->getHash()])) {
              $mouvement->quantite = $mouvement->quantite - $cotisationsPrec[$cotisation->getHash()]->getQuantite();
          }

          if(!$mouvement->quantite) {
              continue;
          }

          if($mouvement->quantite) {
              $rienAFacturer = false;
          }

          $mouvements[$mouvement->getMD5Key()] = $mouvement;
      }

      if($rienAFacturer) {
          return array($identifiantCompte => array());

      }

      return array($identifiantCompte => $mouvements);
    }

    public function getMouvementsCalculeByIdentifiant($identifiant) {

        return $this->mouvement_document->getMouvementsCalculeByIdentifiant($identifiant);
    }

    public function generateMouvements() {

        return $this->mouvement_document->generateMouvements();
    }

    public function findMouvement($cle, $id = null){
      return $this->mouvement_document->findMouvement($cle, $id);
    }

    public function facturerMouvements() {

        return $this->mouvement_document->facturerMouvements();
    }

    public function isFactures() {

        return $this->mouvement_document->isFactures();
    }

    public function isNonFactures() {

        return $this->mouvement_document->isNonFactures();
    }

    public function clearMouvements(){
        $this->remove('mouvements');
        $this->add('mouvements');
    }

    /**** FIN DES MOUVEMENTS ****/

	public function hasVersion() {

		return false;
	}

	public function getVersion() {

		return null;
	}

}
