<?php

class EtablissementForm extends acCouchdbObjectForm
{
	protected $updatedValues;
	protected $coordonneesEtablissement = null;

    public function __construct(\acCouchdbJson $object, $options = array(), $CSRFSecret = null) {
        parent::__construct($object, $options, $CSRFSecret);
        $this->updatedValues = array();
    }

     public function configure() {
       $this->setWidgets(array(
         // "siret" => new sfWidgetFormInput(array("label" => "N° SIRET")),
		    "ppm" => new sfWidgetFormInput(array("label" => "N° PPM")),
            "adresse" => new sfWidgetFormInput(array("label" => "Adresse")),
            "commune" => new sfWidgetFormInput(array("label" => "Commune")),
            "code_postal" => new sfWidgetFormInput(array("label" => "Code Postal")),
            "telephone_bureau" => new sfWidgetFormInput(array("label" => "Tél. Bureau")),
						"telephone_mobile" => new sfWidgetFormInput(array("label" => "Tél. Mobile")),
            "email" => new sfWidgetFormInput(array("label" => "Email")),
			"chais_adresse" =>  new sfWidgetFormInput(array("label" => "Adresse")),
			"chais_commune" =>  new sfWidgetFormInput(array("label" => "Commune")),
			"chais_code_postal" =>  new sfWidgetFormInput(array("label" => "Code postal")),
        ));

				$ppmMsg = 'Le PPM doit impérativement commencer par une lettre suivie de 8 chiffres';
        $this->setValidators(array(
            //'siret' => new sfValidatorRegex(array("required" => false, "pattern" => "/^[0-9]{14}$/"), array("invalid" => "Le siret doit être un nombre à 14 chiffres")),
			'ppm' =>  new sfValidatorRegex(array('required' => false,
											'pattern' => "/^[A-Z]{1}[0-9]{8}$/",
											'min_length' => 9,
											'max_length' => 9),
											array('invalid' => $ppmMsg,
											'min_length' => $ppmMsg,
											'max_length' => $ppmMsg,
										)),
            'adresse' => new sfValidatorString(array("required" => false)),
            'commune' => new sfValidatorString(array("required" => false)),
            'code_postal' => new sfValidatorString(array("required" => false)),
            'telephone_bureau' => new sfValidatorString(array("required" => false)),
						'telephone_mobile' => new sfValidatorString(array("required" => false)),
       	    'email' => new sfValidatorEmailStrict(array("required" => false)),
			'chais_adresse' => new sfValidatorString(array("required" => false)),
			'chais_commune' => new sfValidatorString(array("required" => false)),
			'chais_code_postal' => new sfValidatorString(array("required" => false)),
        ));

        if(!$this->getOption("use_email")) {
            $this->getValidator('email')->setOption('required', false);
        }

        if($this->getObject()->exist('siren') && $this->getObject()->identifiant == $this->getObject()->siren) {
            unset($this['siret']);
        }

        $this->widgetSchema->setNameFormat('etablissement[%s]');
    }

	private function getCoordonneesEtablissement() {
		if (!$this->coordonneesEtablissement) {
			$this->coordonneesEtablissement = $this->getObject();
		}
		return $this->coordonneesEtablissement;
	}

	public function updateDefaultsFromObject() {
        parent::updateDefaultsFromObject();
        $this->getCoordonneesEtablissement();
		if($this->getObject()->getDocument()->isAdresseLogementDifferente()) {
			$this->setDefault('chais_adresse', $this->getObject()->getDocument()->chais->adresse);
			$this->setDefault('chais_commune', $this->getObject()->getDocument()->chais->commune);
			$this->setDefault('chais_code_postal', $this->getObject()->getDocument()->chais->code_postal);
		}
    }

    public function save($con = null) {

        parent::save($con);
    }

    public function doUpdateObject($values) {
    	foreach ($this as $field => $widget) {
    		if (!$widget->isHidden()) {
    			if ($this->getObject()->exist($field) && $this->getObject()->get($field) != $values[$field]) {
    				$this->updatedValues[$field] = array($this->getObject()->get($field), $values[$field]);
    			}
    		}
    	}
		parent::doUpdateObject($values);
        if ($this->getObject()->getDocument()->exist('chais')) {
		$this->getObject()->getDocument()->chais->adresse = $values['chais_adresse'];
		$this->getObject()->getDocument()->chais->commune = $values['chais_commune'];
		$this->getObject()->getDocument()->chais->code_postal = $values['chais_code_postal'];

		if(!$this->getObject()->getDocument()->isAdresseLogementDifferente()) {
		    $this->getObject()->getDocument()->remove('chais');
		    $this->getObject()->getDocument()->add('chais');
		}
        }

	}

    public function getUpdatedValues()
    {
    	return $this->updatedValues;
    }

    public function hasUpdatedValues()
    {
    	return (count($this->updatedValues) > 0);
    }


}
