<?php

class EtablissementForm extends acCouchdbObjectForm
{
     public function configure() {
       $this->setWidgets(array(
            "siret" => new sfWidgetFormInput(),
            "raison_sociale" => new sfWidgetFormInput(),
            "adresse" => new sfWidgetFormInput(),
            "commune" => new sfWidgetFormInput(),
            "code_postal" => new sfWidgetFormInput(),
            "telephone_bureau" => new sfWidgetFormInput(),
            "telephone_mobile" => new sfWidgetFormInput(),
            "telephone_prive" => new sfWidgetFormInput(),
            "fax" => new sfWidgetFormInput(),
        ));

        $this->setValidators(array(
            'siret' => new sfValidatorNumber(array("required" => false, "min" => 14, "max" => 14), array("min" => "Le siret doit être un nombre à 14 chiffres", "max" => "Le siret doit être un nombre à 14 chiffres")),
            'raison_sociale' => new sfValidatorString(),
            'adresse' => new sfValidatorString(),
            'commune' => new sfValidatorString(),
            'code_postal' => new sfValidatorString(),
            'telephone_bureau' => new sfValidatorString(array("required" => false)),
            'telephone_mobile' => new sfValidatorString(array("required" => false)),
            'telephone_prive' => new sfValidatorString(array("required" => false)),
            'fax' => new sfValidatorString(array("required" => false)),
        ));

        $this->widgetSchema->setNameFormat('etablissement[%s]');
    }
}