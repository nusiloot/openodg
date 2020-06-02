<?php
class ProduitRendementsForm extends acCouchdbObjectForm {

    public function configure() {
            $this->setWidget('rendement', new bsWidgetFormInput());
            $this->setWidget('rendement_conseille', new bsWidgetFormInput());
            $this->setWidget('rendement_dr', new bsWidgetFormInput());
            $this->setWidget('rendement_vci', new bsWidgetFormInput());
            $this->setWidget('rendement_vci_total', new bsWidgetFormInput());

            $this->getWidget('rendement')->setLabel("Rendemt Maximum :");
            $this->getWidget('rendement_conseille')->setLabel("Rendemt Conseillé :");
            $this->getWidget('rendement_dr')->setLabel("Rendemt DR :");
            $this->getWidget('rendement_vci')->setLabel("Rendemt VCI :");
            $this->getWidget('rendement_vci_total')->setLabel("Rendemt VCI Total :");

            $this->setValidator('rendement', new sfValidatorNumber(array('required' => false)));
            $this->setValidator('rendement_conseille', new sfValidatorNumber(array('required' => false)));
            $this->setValidator('rendement_dr', new sfValidatorNumber(array('required' => false)));
            $this->setValidator('rendement_vci', new sfValidatorNumber(array('required' => false)));
            $this->setValidator('rendement_vci_total', new sfValidatorNumber(array('required' => false)));
        $this->widgetSchema->setNameFormat('%s');
    }


}
