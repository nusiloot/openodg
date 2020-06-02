<?php

class constatsActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        if (($tourneesRecapDate = $request->getParameter('tourneesRecapDate')) && $request->isMethod(sfWebRequest::POST)) {
            $this->jour = Date::getIsoDateFromFrenchDate($tourneesRecapDate['date']);
            return $this->redirect('constats', array('jour' => $this->jour));
        }
        $this->jour = $request->getParameter('jour');

        $this->organisationJournee = RendezvousClient::getInstance()->buildOrganisationNbDays(2, $this->jour);
        $this->rendezvousNonPlanifies = RendezvousClient::getInstance()->getRendezvousByNonPlanifiesNbDays(2, $this->jour);
        $this->formDate = new TourneesRecapDateForm(array('date' => Date::francizeDate($this->jour)));
        $this->form = new LoginForm();

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        return $this->redirect('rendezvous_declarant', $this->form->getValue('etablissement')->getCompte());
    }

    public function executePlanificationJour(sfWebRequest $request) {
        $this->jour = $request->getParameter('jour');
        $this->rendezvousJournee = RendezvousClient::getInstance()->buildRendezvousJournee($this->jour);
        $this->tourneesJournee = TourneeClient::getInstance()->buildTourneesJournee($this->jour);
    }

    public function executeTourneeDelete(sfWebRequest $request) {
        $tournee = TourneeClient::getInstance()->find($request->getParameter('id'));

        if(!$tournee) {

            return $this->forward404(sprintf("La tournée %s n'existe pas !", $request->getParameter('id')));
        }

        if(count($tournee->rendezvous) > 0) {

            return $this->forward404(sprintf("La tournée %s ne peut pas être supprimé car elle a des rendez-vous !", $request->getParameter('id')));
        }

        $date = $tournee->date;

        $tournee->delete();

        return $this->redirect('constats_planification_jour', array('jour' => $date));
    }

    public function executeTourneeAgentRendezvous(sfWebRequest $request) {
        $request->setParameter('modeMobile', true);
        $this->tournee = $this->getRoute()->getTournee();
        $this->agent = $this->tournee->getFirstAgent();
        $this->date = $this->tournee->getDate();
        $this->lock = false;
        $this->reload = $request->getParameter('reload', 0);
        $this->constructProduitsList();
        $this->contenants = ConstatsClient::getInstance()->getContenantsLibelle();
        $this->raisonsRefus = ConstatsClient::getInstance()->getRaisonsRefusLibelle();
        $this->constats = array();
    }

    public function executeTourneeAgentJsonRendezvous(sfWebRequest $request) {
        $this->tournee = $this->getRoute()->getTournee();

        $json = array();
        $constats = array();

        foreach ($this->tournee->getRendezvous() as $idrendezvous => $rendezvous) {
            $constats[$rendezvous->constat] = ConstatsClient::getInstance()->find($rendezvous->constat);
        }
        foreach ($this->tournee->getRendezvous() as $idrendezvous => $rendezvous) {
            $rdvConstats = array();

            $rdvJson = $rendezvous->toJson();
            $rdvJson->compte_raison_sociale = Anonymization::hideIfNeeded($rdvJson->compte_raison_sociale);
            $rdvJson->compte_adresse = Anonymization::hideIfNeeded($rdvJson->compte_adresse);
            $rdvJson->compte_telephone_mobile = Anonymization::hideIfNeeded($rdvJson->compte_telephone_mobile);
            $rdvJson->compte_telephone_bureau = Anonymization::hideIfNeeded($rdvJson->compte_telephone_bureau);
            $rdvJson->compte_telephone_prive = Anonymization::hideIfNeeded($rdvJson->compte_telephone_prive);
            $rdvJson->compte_email = Anonymization::hideIfNeeded($rdvJson->compte_email);
            $rdvJson->nb_non_saisis = 0;
            $rdvJson->nb_refuses = 0;
            $rdvJson->nb_approuves = 0;
            $rdvJson->nb_assembles = 0;
            $rdvJson->termine = true;
            $rdvJson->hasOneCommentaire = false;
            $rdvJson->nom_agent_origine = $this->tournee->getFirstAgent()->getNom();
            $rdvConstats['constats'] = array();

            foreach ($constats[$rendezvous->constat]->constats as $constatkey => $constatNode) {
                $constatNodeJson = $constatNode->toJson();
                $constatNodeJson->idconstatdoc = $rendezvous->constat;
                $constatNodeJson->idconstatnode = $constatkey;
                if ($idrendezvous == $constatNode->rendezvous_raisin) {
                    $constatNodeJson->type_constat = 'raisin';
                    if (!$constatNodeJson->produit) {
                        $rdvJson->termine = false;
                    }
                    $constatNodeJson->nom_agent_origine = $this->tournee->getFirstAgent()->getNom();
                    $rdvConstats['constats'][$constatNodeJson->idconstatdoc . '_' . $constatNodeJson->idconstatnode] = $constatNodeJson;
                    if ($constatNodeJson->statut_raisin == ConstatsClient::STATUT_REFUSE) {
                        $rdvJson->nb_refuses++;
                    } elseif ($constatNodeJson->statut_raisin == ConstatsClient::STATUT_APPROUVE) {
                        $rdvJson->nb_approuves++;
                    } else {
                        $rdvJson->nb_non_saisis++;
                    }
                }
                if ($idrendezvous == $constatNode->rendezvous_volume) {
                    $constatNodeJson->type_constat = 'volume';
                    $constatNodeJson->email = $rdvJson->compte_email;
                    if ($constatNodeJson->commentaire_raisin) {
                        $rdvJson->hasOneCommentaire = true;
                    }
                    if (($constatNodeJson->statut_volume != ConstatsClient::STATUT_APPROUVE) && ($constatNodeJson->statut_volume != ConstatsClient::STATUT_REFUSE)) {
                        $rdvJson->termine = false;
                    }
                    $rdvConstats['constats'][$constatNodeJson->idconstatdoc . '_' . $constatNodeJson->idconstatnode] = $constatNodeJson;
                    if ($constatNodeJson->statut_volume == ConstatsClient::STATUT_REFUSE) {
                        if ($constatNodeJson->raison_refus == ConstatsClient::RAISON_REFUS_ASSEMBLE) {
                            $rdvJson->nb_assembles++;
                        } else {
                            $rdvJson->nb_refuses++;
                        }
                    } elseif ($constatNodeJson->statut_volume == ConstatsClient::STATUT_APPROUVE) {
                        $rdvJson->nb_approuves++;
                    } else {
                        $rdvJson->nb_non_saisis++;
                    }
                }
            }
            $rdvConstats['heure'] = $rdvJson->heure;
            $rdvConstats['rendezvous'] = $rdvJson;
            $rdvConstats['idrdv'] = $idrendezvous;
            $rdvConstats['typerendezvous'] = $rdvJson->type_rendezvous;
            $rdvConstats['nomAgentOrigine'] = $rdvJson->nom_agent_origine;
            $rdvConstats['isRendezvousRaisin'] = ($rdvJson->type_rendezvous == RendezvousClient::RENDEZVOUS_TYPE_RAISIN);
            $json[$idrendezvous] = $rdvConstats;
        }

        if (!$request->isMethod(sfWebRequest::POST)) {
            $this->response->setContentType('application/json');

            return $this->renderText(json_encode($json));
        }

        $json = json_decode($request->getContent());
        $json_return = array();

        foreach ($json as $json_content) {
            $constat = ConstatsClient::getInstance()->find($json_content->idconstatdoc);
            $constat->updateAndSaveConstatNodeFromJson($json_content->idconstatnode, $json_content);
            $json_return[$json_content->_idNode] = true;
        }
        $this->response->setContentType('application/json');

        return $this->renderText(json_encode($json_return));
    }

    public function executeAjoutAgentTournee(sfWebRequest $request) {
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('Date'));
        $this->jour = $request->getParameter('jour');
        $this->retour = $request->getParameter('retour', null);
        $this->form = new TourneeAddAgentForm(array('date' => format_date($this->jour, "dd/MM/yyyy", "fr_FR")));
        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }
        $compteAgent = CompteClient::getInstance()->find('COMPTE-' . $this->form->getValue('agent'));
        $tournee = TourneeClient::getInstance()->findOrAddByDateAndAgent($this->form->getValue('date'), $compteAgent);
        if ($this->retour && $this->retour == 'planification') {
            $this->redirect('constats_planifications', array('date' => $this->jour));
        }
        $this->redirect('constats_planification_jour', array('jour' => $this->jour));
    }

    public function executePlanifications(sfWebRequest $request) {
        $this->jour = $request->getParameter('date');
        $this->couleurs = array("#91204d", "#fa6900", "#1693a5", "#e05d6f", "#7ab317", "#ffba06", "#907860", "#172f77", "#24e4BD", "#fc1307", "#fc0afc", "#52e9af");
        $this->rdvsPris = RendezvousClient::getInstance()->getRendezvousByDateAndStatut($this->jour, RendezvousClient::RENDEZVOUS_STATUT_PRIS);
        $this->rdvsRealises = RendezvousClient::getInstance()->getRendezvousByDateAndStatut($this->jour, RendezvousClient::RENDEZVOUS_STATUT_REALISE);
        $this->rdvsAnnules = RendezvousClient::getInstance()->getRendezvousByDateAndStatut($this->jour, RendezvousClient::RENDEZVOUS_STATUT_ANNULE);

        $this->tournees = TourneeClient::getInstance()->getTourneesByDateAndType($this->jour, TourneeClient::TYPE_TOURNEE_CONSTAT_VTSGN);

        $this->heures = array();
        for ($i = 7; $i <= 22; $i++) {
            $this->heures[sprintf("%02d:00", $i)] = sprintf("%02d", $i);
        }

        $this->tourneesCouleur = array();
        $i = 0;
        foreach ($this->tournees as $tournee) {
            $this->tourneesCouleur[$tournee->_id] = $this->couleurs[$i];
            $i++;
        }

        $this->rdvs = array();
        $this->rdvsSansHeure = array();
        foreach ($this->tournees as $tournee) {
            foreach ($tournee->rendezvous as $id => $rendezvous) {
                if ($rendezvous->type_rendezvous == RendezvousClient::RENDEZVOUS_TYPE_RAISIN) {
                    $this->rdvs[$rendezvous->getHeure()][$tournee->_id][$id] = $rendezvous;
                }
                if ($rendezvous->type_rendezvous == RendezvousClient::RENDEZVOUS_TYPE_VOLUME) {
                    $this->rdvs['no-hour'][$tournee->_id][$id] = $rendezvous;
                }
            }
        }

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }


        $rdvValues = $request->getParameter("rdvs", array());

        foreach ($rdvValues as $id_rdv => $values) {
            if ($values['tournee']) {
                $tournee = $this->tournees[$values['tournee']];
                $tournee->addRendezVousAndGenerateConstat($id_rdv);
                $tournee->save();
            } else {
                $rdv = RendezvousClient::getInstance()->find($id_rdv);
                $rdv->set('statut', RendezvousClient::RENDEZVOUS_STATUT_PRIS);
                $rdv->save();
            }
            foreach ($this->tournees as $tournee) {
                if (!$tournee->rendezvous->exist($id_rdv)) {
                    continue;
                }
                if ($tournee->_id == $values['tournee']) {
                    continue;
                }
                $tournee->rendezvous->remove($id_rdv);
                $tournee->save();
            }
        }

        if ($request->isXmlHttpRequest()) {

            return $this->renderText(json_encode(array("success" => true, "document" => array("id" => null, "revision" => null))));
        }

        return $this->redirect('constats_planification_jour', array('jour' => $this->jour));
    }

    public function executeRendezvousDeclarant(sfWebRequest $request) {

        $this->compte = $this->getRoute()->getCompte();
        $this->rendezvousConstatsDeclarant = RendezvousClient::getInstance()->getRendezvousConstatsByCompte($this->compte->cvi);
        $this->formsRendezVous = array();
        $this->form = new LoginForm();
        foreach ($this->compte->getChais() as $chaiKey => $chai) {
            $rendezvous = new Rendezvous();
            $rendezvous->identifiant = $this->compte->identifiant;
            $rendezvous->idchai = $chaiKey;
            $this->formsRendezVous[$chaiKey] = new RendezvousDeclarantForm($rendezvous);
        }

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()));
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        return $this->redirect('rendezvous_declarant', $this->form->getValue('compte'));
    }

    public function executeRendezvousDeclarantRemove(sfWebRequest $request) {
        $rendezvous = RendezvousClient::getInstance()->find($request->getParameter('idrendezvous'));
        $compte = $rendezvous->getCompte();
        RendezvousClient::getInstance()->delete($rendezvous);
        $this->redirect('rendezvous_declarant', $compte);
    }

    public function executeRendezvousModification(sfWebRequest $request) {
        $this->rendezvous = $this->getRoute()->getRendezvous();
        $this->chai = $this->rendezvous->getChai();
        $this->retour = $request->getParameter('retour', null);
        $this->form = new RendezvousDeclarantForm($this->rendezvous);
        if (!$request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()));
        if (!$this->form->isValid()) {
            return $this->getTemplate('rendezvousDeclarant');
        }
        $this->form->save();
        if ($this->retour && $this->retour == 'planification') {
            $this->redirect('constats_planifications', array('date' => $this->rendezvous->getDate()));
        }
        $this->redirect('rendezvous_declarant', $this->rendezvous->getCompte());
    }

    public function executeRendezvousCreation(sfWebRequest $request) {
        $this->compte = $this->getRoute()->getCompte();
        $this->idchai = $request->getParameter('idchai');
        $rendezvous = new Rendezvous();
        $rendezvous->idchai = $this->idchai;
        $rendezvous->identifiant = $this->compte->identifiant;

        $this->chai = $this->compte->chais->get($this->idchai);
        $this->form = new RendezvousDeclarantForm($rendezvous);

        if (!$request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()));
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }
        $date = $this->form->getValue('date');
        $heure = $this->form->getValue('heure');
        $commentaire = $this->form->getValue('commentaire');
        $rendezvous = RendezvousClient::getInstance()->findOrCreate($this->compte, $this->idchai, $date, $heure, $commentaire);
        $rendezvous->save();
        $this->sendMailConfirmationPriseRendezVous($rendezvous);
        $this->redirect('rendezvous_declarant', $this->compte);
    }

    public function executeConstatPdf(sfWebRequest $request) {
        $this->constats = $this->getRoute()->getConstats();
        $this->constatNode = $request->getParameter('identifiantconstat');

        if(!$this->getUser()->isAdmin() && $this->getUser()->getEtablissement() && $this->constats->getCompte()->_id != $this->getUser()->getEtablissement()->getCompte()->_id) {

            return $this->forwardSecure();
        }

        $this->document = new ExportConstatPdf($this->constats, $this->constatNode, $this->getRequestParameter('output', 'pdf'), false);
        $this->document->setPartialFunction(array($this, 'getPartial'));

        if ($request->getParameter('force')) {
            $this->document->removeCache();
        }

        $this->document->generate();

        $this->document->addHeaders($this->getResponse());

        return $this->renderText($this->document->output());
    }

    private function constructProduitsList() {
        $this->produits = array();
        foreach (ConstatsClient::getInstance()->getProduits() as $produit) {
            if (!$produit->hasVtsgn()) {
                continue;
            }

            $this->produits[$produit->getHash()] = $produit->getLibelleComplet(true);
        }
    }

    private function sendMailConfirmationPriseRendezVous($rendezvous) {
        Email::getInstance()->sendPriseDeRendezvousMails($rendezvous);
    }

    public function executeRedirectInterfaceMobileAgent(sfWebRequest $request) {
        $request->setParameter('modeMobile', true);
        $this->form = new RedirectAgentForm();
        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }
        $compteAgent = CompteClient::getInstance()->find('COMPTE-' . $this->form->getValue('agent'));
        $tournee = TourneeClient::getInstance()->findByDateAndAgent(date('Y-m-d'), $compteAgent);
        if ($tournee) {
            $this->redirect('tournee_rendezvous_agent', $tournee);
        }

        $tournee = TourneeClient::getInstance()->findTourneeDegustationByDateAndAgent(date('Y-m-d'), $compteAgent);
        if($tournee) {
            $this->redirect('degustation_tournee', array('sf_subject' => $tournee, 'agent' => $compteAgent->_id, 'date' => date('Y-m-d')));
        }

        $this->getUser()->setFlash('error', "Aucune tournée trouvée pour cet agent aujourd'hui");

        return $this->redirect('tournee_agent_accueil');
    }

    protected function forwardSecure() {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

}
