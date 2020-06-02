<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompteDegustateurModificationForm
 *
 * @author mathurin
 */
class CompteDegustateurModificationForm extends CompteModificationForm {

    protected $produits;
        private $syndicats;

    public function __construct(\acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        parent::__construct($object, $options, $CSRFSecret);
        $this->initDefaultProduits();
        $this->initDefaultSyndicats();
    }

    public function configure() {
        parent::configure();
        $this->setWidget("civilite", new sfWidgetFormChoice(array('choices' => $this->getCivilites())));
        $this->setWidget("prenom", new sfWidgetFormInput(array("label" => "Prénom")));
        $this->setWidget("nom", new sfWidgetFormInput(array("label" => "Nom")));

        $this->setWidget("raison_sociale", new sfWidgetFormInput(array("label" => "Société")));

        $this->setValidator('civilite', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCivilites())), array('required' => "Aucune civilité choisie.")));
        $this->setValidator('prenom', new sfValidatorString(array("required" => false)));
        $this->setValidator('nom', new sfValidatorString(array("required" => false)));

        $this->setValidator('raison_sociale', new sfValidatorString(array("required" => false)));
        
        $this->setWidget("produits", new sfWidgetFormChoice(array('multiple' => true, 'choices' => $this->getAllProduits())));
        $this->setValidator('produits', new sfValidatorChoice(array('required' => false, 'multiple' => true, 'choices' => array_keys($this->getAllProduits()))));
    
        $this->setWidget("syndicats", new sfWidgetFormChoice(array('multiple' => true, 'choices' => $this->getSyndicats())));
        $this->setValidator('syndicats', new sfValidatorChoice(array("required" => false, 'multiple' => true, 'choices' => array_keys($this->getSyndicats()))));

        $this->embedForm('formations', new CompteFormationsForm($this->getObject()->formations));
    }

    private function getSyndicats() {
        $compteClient = CompteClient::getInstance();
        if (!$this->syndicats) {
            foreach ($compteClient->getAllSyndicats() as $syndicatId) {      
                $syndicat = CompteClient::getInstance()->find($syndicatId);
            $this->syndicats[$syndicatId] = $syndicat->nom_a_afficher;
            }
        }
        return $this->syndicats;
    }

    protected function getAllProduits() {
        $produits = array("" => "");

        foreach (ConfigurationClient::getConfiguration()->declaration->getProduitsFilter(_ConfigurationDeclaration::TYPE_DECLARATION_DEGUSTATION, 'ConfigurationAppellation') as $appellation) {
            
            if(!$appellation->exist('mention') || !$appellation->hasManyLieu()) {
                $produits[str_replace('/', '-', $appellation->getHash())] = $appellation->getLibelleComplet();
                continue;
            }

            foreach($appellation->getLieux() as $lieu) {
                $produits[str_replace('/', '-', $lieu->getHash())] = $lieu->getLibelleComplet();
            }
        }

        return $produits;
    }


    public function save($con = null) {
        if (array_key_exists('produits', $this->values)) {
            $produits = ($this->values['produits']) ? $this->values['produits'] : array();
            $this->getObject()->updateLocalTagsProduits($produits);
        }

        if (array_key_exists('syndicats', $this->values)) {
            $syndicats = ($this->values['syndicats']) ? $this->values['syndicats'] : array();
            $this->getObject()->updateLocalSyndicats($syndicats);
        }

        parent::save($con);
    }

    public function initDefaultProduits() {
        $default_produits = array();
        foreach ($this->getObject()->getInfosProduits() as $produit_hash => $produit_libelle) {
            $default_produits[] = $produit_hash;
        }
        $this->widgetSchema['produits']->setDefault($default_produits);
    }
    
    public function initDefaultSyndicats() {
        $default_syndicats = array();
        foreach ($this->getObject()->getInfosSyndicats() as $syndicats_key => $syndicats_libelle) {
            $default_syndicats[] = $syndicats_key;
        }
        $this->widgetSchema['syndicats']->setDefault($default_syndicats);
    }

}
