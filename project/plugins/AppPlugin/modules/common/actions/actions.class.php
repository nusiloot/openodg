<?php

class commonActions extends sfActions {

    public function executeAccueil(sfWebRequest $request) {
        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {

            return $this->redirect('declaration');
        }
	
	if ($this->getUser()->hasCredential(myUser::CREDENTIAL_DREV_ADMIN)) {

            return $this->redirect('declaration');
        }

        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_TOURNEE)) {

            return $this->redirect('tournee_agent_accueil');
        }

        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_CONTACT)) {

            return $this->redirect('compte_recherche');
        }

        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_HABILITATION) && HabilitationConfiguration::getInstance()->isSuiviParDemande()) {

            return $this->redirect('habilitation_demande');
        }

        if ($this->getUser()->hasCredential(myUser::CREDENTIAL_HABILITATION)) {

            return $this->redirect('habilitation');
        }

        if(!$this->getUser()->getCompte()->getSociete()->getEtablissementPrincipal()) {

            return $this->forwardSecure();
        }

        if($request->getParameter('redirect', null) == 'documents') {

            return $this->redirect('pieces_historique', $this->getUser()->getCompte()->getSociete()->getEtablissementPrincipal());
        }

        return $this->redirect('declaration_etablissement', $this->getUser()->getCompte()->getSociete()->getEtablissementPrincipal());
    }

    public function executeContact(sfWebRequest $request) {

    }

    public function executeMentionsLegales(sfWebRequest $request) {

    }

    protected function forwardSecure() {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }
}
