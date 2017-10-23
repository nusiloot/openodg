<?php

class HabilitationClient extends acCouchdbClient {

    const TYPE_MODEL = "Habilitation";
    const TYPE_COUCHDB = "HABILITATION";

    const ACTIVITE_PRODUCTEUR = "PRODUCTEUR";
    const ACTIVITE_VINIFICATEUR = "VINIFICATEUR";
    const ACTIVITE_VRAC = "VRAC";
    const ACTIVITE_ELABORATEUR = "ELABORATEUR";
    const ACTIVITE_CONDITIONNEUR = "CONDITIONNEUR";
    const ACTIVITE_VENTE_A_LA_TIREUSE = "VENTE_A_LA_TIREUSE";


    const STATUT_DEMANDE = "STATUT_DEMANDE";
    const STATUT_HABILITE = "HABILITE";
    const STATUT_SUSPENDU = "SUSPENTU";
    const STATUT_REFUS = "REFUS";
    const STATUT_RETRAIT = "RETRAIT";

    public static $activites_libelles = array( self::ACTIVITE_PRODUCTEUR => "Producteur",
                                                  self::ACTIVITE_VINIFICATEUR => "Vinificateur",
                                                  self::ACTIVITE_VRAC => "Vrac",
                                                  self::ACTIVITE_CONDITIONNEUR => "Conditionneur",
                                                  self::ACTIVITE_ELABORATEUR => "Élaborateur",
                                                  self::ACTIVITE_VENTE_A_LA_TIREUSE => "Vente tireuse"
                                                );
    public static $statuts_libelles = array( self::STATUT_DEMANDE => "Demande",
                                               self::STATUT_HABILITE => "Habilité",
                                               self::STATUT_SUSPENDU => "Suspendu",
                                               self::STATUT_REFUS => "Refus",
                                               self::STATUT_RETRAIT => "Retrait");

    public static function getInstance()
    {
      return acCouchdbManager::getClient("Habilitation");
    }

        public function find($id, $hydrate = self::HYDRATE_DOCUMENT, $force_return_ls = false) {
            $doc = parent::find($id, $hydrate, $force_return_ls);

            if($doc && $doc->type != self::TYPE_MODEL) {

                throw new sfException(sprintf("Document \"%s\" is not type of \"%s\"", $id, self::TYPE_MODEL));
            }

            return $doc;
        }

        public function createDoc($identifiant, $date = '') {
          if (!$date) {
            $date = date('Y-m-d');
          }
          return $this->createOrGetDocFromIdentifiantAndDate($identifiant, $date);
        }

        public function createOrGetDocFromIdentifiantAndDate($identifiant, $date)
        {
            $habilitation_found = $this->findPreviousByIdentifiantAndDate($identifiant, $date);
            if ($habilitation_found && $habilitation_found->date === $date) {
              return $habilitation_found;
            }
            if (!$habilitation_found) {
              $habilitation = new Habilitation();
              $habilitation->initDoc($identifiant,$date);
              $habilitation->storeDeclarant();
              $etablissement = $habilitation->getEtablissementObject();
            }else{
              $habilitation_found->date = $date;
              $habilitation = clone $habilitation_found;
              $habilitation_found = null;
            }

            return $habilitation;
        }

        public function createOrGetDocFromHistory($habilitation_h){
          if(date('Y-m-d') == $habilitation_h->getDate()){
            return $habilitation_h;
          }
          $date = $habilitation_h->date;
          $habilitation_h->date = date('Y-m-d');
          $habilitation = clone $habilitation_h;
          $habilitation_h->date = $date;
          return $habilitation;
        }

        public function findPreviousByIdentifiantAndDate($identifiant, $date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
          $h = $this->getHistory($identifiant, $date, $hydrate);
          if (!count($h)) {
            return NULL;
          }
          $h = $h->getDocs();
          end($h);
          $doc = $h[key($h)];
          return $doc;
        }

        public function getHistory($identifiant, $date = '9999-99-99', $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
          return $this->startkey(sprintf(self::TYPE_COUCHDB."-%s-00000000", $identifiant))
                      ->endkey(sprintf(self::TYPE_COUCHDB."-%s-%s", $identifiant, str_replace('-', '', $date)))->execute($hydrate);//acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
          $history = array();
          foreach($ids as $id) {
                $history[] = HabilitationClient::getInstance()->find($id, $hydrate);
          }
          return $history;
        }

        public function getLastHabilitation($identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT){
          $history = $this->getHistory($identifiant, $hydrate);
          return $this->findPreviousByIdentifiantAndDate($identifiant, '9999-99-99');
        }
    }