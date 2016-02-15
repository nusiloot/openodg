<?php
class avaComponents extends sfComponents 
{
    
    public function executeHistory(sfWebRequest $request) 
    {
        $this->etablissement = $this->getUser()->getEtablissement();
        $this->history = array();
        $drevsHistory = DRevClient::getInstance()->getHistory($this->etablissement->identifiant);
        $drevmarcsHistory = DRevMarcClient::getInstance()->getHistory($this->etablissement->identifiant);
        $parcellairesHistory = ParcellaireClient::getInstance()->getHistory($this->etablissement->identifiant);
        $parcellairesCremantHistory = ParcellaireClient::getInstance()->getHistory($this->etablissement->identifiant, true);

        foreach ($drevsHistory as $drevHistory) {
        	$this->history[$drevHistory->validation.$drevHistory->_id] = $drevHistory;
        }
    	foreach ($drevmarcsHistory as $drevmarcHistory) {
        	$this->history[$drevmarcHistory->validation.$drevmarcHistory->_id] = $drevmarcHistory;
        }
        foreach ($parcellairesHistory as $parcellaireHistory) {
            $this->history[$parcellaireHistory->validation.$parcellaireHistory->_id] = $parcellaireHistory;
        }
        foreach ($parcellairesCremantHistory as $parcellaireCremantHistory) {
            $this->history[$parcellaireCremantHistory->validation.$parcellaireCremantHistory->_id] = $parcellaireCremantHistory;
        }

        krsort($this->history);
    }
    
}
