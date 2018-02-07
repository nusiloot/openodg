<?php

class parcellaireIrrigableActions extends sfActions {

    public function executeCreate(sfWebRequest $request) {
        $etablissement = $this->getRoute()->getEtablissement();

        $this->secureEtablissement(EtablissementSecurity::DECLARANT_PARCELLAIRE, $etablissement);

        $this->parcellaireIrrigable = ParcellaireIrrigableClient::getInstance()->findOrCreate($etablissement->identifiant, $request->getParameter('campagne', ConfigurationClient::getInstance()->getCampagneManager()->getCurrentNext()));
        $this->parcellaireIrrigable->save();

        return $this->redirect('parcellaireirrigable_edit', $this->parcellaireIrrigable);
    }

    public function executeEdit(sfWebRequest $request) {
        $etablissement = $this->getRoute()->getEtablissement();

        $this->secureEtablissement(EtablissementSecurity::DECLARANT_PARCELLAIRE, $etablissement);

        $this->parcellaireIrrigable = $this->getRoute()->getParcellaireIrrigable();
        $this->parcellesLast = $this->parcellaireIrrigable->getParcellesFromLastParcellaire();
        
    }

    protected function secureEtablissement($droits, $etablissement) {
        if (!EtablissementSecurity::getInstance($this->getUser(), $etablissement)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }


}