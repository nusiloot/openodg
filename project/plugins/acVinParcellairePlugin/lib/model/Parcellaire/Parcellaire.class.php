<?php

/**
 * Model for Parcellaire
 *
 */
class Parcellaire extends BaseParcellaire {

    protected $declarant_document = null;

    public function __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }

    protected function initDocuments() {
        $this->declarant_document = new DeclarantDocument($this);
    }

    public function storeDeclarant() {
        $this->declarant_document->storeDeclarant();
    }

    public function getEtablissementObject() {

        return EtablissementClient::getInstance()->findByIdentifiant($this->identifiant);
    }

    public function initDoc($identifiant, $campagne) {
        $this->identifiant = $identifiant;
        $this->campagne = $campagne;
        $this->set('_id', ParcellaireClient::getInstance()->buildId($this->identifiant, $this->campagne));
        $this->storeDeclarant();
    }

    public function getConfiguration() {
        return acCouchdbManager::getClient('Configuration')->retrieveConfiguration($this->campagne);
    }

    public function storeEtape($etape) {
        if($etape == $this->etape) {
            
            return false;
        }

        $this->add('etape', $etape);

        return true;
    }

    public function isPapier() {
        return $this->exist('papier') && $this->get('papier');
    }

    public function hasVendeurs() {
        return count($this->vendeurs);
    }

    public function initProduitFromLastParcellaire() {
        if (count($this->declaration) == 0) {
            $this->importProduitsFromLastParcellaire();
        }
    }

    private function getParcellaireLastCampagne() {
        $campagnePrec = $this->campagne - 1;
        $parcellairePrevId = ParcellaireClient::getInstance()->buildId($this->identifiant, $campagnePrec);
        return ParcellaireClient::getInstance()->find($parcellairePrevId);
    }

    private function importProduitsFromLastParcellaire() {
        $parcellairePrev = $this->getParcellaireLastCampagne();
        if (!$parcellairePrev) {
            return;
        }
        $this->declaration = $parcellairePrev->declaration;
    }

    public function getProduits($onlyActive = false) {
        return $this->declaration->getProduits($onlyActive = false);
    }

    public function getAllParcellesByAppellations() {
        $appellations = $this->declaration->getAppellations();
        $parcellesByAppellations = array();
        foreach ($appellations as $appellation) {
            $parcellesByAppellations[$appellation->getHash()] = new stdClass();
            $parcellesByAppellations[$appellation->getHash()]->appellation = $appellation;
            $parcellesByAppellations[$appellation->getHash()]->parcelles = $appellation->getProduitsCepageDetails();
        }
        return $parcellesByAppellations;
    }

    public function getAllParcellesByAppellationSortedByCommunes($appellationHash) {
        $parcelles = $this->getAllParcellesByAppellation($appellationHash);
        usort($parcelles, 'Parcellaire::sortParcellesByCommune');
        return $parcelles;
    }
    
    static function sortParcellesByCommune($parcelle_0, $parcelle_1) {
        if ($parcelle_0->getKey() == $parcelle_1->getKey()) {

            return 0;
        }
        return ($parcelle_0->getKey() > $parcelle_1->getKey()) ? +1 : -1;
    }
    
    public function getAllParcellesByAppellation($appellationHash) {
       $allParcellesByAppellations = $this->getAllParcellesByAppellations();
        $parcelles = array();

        foreach ($allParcellesByAppellations as $appellation) {
            $appellationKey = str_replace('appellation_', '', $appellation->appellation->getKey());
            if ($appellationKey == $appellationHash) {
                $parcelles = $appellation->parcelles;
            }
        }
        return $parcelles;
    }

    public function getAllParcellesByLieux() {
        $lieux = $this->declaration->getLieux();
        $parcellesBylieux = array();
        foreach ($lieux as $lieu) {
            $parcellesBylieux[$lieu->getHash()] = new stdClass();
            $parcellesBylieux[$lieu->getHash()]->lieu = $lieu;
            $parcellesBylieux[$lieu->getHash()]->parcelles = $lieu->getProduitsCepageDetails();
        }
        return $parcellesBylieux;
    }

    public function getAppellationNodeFromAppellationKey($appellationKey, $autoAddAppellation = false) {
        $appellations = $this->declaration->getAppellations();
        $appellationNode = null;
        foreach ($appellations as $key => $appellation) {
            if ('appellation_' . $appellationKey == $key) {
                $appellationNode = $appellation;
                break;
            }
        }
        if (!$appellationNode && $autoAddAppellation) {
            foreach ($this->getConfiguration()->getDeclaration()->getNoeudAppellations() as $key => $appellation) {
                if ('appellation_' . $appellationKey == $key) {
                    $appellationNode = $this->addAppellation($appellation->getHash());
                    break;
                }
            }
        }
        return $appellationNode;
    }

    public function addProduit($hash, $add_appellation = true) {
        $config = $this->getConfiguration()->get($hash);
        if ($add_appellation) {
            $this->addAppellation($config->getAppellation()->getHash());
        }

        $produit = $this->getOrAdd($config->getHash());
        $produit->getLibelle();

        return $produit;
    }

    public function addProduitParcelle($hash, $parcelleKey, $commune, $section, $numero_parcelle, $lieu = null) {
        $produit = $this->getOrAdd($hash);
        $this->addProduit($produit->getHash());

        return $produit->addDetailNode($parcelleKey, $commune, $section, $numero_parcelle, $lieu);
    }

    public function addParcelleForAppellation($appellationKey, $cepage, $commune, $section, $numero_parcelle, $lieu = null) {
        $hash = str_replace('-', '/', $cepage);
        $commune = KeyInflector::slugify($commune);
        $section = KeyInflector::slugify($section);
        $numero_parcelle = KeyInflector::slugify($numero_parcelle);
        $parcelleKey = KeyInflector::slugify($commune . '-' . $section . '-' . $numero_parcelle);
        $this->addProduitParcelle($hash, $parcelleKey, $commune, $section, $numero_parcelle, $lieu);
    }

    public function addAppellation($hash) {
        $config = $this->getConfiguration()->get($hash);
        $appellation = $this->getOrAdd($config->hash);

        return $appellation;
    }

    public function addAcheteur($type, $cvi) {
        if ($this->acheteurs->add($type)->exist($cvi)) {

            return $this->acheteurs->add($type)->get($cvi);
        }

        $acheteur = $this->acheteurs->add($type)->add($cvi);

        if ($cvi == $this->identifiant) {
            $acheteur->nom = "Sur place";
            $acheteur->cvi = $cvi;
            $acheteur->commune = null;

            return $acheteur;
        }

        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-' . $cvi, acCouchdbClient::HYDRATE_JSON);

        if (!$etablissement) {
            throw new sfException(sprintf("L'acheteur %s n'a pas été trouvé", 'ETABLISSEMENT-' . $cvi));
        }

        $acheteur->nom = $etablissement->raison_sociale;
        $acheteur->cvi = $cvi;
        $acheteur->commune = $etablissement->commune;

        return $acheteur;
    }

    public function hasParcelleForAppellationKey($appellationKey) {
        $allParcelles = $this->getAllParcellesByAppellations();
        foreach ($allParcelles as $hash => $appellation) {
            if ($appellation->appellation->getKey() == 'appellation_' . $appellationKey) {
                foreach ($appellation->appellation->getMentions() as $mention) {
                    if (!count($mention->getLieux())) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function getParcellesByCommunes() {
        $parcellesByCommunes = array();
        $allParcellesByAppellations = $this->getAllParcellesByAppellations();
        $config = $this->getConfiguration();
        foreach ($allParcellesByAppellations as $appellation_key => $parcellesNodes) {
            $configAppellationLibelle = $config->get($appellation_key)->getLibelle();
            foreach ($parcellesNodes->parcelles as $key => $parcelle) {
                if (!array_key_exists($parcelle->commune, $parcellesByCommunes)) {
                    $parcellesByCommunes[$parcelle->commune] = new stdClass();
                    $parcellesByCommunes[$parcelle->commune]->commune = $parcelle->commune;
                    $parcellesByCommunes[$parcelle->commune]->total_superficie = 0;
                    $parcellesByCommunes[$parcelle->commune]->produits = array();
                }
                $key_produit = $key;
                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit] = new stdClass();

                $configLieuLibelle = $config->get($parcelle->getCepage()->getCouleur()->getLieu()->getHash())->getLibelle();
                $configCepageLibelle = $config->get($parcelle->getCepage()->getHash())->getLibelle();

                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit]->appellation_libelle = $configAppellationLibelle;
                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit]->lieu_libelle = $configLieuLibelle;
                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit]->cepage_libelle = $configCepageLibelle;
                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit]->num_parcelle = $parcelle->section . ' ' . $parcelle->numero_parcelle;
                $parcellesByCommunes[$parcelle->commune]->produits[$key_produit]->superficie = $parcelle->superficie;
                $parcellesByCommunes[$parcelle->commune]->total_superficie += $parcelle->superficie;
            }
        }
        return $parcellesByCommunes;
    }

    public function getParcellesByCommunesLastCampagne() {
        $parcellairePrev = $this->getParcellaireLastCampagne();
        if (!$parcellairePrev) {
            return array();
        }
        return $parcellairePrev->getParcellesByCommunes();
    }

    public function getParcellesByLieux() {
        $parcellesByLieux = array();
        $allParcellesByLieux = $this->getAllParcellesByLieux();
        $config = $this->getConfiguration();
        foreach ($allParcellesByLieux as $lieu_hash => $lieuNode) {
            $configAppellationLibelle = $config->get($lieu_hash)->getAppellation()->getLibelle();
            $configLieuLibelle = $config->get($lieu_hash)->getLibelle();

            if (!array_key_exists($lieu_hash, $parcellesByLieux)) {
                $parcellesByLieux[$lieu_hash] = new stdClass();
                $parcellesByLieux[$lieu_hash]->total_superficie = 0;
                $parcellesByLieux[$lieu_hash]->appellation_libelle = $configAppellationLibelle;
                $parcellesByLieux[$lieu_hash]->lieu_libelle = $configLieuLibelle;
                $parcellesByLieux[$lieu_hash]->parcelles = array();
                $parcellesByLieux[$lieu_hash]->acheteurs = $this->get($lieu_hash)->getAcheteursNode();
            }

            $parcelaireCouleurs = $this->get($lieu_hash)->getCouleurs();
            foreach ($parcelaireCouleurs as $parcelaireCouleur) {
                foreach ($parcelaireCouleur->getCepages() as $parcelaireCepage) {
                    foreach ($parcelaireCepage->detail as $parcelle) {
                        $configCepageLibelle = $config->get($parcelle->getCepage()->getHash())->getLibelleLong();
                        $parcellesByLieux[$lieu_hash]->parcelles[$parcelle->gethash()] = new stdClass();
                        $parcellesByLieux[$lieu_hash]->parcelles[$parcelle->gethash()]->cepage_libelle = $configCepageLibelle;
                        $parcellesByLieux[$lieu_hash]->parcelles[$parcelle->gethash()]->parcelle = $parcelle;
                        $parcellesByLieux[$lieu_hash]->total_superficie += $parcelle->superficie;
                    }
                }
            }
        }
        return $parcellesByLieux;
    }

    public function validate($date = null) {
        if (is_null($date)) {
            $date = date('Y-m-d');
        }

        $this->declaration->cleanNode();
        $this->validation = $date;
        $this->validateOdg();
    }

    public function validateOdg() {
        $this->validation_odg = date('Y-m-d');
    }

}
