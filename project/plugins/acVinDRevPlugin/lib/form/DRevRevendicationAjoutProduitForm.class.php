<?php
class DrevRevendicationAjoutProduitForm extends acCouchdbObjectForm
{
    protected $produits;

    public function __construct(acCouchdbJson $object, $options = array(), $CSRFSecret = null)
    {
        $this->produits = array();
        parent::__construct($object, $options, $CSRFSecret);
    }

    public function configure()
    {
        $produits = $this->getProduits();
        $this->setWidgets(array(
            'hashref' => new sfWidgetFormChoice(array('choices' => $produits))
        ));
        $this->widgetSchema->setLabels(array(
            'hashref' => 'Appellation: '
        ));

        $this->setValidators(array(
            'hashref' => new sfValidatorChoice(array('required' => true, 'choices' => array_keys($produits)),array('required' => "Aucune appellation saisi."))
        ));
        if(DrevConfiguration::getInstance()->hasMentionsCompletaire()) {
            $this->widgetSchema['denomination_complementaire'] = new sfWidgetFormInput();
            $this->widgetSchema['denomination_complementaire']->setLabel("");
            $this->validatorSchema['denomination_complementaire'] = new sfValidatorString(array('required' => false));
        }
        $this->widgetSchema->setNameFormat('drev_revendication_ajout_produit[%s]');
    }

    public function getProduits()
    {
        if (!$this->produits) {
            $produits = $this->getObject()->getConfigProduits();
            foreach ($produits as $produit) {
                if (!$produit->isActif()) {
                	continue;
                }

                $this->produits[$produit->getHash()] = $produit->getLibelleComplet();
            }
        }
        return array_merge(array('' => ''), $this->produits);
    }

    public function hasProduits()
    {
        return (count($this->getProduits()) > 1);
    }

    protected function doUpdateObject($values)
    {
        if (isset($values['hashref']) && !empty($values['hashref'])) {
            $denomination_complementaire = (isset($values['denomination_complementaire']) && !empty($values['denomination_complementaire']))? ($values['denomination_complementaire']) : null;
            $this->getObject()->addProduit($values['hashref'],$denomination_complementaire);
        }
    }

}
