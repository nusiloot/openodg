<?php
class FactureClient extends acCouchdbClient {

    const TYPE_MODEL = "Facture";
    const TYPE_COUCHDB = "FACTURE";
    const FACTURE_LIGNE_ORIGINE_TYPE_DRM = "DRM";
    const FACTURE_LIGNE_ORIGINE_TYPE_SV12 = "SV12";
    const FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE = "propriete";
    const FACTURE_LIGNE_MOUVEMENT_TYPE_CONTRAT = "contrat";
    const FACTURE_LIGNE_PRODUIT_TYPE_VINS = "contrat_vins";
    const FACTURE_LIGNE_PRODUIT_TYPE_MOUTS = "contrat_mouts";
    const FACTURE_LIGNE_PRODUIT_TYPE_RAISINS = "contrat_raisins";
    const FACTURE_LIGNE_PRODUIT_TYPE_ECART = "ecart";

    const STATUT_REDRESSEE = 'REDRESSE';
    const STATUT_NONREDRESSABLE = 'NON_REDRESSABLE';

    const FACTURE_PAIEMENT_CHEQUE = "CHEQUE";
    const FACTURE_PAIEMENT_VIREMENT = "VIREMENT";
    const FACTURE_PAIEMENT_PRELEVEMENT_AUTO = "PRELEVEMENT_AUTO";


    public static $origines = array(self::FACTURE_LIGNE_ORIGINE_TYPE_DRM, self::FACTURE_LIGNE_ORIGINE_TYPE_SV12);

    public static $types_paiements = array(self::FACTURE_PAIEMENT_CHEQUE => "Chèque", self::FACTURE_PAIEMENT_VIREMENT => "Virement", self::FACTURE_PAIEMENT_PRELEVEMENT_AUTO => "Prélèvement automatique");

    private $documents_origine = array();

    public static function getInstance() {
        return acCouchdbManager::getClient("Facture");
    }

    public function getId($identifiant, $numeroFacture) {
        return 'FACTURE-'.$identifiant.'-'.$numeroFacture;
    }

    public function getNextNoFacture($idClient,$date)
    {
      $id = '';
    	$facture = self::getAtDate($idClient,$date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        if (count($facture) > 0) {
            $id .= ((double)str_replace('FACTURE-'.$idClient.'-', '', max($facture)) + 1);
        } else {
            $id.= $date.'01';
        }
      return $id;
    }

    public function getNextNoFactureCampagneFormatted($idClient, $campagne, $format){
        $annee = DateTime::createFromFormat("Y",$campagne)->format("y");
        $archiveNumero = ArchivageAllView::getInstance()->getLastNumeroArchiveByTypeAndCampagne("Facture", $campagne);
        return sprintf($format, $annee, intval($archiveNumero) + 1);
    }

    public function getAtDate($idClient,$date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('FACTURE-'.$idClient.'-'.$date.'00')->endkey('FACTURE-'.$idClient.'-'.$date.'99')->execute($hydrate);
    }

    public function createFactureByTemplate($template, $compte, $date_facturation = null, $message_communication = null) {
        $mouvements = $template->getMouvements($compte->identifiant);
        $mouvements = $this->aggregateMouvements($mouvements);

        if(!count($mouvements)) {

            return false;
        }

        return FactureClient::getInstance()->createDoc($mouvements, $compte, $date_facturation, $message_communication, $template->arguments->toArray(true, false), $template);
    }

    public function getMouvementsByDocs($compteIdentifiant, $docs, $regenerate = false) {
        if (!$docs) {

            return array();
        }
        $mouvements = array();
        if($docs && !is_array($docs)){
          $docs = array($docs);
        }
        foreach($docs as $doc) {
            if($regenerate) {
                $doc->remove('mouvements');
                $doc->add('mouvements');
            }

            $generated = false;
            if(!count($doc->mouvements)) {
                $doc->generateMouvements();
                $doc->save();
                $generated = true;
            }

            if($generated && count($doc->mouvements) && !$doc->exist('mouvements/'.$compteIdentifiant)) {
                $mouvs = $doc->mouvements->getFirst();
                $doc->mouvements->add($compteIdentifiant, $mouvs->toArray(true, false));
                $doc->mouvements->remove($mouvs->getKey());
            }

            if($generated) {
                $doc->save();
            }

            if(!$doc->exist('mouvements/'.$compteIdentifiant)) {

                continue;
            }

            $mouvs = $doc->mouvements->get($compteIdentifiant);

            foreach($mouvs as $m) {
                if((!$m->isFacturable() || $m->facture)) {
                    continue;
                }
                $mouvements[] = $m;
            }
        }

        return $mouvements;
    }

    public function aggregateMouvements($mouvements) {
        $mouvementsAggreges = array();

        foreach($mouvements as $mouv) {
          $key = $mouv->categorie.$mouv->type_hash.$mouv->taux;
            if($mouv->exist("template")){
              $key = $mouv->template.$key;
            }

            if(!isset($mouvementsAggreges[$key])) {
                $mouvementsAggreges[$key] = array(
                    "categorie" => $mouv->categorie,
                    "type_hash" => $mouv->type_hash,
                    "type_libelle" => $mouv->type_libelle,
                    "quantite" => 0,
                    "taux" => $mouv->taux,
                    "origines" => array(),
                );
                if($mouv->exist("tva")){
                    $mouvementsAggreges[$key]["tva"] = $mouv->tva;
                }
                if($mouv->exist("unite")){
                  $mouvementsAggreges[$key]["unite"] = $mouv->unite;
                }
            }

            $mouvementsAggreges[$key]["quantite"] += $mouv->quantite;
            if(!isset($mouvementsAggreges[$key]["origines"][$mouv->getDocument()->_id])) {
                $mouvementsAggreges[$key]["origines"][$mouv->getDocument()->_id] = array();
            }
            $mouvementsAggreges[$key]["origines"][$mouv->getDocument()->_id][] = $mouv->getKey();
        }

        return array_values($mouvementsAggreges);
    }

    public function createDoc($mouvements, $compte, $date_facturation = null, $message_communication = null, $arguments = array(), $template = null) {
        $facture = new Facture();
        $facture->storeDatesCampagne($date_facturation);
        // Attention le compte utilisé pour OpenOdg est celui de la société
        if($compte->exist('id_societe')){
          $compte = $compte->getSociete()->getMasterCompte();
        }
        $facture->constructIds($compte);
        $facture->storeEmetteur();
        $facture->storeDeclarant($compte);
        $facture->storeLignesByMouvements($mouvements, $template);
        $facture->updateTotaux();
        $facture->storeOrigines();
        $facture->storeTemplates($template);
        if(FactureConfiguration::getInstance()->getModaliteDePaiement()) {
            $facture->add('modalite_paiement',FactureConfiguration::getInstance()->getModaliteDePaiement());
        }
        $facture->arguments = $arguments;
        if(trim($message_communication)) {
          $facture->addOneMessageCommunication($message_communication);
        }
        if(FactureConfiguration::getInstance()->hasPaiements()){
          $facture->add("paiements",array());
        }

        if(!$facture->total_ttc && FactureConfiguration::getInstance()->isFacturationAllEtablissements()){
          return null;
        }

        return $facture;
    }

    public function regenerate($facture_or_id) {
        $facture = $facture_or_id;

        if(is_string($facture)) {
            $facture = $this->find($facture_or_id);
        }

        if($facture->isPayee()) {

            throw new sfException(sprintf("La factures %s a déjà été payée", $facture->_id));
        }

        $docs = array();

        foreach($facture->origines as $id) {
            $docs[$id] = $this->getDocumentOrigine($id);
        }

        $mouvements = $this->getMouvementsByDocs($facture->identifiant, $docs, true);
        $mouvements = $this->aggregateMouvements($mouvements);

        $template = $facture->getTemplate();
        $message_communication = null;
        if($facture->exist('message_communication')) {
            $message_communication = $facture->message_communication;
        }

        $f = FactureClient::getInstance()->createDoc($mouvements, $facture->getCompte(), date('Y-m-d'), $message_communication, $template->arguments->toArray(true, false), $template);

        $f->_id = $facture->_id;
        $f->_rev = $facture->_rev;
        $f->numero_facture = $facture->numero_facture;
        $f->numero_ava = $facture->numero_ava;
        $f->numero_archive = $facture->numero_archive;

        $f->forceFactureMouvements();

        return $f;
    }

    public function getDocumentOrigine($id) {
        if (!array_key_exists($id, $this->documents_origine)) {
            $this->documents_origine[$id] = acCouchdbManager::getClient()->find($id);
        }
        return $this->documents_origine[$id];
    }

    public function findByIdentifiant($identifiant) {
        return $this->find('FACTURE-' . $identifiant);
    }

    public function findBySocieteAndId($idSociete, $idFacture) {
        return $this->find('FACTURE-'.$idSociete . '-' . $idFacture);
    }

    private function getReduceLevelForFacturation() {
      return MouvementfactureFacturationView::KEYS_VRAC_DEST + 1;
    }

    public function getFacturationForSociete($societe) {
      return MouvementfactureFacturationView::getInstance()->getMouvementsBySocieteWithReduce($societe, 0, 1, $this->getReduceLevelForFacturation());
    }

    public function getMouvementsForMasse($regions) {
        if(!$regions){
            return MouvementfactureFacturationView::getInstance()->getMouvements(0, 1, $this->getReduceLevelForFacturation());
        }
        $mouvementsByRegions = array();
        foreach ($regions as $region) {
            $mouvementsByRegions = array_merge(MouvementfactureFacturationView::getInstance()->getMouvementsFacturablesByRegions(0, 1,$region,$this->getReduceLevelForFacturation()),$mouvementsByRegions);
        }
       return $mouvementsByRegions;
    }

    public function getMouvementsNonFacturesBySoc($mouvements) {
        $generationFactures = array();
        foreach ($mouvements as $mouvement) {
	  $societe_id = substr($mouvement->key[MouvementfactureFacturationView::KEYS_ETB_ID], 0, -2);
	  if (isset($generationFactures[$societe_id])) {
	    $generationFactures[$societe_id][] = $mouvement;
	  } else {
	    $generationFactures[$societe_id] = array();
	    $generationFactures[$societe_id][] = $mouvement;
	  }
        }
        return $generationFactures;
    }

    public function filterWithParameters($mouvementsBySoc, $parameters) {
        if (isset($parameters['date_mouvement']) && ($parameters['date_mouvement'])){
          $date_mouvement = Date::getIsoDateFromFrenchDate($parameters['date_mouvement']);
          foreach ($mouvementsBySoc as $identifiant => $mouvements) {
              foreach ($mouvements as $key => $mouvement) {
                      $farDateMvt = $this->getGreatestDate($mouvement->value[MouvementfactureFacturationView::VALUE_DATE]);
                      if(Date::sup($farDateMvt,$date_mouvement)) {
  		                    unset($mouvements[$key]);
                          $mouvementsBySoc[$identifiant] = $mouvements;
                          continue;
                      }

                      if(isset($parameters['type_document']) && !in_array($parameters['type_document'], self::$origines)) {
                          unset($mouvements[$key]);
                          $mouvementsBySoc[$identifiant] = $mouvements;
                          continue;
                      }

                      if(isset($parameters['type_document']) && $parameters['type_document'] != $mouvement->key[MouvementfactureFacturationView::KEYS_ORIGIN]) {
                        unset($mouvements[$key]);
                        $mouvementsBySoc[$identifiant] = $mouvements;
                        continue;
                      }
              }
          }
        }
        foreach ($mouvementsBySoc as $identifiant => $mouvements) {
            $somme = 0;
            foreach ($mouvements as $key => $mouvement) {
                $prix = $mouvement->value[MouvementfactureFacturationView::VALUE_VOLUME] * $mouvement->value[MouvementfactureFacturationView::VALUE_CVO];
                if(!$prix) {
                  unset($mouvementsBySoc[$identifiant][$key]);
                  continue;
                }
                $somme += $prix;
            }
	          $somme = $somme * -1;
            $somme = $this->ttc($somme);

            if(count($mouvementsBySoc[$identifiant]) == 0) {
              $mouvementsBySoc[$identifiant] = null;
            }

            if (isset($parameters['seuil']) && $parameters['seuil']) {
                if (($somme < $parameters['seuil']) && ($somme >= 0)) {
                    $mouvementsBySoc[$identifiant] = null;
                }
          }

      }
      $mouvementsBySoc = $this->cleanMouvementsBySoc($mouvementsBySoc);
      return $mouvementsBySoc;
    }

    public function getComptesIdFilterWithParameters($arguments) {
        $ids = array();
        if(!$arguments['requete'] && FactureConfiguration::getInstance()->isFacturationAllEtablissements()){
          $comptes = CompteAllView::getInstance()->findByInterproVIEW('INTERPRO-declaration');
          foreach($comptes as $compte) {
             $ids[] = $compte->id;
          }
        }else{
          $comptes = CompteClient::getInstance()->getComptes($arguments['requete']);
          foreach($comptes as $compte) {
            $ids[] = $compte->_id;
          }
        }

        return $ids;
    }

    private function getGreatestDate($dates){
        if(is_string($dates)) return $dates;
        if(is_array($dates)){
            $dateres = $dates[0];
            foreach ($dates as $date) {
                if(Date::sup($date, $dateres)) $dateres=$date;
            }
            return $dateres;
        }
         throw new sfException("La date du mouvement ou le tableau de date est mal formé ".print_r($dates, true));
    }

    private function cleanMouvementsBySoc($mouvementsBySoc){
      if (count($mouvementsBySoc) == 0)
	return null;
      foreach ($mouvementsBySoc as $identifiant => $mouvement) {
	if (!count($mouvement))
	  unset($mouvementsBySoc[$identifiant]);
      }
      return $mouvementsBySoc;
    }

    public function createFactureByTemplateWithGeneration($template, $compte_or_id, $date_facturation = null) {
        $generation = new Generation();
        $generation->date_emission = date('Y-m-d-H:i');
        $generation->type_document = GenerationClient::TYPE_DOCUMENT_FACTURES;
        $generation->documents = array();
        $generation->somme = 0;
        $cpt = 0;

        $compte = $compte_or_id;

        if(is_string($compte)) {
            $compte = CompteClient::getInstance()->find($compte_or_id);
        }

        $f = $this->createFactureByTemplate($template, $compte, $date_facturation);

        if(!$f) {

            return false;
        }

        $f->save();

        $generation->somme += $f->total_ttc;
        $generation->add('documents')->add($cpt, $f->_id);
        $generation->libelle = $compte->nom_a_afficher;
        $cpt++;

        return $generation;
    }

    private function ttc($p) {
        return $p + $p * 0.196;
    }

    public function getTypes() {
        return array(FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_VINS,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_RAISINS,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_ECART,
            FactureClient::FACTURE_LIGNE_PRODUIT_TYPE_MOUTS);
    }

    public function getProduitsFromTypeLignes($lignes) {
        $produits = array();
        foreach ($lignes as $ligne) {
            if (array_key_exists($ligne->produit_hash, $produits)) {
                $produits[$ligne->produit_hash][] = $ligne;
            } else {
                $produits[$ligne->produit_hash] = array();
                $produits[$ligne->produit_hash][] = $ligne;
            }
        }
        return $produits;
    }

    public function isRedressee($factureview){
      return ($factureview->value[FactureSocieteView::VALUE_STATUT] == self::STATUT_REDRESSEE);
    }

    public function isRedressable($factureview){
      return !$this->isRedressee($factureview) && $factureview->value[FactureSocieteView::VALUE_STATUT] != self::STATUT_NONREDRESSABLE;
    }

    public function getTypeLignePdfLibelle($typeLibelle) {
      if ($typeLibelle == self::FACTURE_LIGNE_MOUVEMENT_TYPE_PROPRIETE)
	return 'Sorties de propriété';
      switch ($typeLibelle) {
      case self::FACTURE_LIGNE_PRODUIT_TYPE_MOUTS:
	return 'Sorties de contrats moûts';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_RAISINS:
	return 'Sorties de contrats raisins';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_VINS:
	return 'Sorties de contrats vins';

      case self::FACTURE_LIGNE_PRODUIT_TYPE_ECART:
	return 'Sorties raisins et moûts';
      }
      return '';
    }

    public function createAvoir(Facture $f) {
      if (!$f->isRedressable()) {
  return ;
      }
      $avoir = clone $f;

      $avoir->constructIds($f->getCompte(), $f->region);

      foreach($avoir->lignes as $ligne) {
          foreach($ligne->details as $detail) {
              $detail->quantite *= -1;
              $detail->montant_ht *= -1;
              $detail->montant_tva *= -1;
          }

          $ligne->montant_ht *= -1;
          $ligne->montant_tva *= -1;

          $ligne->remove('origine_mouvements');
          $ligne->add('origine_mouvements');
      }

      $avoir->total_ht *= -1;
      $avoir->total_taxe *= -1;
      $avoir->total_ttc *= -1;

      $avoir->remove('origines');
      $avoir->add('origines');

      $avoir->remove('templates');
      $avoir->add('templates');

      $avoir->numero_archive = null;
      $avoir->numero_ava = null;
      $avoir->versement_comptable = 0;
      $avoir->versement_comptable_paiement = 1;
      $avoir->storeDatesCampagne(date('Y-m-d'));
      $avoir->date_paiement = null;
      $avoir->reglement_paiement = null;
      $avoir->remove('arguments');
      $avoir->add('arguments');

      return $avoir;
    }

    public function defactureCreateAvoirAndSaveThem(Facture $f) {
      if (!$f->isRedressable()) {
	       return ;
      }
      $avoir = clone $f;
      $compte = CompteClient::getInstance()->find("COMPTE-".$avoir->identifiant);
      $avoir->constructIds($compte, $f->region);
      $f->add('avoir',$avoir->_id);
      $f->save();
      foreach($avoir->lignes as $type => $ligne) {
        $ligne->montant_ht *= -1;
        $ligne->montant_tva *= -1;
      	foreach($ligne->details as $id => $detail) {
          $detail->quantite *= -1;
      	  $detail->montant_ht *= -1;
          $detail->montant_tva *= -1;
      	}
      }
      $avoir->total_ttc *= -1;
      $avoir->total_ht *= -1;
      $avoir->total_taxe *= -1;
      $avoir->remove('echeances');
      $avoir->add('echeances');
      $avoir->statut = self::STATUT_NONREDRESSABLE;
      $avoir->storeDatesCampagne(date('Y-m-d'));
      $avoir->numero_archive = null;
      $avoir->versement_comptable = 0;
      $avoir->save();
      $f->defacturer();
      $f->save();
      return $avoir;
    }

    public function getFacturesByCompte($identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        $ids = $this->startkey(sprintf("FACTURE-%s-%s", $identifiant, "0000000000"))
                    ->endkey(sprintf("FACTURE-%s-%s", $identifiant, "9999999999"))
                    ->execute(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        $factures = array();

        foreach($ids as $id) {
            $factures[$id] = FactureClient::getInstance()->find($id, $hydrate);
        }

        krsort($factures);

        return $factures;
    }

    public function getDateCreation($id) {
        $d = substr($id, -10,8);
        $matches = array();
        if(preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', $d, $matches)){
        return $matches[3].'/'.$matches[2].'/'.$matches[1];
        }
        return '';
    }

}
