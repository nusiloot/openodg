<?php
/**
 * Model for Rendezvous
 *
 */

class Rendezvous extends BaseRendezvous {

    
    public function constructId() {
        $this->set('_id', sprintf("%s-%s-%s", RendezvousClient::TYPE_COUCHDB, $this->cvi, str_replace("-", "", $this->date).str_replace(":", "", $this->heure)));
    }
    
    public function getDateHeure(){
        return str_replace('-','',$this->getDate()).str_replace(':','',$this->getHeure());
    }

    public function getAgentNom() {
        $tournee = TourneeClient::getInstance()->findTourneeByIdRendezvous($this->_id);

        if(!$tournee) {

            return $this->nom_agent_origine;
        }

        return $tournee->getFirstAgent()->nom;
    }

    public function getNomAgentOrigine() {

        return Anonymization::hideIfNeeded($this->_get('nom_agent_origine'));
    }

    public function getCompte() {
        return CompteClient::getInstance()->findByIdentifiant($this->identifiant);
    }
    
    public function getChai() {
        return $this->getCompte()->getChais()->get($this->idchai);
    }
    
    public function isRendezvousRaisin() {
        return RendezvousClient::RENDEZVOUS_TYPE_RAISIN == $this->type_rendezvous;
    }
    
    public function isRendezvousVolume() {
        return RendezvousClient::RENDEZVOUS_TYPE_VOLUME == $this->type_rendezvous;
    }
    
    public function isRealise() {
        return RendezvousClient::RENDEZVOUS_STATUT_REALISE == $this->statut;
    }
    
    public function getDateHeureFr(){
        return ucfirst(format_date($this->getDate(), "P", "fr_FR"))." à ".str_replace(':','h',$this->getHeure());
    }

    public function incrementId() {
        $this->_id = RendezvousClient::getInstance()->incrementId($this->_id);
    }

    public function getTournee() {

        return TourneeClient::getInstance()->findTourneeByIdRendezvous($this->_id);
    }

    public function getAgent() {

        $tournee = $this->getTournee();

        if(!$tournee) {

            return null;
        }

        return $tournee->getAgentUniqueObj();
    }

    public function getTelephoneBureau(){
        return Anonymization::hideIfNeeded($this->_get('telephone_bureau'));
    }
    public function getTelephoneMobile(){
        return Anonymization::hideIfNeeded($this->_get('telephone_mobile'));
    }
	public function getTelephonePrive(){
		return Anonymization::hideIfNeeded($this->_get('telephone_prive'));
	}
    public function getEmail(){
        return Anonymization::hideIfNeeded($this->_get('email'));
    }
    public function getAdresse() {
        return Anonymization::hideIfNeeded($this->_get('adresse'));
    }
    public function getRaisonSociale() {
        return Anonymization::hideIfNeeded($this->_get('raison_sociale'));
    }

    protected function preSave() {
        if(!$this->isNew()) {
            return;
        }
        for($i = 0; $i <= 10; $i++) {
            if(!RendezvousClient::getInstance()->find($this->_id, acCouchdbClient::HYDRATE_JSON)) {
                return;
            }
            $this->incrementId();
        }
    }
}