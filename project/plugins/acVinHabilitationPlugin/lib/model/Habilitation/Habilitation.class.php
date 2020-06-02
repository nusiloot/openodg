<?php
/**
 * Model for Habilitation
 *
 */

class Habilitation extends BaseHabilitation implements InterfaceProduitsDocument, InterfaceDeclaration {


    protected $mouvement_document = null;
    protected $version_document = null;

    public function __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    public function __clone() {
        if ($this->_id == $this->getTheoriticalId()) {
          throw new sfException("La date de l'habilitation doit être différente de celle du document d'origine");
        }
        parent::__clone();
        $this->initDocuments();
        $this->constructId();
    }

    public function getDeclarant(){
        $etablissement = EtablissementClient::getInstance()->find($this->identifiant);
        if(!$etablissement){
          return null;
        }
        $compte = CompteClient::getInstance()->find($etablissement->getCompte());
        $declarant = new stdClass();
        $declarant->nom = $etablissement->nom;
        $declarant->raison_sociale = $etablissement->raison_sociale;
        $declarant->cvi = $etablissement->cvi;
        $declarant->siret = $etablissement->getSociete()->siret;

        $declarant->adresse = $compte->adresse;
        $declarant->adresse_complementaire = $compte->adresse_complementaire;
        $declarant->commune = $compte->commune;
        $declarant->code_postal = $compte->code_postal;
        $declarant->telephone_bureau = $compte->telephone_bureau;
        $declarant->telephone_mobile = $compte->telephone_mobile;
        $declarant->email = $compte->email ;

        return $declarant;

    }

    protected function initDocuments() {
        $this->historique = array();
    }

    private function getTheoriticalId() {
      $date = str_ireplace("-","",$this->date);
      return 'HABILITATION-' . $this->identifiant. '-'. $date;
    }

    public function constructId() {
        $id = $this->getTheoriticalId();

        $this->set('_id', $id);
    }

    public function getConfiguration() {

        return acCouchdbManager::getClient('Configuration')->getConfiguration($this->date);
    }

    public function getProduitsConfig() {

        return $this->getConfiguration()->getProduitsCahierDesCharges();
    }

    public function getProduitsHabilites() {
        $produits = array();
        foreach($this->getProduits() as $produit) {
            if(!count($produit->getActivitesHabilites())) {
                continue;
            }

            $produits[$produit->getHash()] = $produit;
        }

        return $produits;
    }

    public function getProduits($onlyActive = true) {
        return $this->declaration->getProduits($onlyActive);
    }
    public function isPapier() {
        return $this->exist('papier') && $this->get('papier');
    }

    public function isAutomatique() {
        return $this->exist('automatique') && $this->get('automatique');
    }

    public function isLectureSeule() {
        return $this->exist('lecture_seule') && $this->get('lecture_seule');
    }

    public function getValidation() {

        return true;
    }

    public function getValidationOdg() {

        return $this->_get('validation_odg');
    }

    public function initDoc($identifiant,$date) {
        $this->identifiant = $identifiant;
        $this->date = $date;
        $etablissement = $this->getEtablissementObject();
        $this->constructId();
    }

    public function addProduit($hash, $date = null) {
        $hash = preg_replace("|/declaration/|", '', $hash);
        $node = $this->getConfiguration()->get('/declaration/'.$hash)->getNodeCahierDesCharges();
        $hashToAdd = preg_replace("|/declaration/|", '', $node->getHash());
        $exist = $this->exist('declaration/'.$hashToAdd);
        $produit = $this->add('declaration')->add($hashToAdd);
        if(!$exist) {
            $produit_libelle = $produit->getLibelle();
            $produit->initActivites();
            if($date == $this->date || !$date) {
                $this->addHistoriqueNewProduit($produit_libelle);
            }
            $this->declaration->reorderByConf();
        }
        return $this->get($produit->getHash());
    }



    public function storeEtape($etape) {
        $etapeOriginal = ($this->exist('etape')) ? $this->etape : null;

        $this->add('etape', $etape);

        return $etapeOriginal != $this->etape;
    }

    public function validate($date = null) {
        if(is_null($date)) {
            $date = date('Y-m-d');
        }

        $this->cleanDoc();
        $this->validation = $date;
        $this->generateMouvements();
    }


    public function getEtablissementObject() {

        return EtablissementClient::getInstance()->findByIdentifiant($this->identifiant);
    }

    public function getSociete() {
        return $this->getEtablissementObject()->getSociete();
    }

  public function isLastOne(){
    $last = HabilitationClient::getInstance()->getLastHabilitation($this->identifiant, acCouchdbClient::HYDRATE_JSON);
    if(!$last) {
        return true;
    }
    return $this->_id == $last->_id;
  }

  public function getPrevious() {
      $date = new DateTime($this->date);
      $date->modify('-1 day');
      $precedente = HabilitationClient::getInstance()->findPreviousByIdentifiantAndDate($this->identifiant, $date->format('Y-m-d'));
      if($precedente && $precedente->_id == $this->_id) {

          return null;
      }

      return $precedente;
  }

  public function getNext() {
      $date = new DateTime($this->date);
      $suivante = HabilitationClient::getInstance()->findNextByIdentifiantAndDate($this->identifiant, $date->format('Y-m-d'));

      return $suivante;
  }

  private function addHistoriqueNewProduit($complement){
      $this->addHistorique("Ajout du produit : ".$complement);
  }

  public function addHistorique($description, $commentaire = '', $auteur = '', $statut = null) {
    $historiqueRow = $this->get('historique')->add(null);
    $historiqueRow->iddoc = $this->_id;
    $historiqueRow->date = $this->getDate();
    $historiqueRow->auteur = $auteur;
    $historiqueRow->statut = $statut;
    try {
      if (!$auteur && sfContext::getInstance() && sfContext::getInstance()->getUser() && sfContext::getInstance()->getUser()->getCompte()) {
        $historiqueRow->auteur = (sfContext::getInstance()->getUser()->isAdmin())? 'Admin' : sfContext::getInstance()->getUser()->getCompte()->identifiant;
      }
    }catch(sfException $e) {}
    $historiqueRow->description = $description;
    $historiqueRow->commentaire = $commentaire;

    return $historiqueRow;
  }

  public function getFullHistoriqueReverse(){
    $historiqueReverse = $this->getFullHistorique();
    $historiqueReverse = array_reverse($historiqueReverse);
    return $historiqueReverse;
  }

  public function getFullHistorique() {
    $historique = array();
    foreach (HabilitationClient::getInstance()->getHistory($this->identifiant, $this->date, acCouchdbClient::HYDRATE_JSON) as $hab) {
      if (isset($hab->historique)) {
        $historique = array_merge($historique, $hab->historique);
      }
    }
      return $historique;
  }

    public function isExcluExportCsv() {
        $etablissement = EtablissementClient::getInstance()->findByIdentifiant($this->identifiant, acCouchdbClient::HYDRATE_JSON);
        if(!$etablissement || $etablissement->statut != EtablissementClient::STATUT_ACTIF) {

            return true;
        }

        $lastHabilitation = HabilitationClient::getInstance()->getLastHabilitation($this->identifiant, acCouchdbClient::HYDRATE_JSON);
        if($lastHabilitation->_id != $this->_id) {

            return true;
        }

        return false;
    }

    public function reorderByConf() {
		$children = array();

		foreach($this as $hash => $child) {
			$children[$hash] = $child->getData();
		}

		foreach($children as $hash => $child) {
			$this->remove($hash);
		}

		foreach($this->getConfig()->getProduits() as $hash => $child) {
			$hashProduit = str_replace("/declaration/", "", $hash);
			if(!array_key_exists($hashProduit, $children)) {
				continue;
			}
			$this->add($hashProduit, $children[$hashProduit]);
		}
	}

  public function isHabiliteFor($hash_produit, $activite) {
    if (!$this->addProduit($hash_produit)->exist('activites')) {
      return false;
    }
    return $this->addproduit($hash_produit)->activites[$activite]->isHabilite();
  }

  public function updateHabilitation($hash_produit, $activites, $statut, $commentaire = "", $date = ''){
        foreach($activites as $activite) {
            $this->addProduit($hash_produit, $date)->updateHabilitation($activite, $statut, $commentaire, $date);
        }
  }

    public function getDemandesSortedOldToRecent() {
        $demandes = array();

        foreach($this->demandes as $key => $demande) {
            $demandes[$demande->date.$demande->getKey()] = $demande;
        }

        ksort($demandes);

        return $demandes;
    }

    public function save() {
        $this->constructId();

        $last = HabilitationClient::getInstance()->getLastHabilitation($this->identifiant, acCouchdbClient::HYDRATE_JSON);

        if($last && $last->_id > $this->_id) {
            $this->add('lecture_seule', true);
        }

        parent::save();
        $precedente = $this->getPrevious();

        if(!$this->isLectureSeule() && $precedente && !$precedente->isLectureSeule()) {
            $precedente->add('lecture_seule', true);
            $precedente->save();
        }
    }

}
