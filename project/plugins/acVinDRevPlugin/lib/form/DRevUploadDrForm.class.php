<?php
class DRevUploadDrForm extends FichierForm
{
	public function configure() {
		parent::configure();
		$this->setWidget('libelle', new sfWidgetFormInputHidden());
		$this->setWidget('date_depot', new sfWidgetFormInputHidden());
		$this->setWidget('visibilite', new sfWidgetFormInputHidden());
		$this->widgetSchema->setLabel('file', 'Fichier');
		$required = ($this->getObject()->getDocumentDefinitionModel() != DRCsvFile::CSV_TYPE_DR)? false : true;
		$this->setValidator('file', new sfValidatorFile(array('required' => $required, 'mime_types' => array('application/vnd.ms-office'), 'path' => sfConfig::get('sf_cache_dir')), array('mime_types' => 'Fichier de type xls attendu')));
	}
}
