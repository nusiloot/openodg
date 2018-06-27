<?php
/**
 * Model for HabilitationHistorique
 *
 */

class HabilitationDemande extends BaseHabilitationDemande {

    public function getConfig()
    {
        return $this->getDocument()->getConfiguration()->get($this->getProduitHash());
    }

    public function setProduitHash($hash) {
        $this->_set('produit_hash', $hash);

        $this->produit_libelle = $this->getConfig()->getLibelleComplet();

        return $this;
    }

    public function getLibelle() {
        if($this->_get('libelle')) {

            return $this->_get('libelle');
        }

        $libelles = array();
        foreach($this->donnees as $key => $value) {
            if(method_exists($this->donnees, "get".ucfirst($key).'Libelle')) {
                $libelles[] = $this->donnees->{$key.'Libelle'};
            } else {
                $libelles[] = ucfirst(str_replace("_", " ", $key)).": ".$value;
            }
        }
        $libelle = implode(", ", $libelles);

        $this->libelle = $libelle;

        return $this->_get('libelle');
    }

    public function getDemandeLibelle() {

        return HabilitationClient::$demande_libelles[$this->demande];
    }

    public function getStatutLibelle() {

        return HabilitationClient::$demande_statut_libelles[$this->statut];
    }

    public function getActivitesLibelle() {
        $activitesLibelle = array();

        foreach($this->activites as $activite) {
            $activitesLibelle[] = HabilitationClient::$activites_libelles[$activite];
        }

        return $activitesLibelle;
    }
}
