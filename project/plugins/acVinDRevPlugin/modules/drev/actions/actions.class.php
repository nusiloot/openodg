<?php

class drevActions extends sfActions {

    public function executePushDR(sfWebRequest $request) {
        $this->url = $request->getParameter('url');
        $this->id = $request->getParameter('id');

        $file_path_csv = sprintf("%s/DR/%s.%s", sfConfig::get('sf_data_dir'), $this->id, "csv");
        $file_path_pdf = sprintf("%s/DR/%s.%s", sfConfig::get('sf_data_dir'), $this->id, "pdf");

        if (!file_exists($file_path_csv) || !file_exists($file_path_pdf)) {

            return $this->redirect($this->url);
        }

        $this->csv = base64_encode(file_get_contents($file_path_csv));
        $this->pdf = base64_encode(file_get_contents($file_path_pdf));
    }

    public function executeSocieteChoixEtablissement(sfWebRequest $request) {
      $usurpation = $request->getParameter('usurpation',null);
      $login = $request->getParameter('login',null);
      if($usurpation && $login){
          $this->getUser()->usurpationOn($login, $request->getReferer());
      }
      $this->etablissement = $this->getRoute()->getEtablissement();
      $this->societe = $this->etablissement->getSociete();
      $this->form = new SocieteEtablissementChoiceForm($this->etablissement);

      if ($request->isMethod(sfWebRequest::POST)) {
          $parameters = $request->getParameter($this->form->getName());
          $this->form->bind($parameters);
          if ($this->form->isValid()) {
              $values = $this->form->getValues();
              $etablissementId = $values['etablissementChoice'];
              if (!$etablissementId) {
                  throw new sfException("L'établissement n'a pas été choisi");
              }
              $etablissement = EtablissementClient::getInstance()->findByIdentifiant($etablissementId);
              if (!$etablissement) {
                  throw new sfException("L'établissement n'existe plus dans la base de donné");
              }
              $this->redirect('declaration_etablissement', array('identifiant' => $etablissementId));
          }
       }
    }


    public function executeCreate(sfWebRequest $request) {
        $etablissement = $this->getRoute()->getEtablissement();
        $this->secureEtablissement(EtablissementSecurity::DECLARANT_DREV, $etablissement);

        $campagne = $request->getParameter("campagne", ConfigurationClient::getInstance()->getCampagneManager()->getCurrent());
        $drev = DRevClient::getInstance()->createDoc($etablissement->identifiant, $campagne);
        $drev->save();

        return $this->redirect('drev_edit', $drev);
    }

    public function executeCreatePapier(sfWebRequest $request) {
        $etablissement = $this->getRoute()->getEtablissement();
        $this->secureEtablissement(EtablissementSecurity::DECLARANT_DREV, $etablissement);

        $campagne = $request->getParameter("campagne", ConfigurationClient::getInstance()->getCampagneManager()->getCurrent());
        $drev = DRevClient::getInstance()->createDoc($etablissement->identifiant, $campagne, true);
        $drev->save();

        return $this->redirect('drev_edit', $drev);
    }

    public function executeEdit(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();

        $this->secure(DRevSecurity::EDITION, $drev);

        if ($drev->exist('etape') && $drev->etape) {
            return $this->redirect('drev_' . $drev->etape, $drev);
        }

        return $this->redirect('drev_exploitation', $drev);
    }

    public function executeDelete(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        $etablissement = $drev->getEtablissementObject();
        $this->secure(DRevSecurity::EDITION, $drev);

        $drev->delete();
        $this->getUser()->setFlash("notice", "La déclaration a été supprimée avec succès.");

        return $this->redirect('declaration_etablissement', array('identifiant' => $etablissement->identifiant, 'campagne' => $drev->campagne));
    }

    public function executeDevalidation(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        if (!$this->getUser()->isAdmin()) {
          $this->secure(DRevSecurity::DEVALIDATION , $drev);
        }

        $drev->validation = null;
        $drev->validation_odg = null;
        foreach ($drev->getProduits() as $produit) {
          if($produit->exist('validation_odg') && $produit->validation_odg){
            $produit->validation_odg = null;
          }
        }
        $drev->add('etape', null);
        $drev->devalidate();
        $drev->save();

        $this->getUser()->setFlash("notice", "La déclaration a été dévalidé avec succès.");

        return $this->redirect($this->generateUrl('drev_edit', $drev));
    }

    public function executeDr(sfWebRequest $request) {
    	$this->drev = $this->getRoute()->getDRev();
    	$this->secure(DRevSecurity::EDITION, $this->drev);

        try {
            $imported = $this->drev->importFromDocumentDouanier();
        } catch (Exception $e) {
            $message = 'Le fichier que vous avez importé ne semble pas contenir les données attendus.';
            if($this->drev->getDocumentDouanierType() != DRCsvFile::CSV_TYPE_DR) {
                $message .= " Pour les SV11 et les SV12 veuillez à bien utiliser le fichier organisé par apporteur (plutôt que celui organisé par produit).";
            }
            $this->getUser()->setFlash('error', $message);

            return $this->redirect('drev_dr_upload', $this->drev);
        }

        if($imported) {
            $this->drev->save();
        }
    }

    public function executeScrapeDr(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        try {
          	FichierClient::getInstance()->scrapeAndSaveFiles($this->drev->getEtablissementObject(), $this->drev->getDocumentDouanierType(), $this->drev->campagne);
        } catch(Exception $e) {
        }

        if (!$this->drev->hasDocumentDouanier()) {

            return $this->redirect('drev_dr_upload', $this->drev);
        }

        $this->drev->importFromDocumentDouanier(true);
        $this->drev->save();

        return $this->redirect('drev_revendication_superficie', $this->drev);
    }

    public function executeDrUpload(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);
        $client = $this->drev->getDocumentDouanierClient();
        if (!$client) {
        	throw new sfException('Client not found');
        }

        $fichier = $client->findByArgs($this->drev->identifiant, $this->drev->campagne);

        if(!$fichier) {
            $fichier = $client->createDoc($this->drev->identifiant, $this->drev->campagne);
        }
        $fichier->libelle = 'Données de Récolte importées depuis la saisie de la DRev '.$this->drev->campagne;
        $this->form = new DRevUploadDrForm($fichier, array('libelle' => 'Données de Récolte importées depuis la saisie de la DRev '.$this->drev->campagne), array("papier" => $this->drev->isPapier()));

        if (!$request->isMethod(sfWebRequest::POST)) {
        	return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
	    }

        if (!$this->form->getValue('file')) {

        	return $this->redirect('drev_revendication_superficie', $this->drev);
        }

        $this->form->save();

        try {
            $this->drev->importFromDocumentDouanier(true);
        } catch(Exception $e) {

            $message = 'Le fichier que vous avez importé ne semble pas contenir les données attendus.';

            if($this->drev->getDocumentDouanierType() != DRCsvFile::CSV_TYPE_DR) {
                $message .= " Pour les SV11 et les SV12 veillez à bien utiliser le fichier organisé par apporteur (plutôt que celui organisé par produit).";
            }

            $this->getUser()->setFlash('error', $message);

            return $this->redirect('drev_dr_upload', $this->drev);
        }
	    $this->drev->save();

        return $this->redirect('drev_revendication_superficie', $this->drev);
    }

    public function executeDrRecuperation(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $drev);

        return $this->redirect(sfConfig::get('app_url_dr_recuperation') .
                        "?" .
                        http_build_query(array(
                            'url' => $this->generateUrl('drev_dr_import', $drev, true),
                            'id' => sprintf('DR-%s-%s', $drev->identifiant, $drev->campagne))));
    }

    public function executeExploitation(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_EXPLOITATION))) {
            $this->drev->save();
        }

        $this->etablissement = $this->drev->getEtablissementObject();

        $this->form = new EtablissementForm($this->drev->declarant, array("use_email" => !$this->drev->isPapier()));

        $this->denominationAutoForm = new DRevDenominationAutoForm($this->drev);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        if (DrevConfiguration::getInstance()->hasExploitationSave()) {
          $this->form->save();
        }

        if ($this->form->hasUpdatedValues() && !$this->drev->isPapier()) {
        	Email::getInstance()->sendNotificationModificationsExploitation($this->drev->getEtablissementObject(), $this->form->getUpdatedValues());
        }

        if ($request->isXmlHttpRequest()) {

            return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->etablissement->_id, "revision" => $this->etablissement->_rev))));
        }

        if ($request->getParameter('redirect', null)) {
            return $this->redirect('drev_validation', $this->drev);
        }

        return $this->redirect('drev_dr', $this->drev);
    }

    public function executeDenominationAuto(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->form = new DRevDenominationAutoForm($this->drev);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->drev->add('denomination_auto',$this->form->getValue('denomination_auto'));
                $this->drev->save();
                return $this->redirect('drev_exploitation', $this->drev);
            }

        }


        return $this->redirect('drev_exploitation', $this->drev);
    }

    private function needDrDouane() {

        return (!$this->drev->hasDocumentDouanier() && ($this->drev->getDocumentDouanierType() == DRCsvFile::CSV_TYPE_DR) && !$this->drev->isPapier());
    }

    public function executeRevendicationSuperficie(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();

        $this->secure(DRevSecurity::EDITION, $this->drev);
        if ($this->needDrDouane() && !$this->getUser()->isAdmin()) {
        	return $this->redirect('drev_dr_upload', $this->drev);
        }

        if (!count($this->drev->declaration)) {
            $drev_previous = DRevClient::getInstance()->find(sprintf("DREV-%s-%s", $this->drev->identifiant, $this->drev->campagne - 1));
            if($drev_previous) {
                  $this->drev->updateFromDRev($drev_previous);
            }
        }
        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_REVENDICATION_SUPERFICIE))) {
            $this->drev->save();
        }

        $this->ajoutForm = new DRevRevendicationAjoutProduitForm($this->drev);
        $this->form = new DRevSuperficieForm($this->drev, array('disabled_dr' => true));

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid() && $request->isXmlHttpRequest()) {
               return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
        }
        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $this->form->save();

        if ($request->isXmlHttpRequest()) {

           return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
       }

        if ($this->drev->exist('etape') && $this->drev->etape == DrevEtapes::ETAPE_VALIDATION) {

            return $this->redirect('drev_validation', $this->drev);
        }

        return $this->redirect('drev_vci', $this->drev);

    }

    public function executeResetVolumes(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);
        foreach ($this->drev->getProduits() as $prod) {
            if(!$prod->canCalculTheoriticalVolumeRevendiqueIssuRecolte()) {
                $prod->volume_revendique_issu_recolte = null;
                continue;
            }

            $prod->volume_revendique_issu_recolte = $prod->getTheoriticalVolumeRevendiqueIssuRecole();
        }
        $this->drev->save();
        return $this->redirect('drev_revendication', $this->drev);
    }

    public function executeLots(sfWebRequest $request) {

        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);
        $this->isAdmin = $this->getUser()->isAdmin();

        if ($this->needDrDouane()) {

            return $this->redirect('drev_dr_upload', $this->drev);
        }
        $has = false;
        if(count($this->drev->getLots())){
            $has = true;
        }
        if(!$has && !count($this->drev->getProduitsLots()) && !$request->getParameter('prec') && !$this->drev->isModificative()) {

            return $this->redirect('drev_revendication', $this->drev);
        }

        if(!$has && !count($this->drev->getProduitsLots()) && $request->getParameter('prec')) {

            return $this->redirect('drev_vci', array('sf_subject' => $this->drev, 'prec' => 1));
        }

        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_LOTS))) {
            $this->drev->save();
        }

        $this->drev->addLot();
        $this->form = new DRevLotsForm($this->drev);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $this->form->save();

        if($request->getParameter('submit') == 'add') {
            return $this->redirect($this->generateUrl('drev_lots', $this->drev).'#dernier');
        }

        if(ConfigurationClient::getCurrent()->declaration->isRevendicationParLots() && $this->drev->isModificative()){
          return $this->redirect('drev_validation', $this->drev);
        }

        return $this->redirect('drev_revendication', $this->drev);
    }

    public function executeDeleteLots(sfWebRequest $request){
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        if(!isset($this->drev->lots[$request->getParameter('appellation')])){
          throw new sfException("le lot d'index ".$request->getParameter('appellation')." n'existe pas ");
        }

        $lot = $this->drev->lots[$request->getParameter('appellation')];
        if($lot){
            $this->drev->remove($lot->getHash());
        }

        $this->drev->save();
        return $this->redirect('drev_lots', $this->drev);

    }

    public function executeRevendication(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);
        if($this->drev->isModificative() && !$this->getUser()->hasDrevAdmin()){
            throw new sfException("Il est impossible d'acceder à une Drev modificatrice pour les volumes revendiquées si vous n'êtes pas administrateur.");
        }
        if ($this->needDrDouane()) {

        	return $this->redirect('drev_dr_upload', $this->drev);
        }

        if(!count($this->drev->getProduitsWithoutLots()) && !$request->getParameter('prec')) {

            return $this->redirect('drev_validation', $this->drev);
        }
        $produits = $this->drev->getProduitsLots();


        if(!count($this->drev->getProduitsWithoutLots()) && $request->getParameter('prec')) {

            return $this->redirect('drev_lots', $this->drev);
        }

        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_REVENDICATION))) {
            $this->drev->save();
        }

        $this->appellation = false;
        if ($request->getParameter(('appellation'))) {
            $this->appellation = $request->getParameter(('appellation'));
            $this->appellation_field = substr(strrchr($this->appellation, '-'), 1);
            $this->appellation_hash = str_replace('-', '/', str_replace('-' . $this->appellation_field, '', $this->appellation));
        }

        $this->form = new DRevRevendicationForm($this->drev, array('disabled_dr' => true));
        $this->ajoutForm = new DRevRevendicationAjoutProduitForm($this->drev);
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if (!$this->form->isValid() && $request->isXmlHttpRequest()) {
                return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
            }
            if ($this->form->isValid()) {
                $this->form->save();

                if ($request->isXmlHttpRequest()) {

                    return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
                }

                return $this->redirect('drev_validation', $this->drev);
            }
        }
    }

    public function executeRevendicationAjoutProduit(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        $this->ajoutForm = new DrevRevendicationAjoutProduitForm($this->drev);
        $this->ajoutForm->bind($request->getParameter($this->ajoutForm->getName()));

        if (!$this->ajoutForm->isValid()) {
            $this->getUser()->setFlash("erreur", 'Une erreur est survenue.');

            return $this->redirect('drev_revendication_superficie', $this->drev);
        }

        $this->ajoutForm->save();

        $this->getUser()->setFlash("notice", 'Le produit a été ajouté avec succès.');

        return $this->redirect('drev_revendication_superficie', $this->drev);
    }

    public function executeRevendicationCepageSuppressionProduit(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);
        $this->hash = str_replace('__', '/', $request->getParameter('hash'));
        if ($this->hash) {
        	$this->drev->remove($this->hash);
        	$this->drev->save();
        }
        return $this->redirect('drev_revendication_superficie', $this->drev);
    }

    public function executeVci(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        if ($this->needDrDouane()) {

        	return $this->redirect('drev_dr_upload', $this->drev);
        }

        if(!count($this->drev->getProduitsVci()) && count($this->drev->declaration->getProduitsLots()) > 0) {

            return $this->redirect('drev_lots', $this->drev);
        }

        if(!count($this->drev->getProduitsVci()) && !$request->getParameter('prec')) {

            return $this->redirect('drev_revendication', $this->drev);
        }

        if(!count($this->drev->getProduitsVci()) && $request->getParameter('prec')) {

            return $this->redirect('drev_revendication_superficie', $this->drev);
        }

        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_VCI))) {
            $this->drev->save();
        }

        $this->form = new DRevVciForm($this->drev);

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid() && $request->isXmlHttpRequest()) {
               return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
        }

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $this->form->save();

        if ($request->isXmlHttpRequest()) {

           return $this->renderText(json_encode(array("success" => true, "document" => array("id" => $this->drev->_id, "revision" => $this->drev->_rev))));
       }

        if ($this->drev->exist('etape') && $this->drev->etape == DrevEtapes::ETAPE_VALIDATION) {

            return $this->redirect('drev_validation', $this->drev);
        }

        if(count($this->drev->declaration->getProduitsLots()) > 0){
            return $this->redirect('drev_lots', $this->drev);
        }

        return $this->redirect('drev_revendication', $this->drev);

    }

    public function executeValidation(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::EDITION, $this->drev);

        if ($this->needDrDouane()) {

        	return $this->redirect('drev_dr_upload', $this->drev);
        }

        if($this->drev->storeEtape($this->getEtape($this->drev, DrevEtapes::ETAPE_VALIDATION))) {
            $this->drev->save();
        }

        $this->drev->cleanDoc();

        $this->validation = new DRevValidation($this->drev);

        $this->form = new DRevValidationForm($this->drev, array(), array('engagements' => $this->validation->getPoints(DrevValidation::TYPE_ENGAGEMENT)));

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }

        if (!$this->validation->isValide() && $this->drev->isTeledeclare() && !$this->getUser()->hasDrevAdmin()) {

            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $documents = $this->drev->getOrAdd('documents');

        foreach ($this->validation->getPoints(DrevValidation::TYPE_ENGAGEMENT) as $engagement) {
            $document = $documents->add($engagement->getCode());
            if ($engagement->getCode() == DRevDocuments::DOC_VCI) {
            	$document->statut = DRevDocuments::STATUT_RECU;
            } else {
            	$document->statut = (($engagement->getCode() == DRevDocuments::DOC_DR && $this->drev->hasDr()) || ($document->statut == DRevDocuments::STATUT_RECU)) ? DRevDocuments::STATUT_RECU : DRevDocuments::STATUT_EN_ATTENTE;
            }
        }

        if($this->drev->isPapier()) {
            $this->drev->validate($this->form->getValue("date"));
        } else {
            $this->drev->validate();
        }

        if($this->getUser()->isAdmin() && !DrevConfiguration::getInstance()->hasValidationOdg()) {
            $this->drev->validateOdg();
        }

        $this->drev->save();

        if($this->getUser()->isAdmin() && $this->drev->isPapier()) {
            Email::getInstance()->sendDrevPapierConfirmee($this->drev);
            $this->getUser()->setFlash("notice", "La déclaration a bien été validée");

            return $this->redirect('drev_visualisation', $this->drev);
        }

        if(!$this->getUser()->hasDrevAdmin() && !$this->getUser()->isAdmin()) {
          $this->sendDRevValidation($this->drev);
          $this->getUser()->setFlash("notice", "La déclaration a bien été validée");
          return $this->redirect('drev_visualisation', $this->drev);
        }

        return $this->redirect('drev_confirmation', $this->drev);
    }

    public function executeConfirmation(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::VISUALISATION, $this->drev);
    }

    public function executeGenerateMouvements(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::VISUALISATION, $this->drev);

        if(count($this->drev->mouvements)) {

            return $this->redirect('drev_visualisation', $this->drev);
        }

        $this->drev->generateMouvements();
        $this->drev->save();

        $this->getUser()->setFlash('notice', 'Les mouvements ont été générés');

        return $this->redirect('drev_visualisation', $this->drev);
    }

    public function executeVisualisation(sfWebRequest $request) {
        $this->drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::VISUALISATION, $this->drev);

        $this->service = $request->getParameter('service');

        $documents = $this->drev->getOrAdd('documents');
        $this->regionParam = $request->getParameter('region',null);
        if (!$this->regionParam && $this->getUser()->getCompte() && $this->getUser()->getCompte()->exist('region')) {
            $this->regionParam = $this->getUser()->getCompte()->region;
        }

        if($this->getUser()->hasDrevAdmin() || $this->drev->validation) {
            $this->validation = new DRevValidation($this->drev);
        }

        $this->form = (count($documents->toArray()) && !$this->drev->hasCompleteDocuments() && $this->getUser()->isAdmin() && $this->drev->validation && !$this->drev->validation_odg) ? new DRevDocumentsForm($documents) : null;

        if (!$request->isMethod(sfWebRequest::POST)) {

            return sfView::SUCCESS;
        }
        $this->form->bind($request->getParameter($this->form->getName()));

        if (!$this->form->isValid()) {

            return sfView::SUCCESS;
        }

        $this->form->save();

        return $this->redirect('drev_visualisation', $this->drev);
    }

    public function executeValidationAdmin(sfWebRequest $request) {
        if(!DrevConfiguration::getInstance()->hasValidationOdg()){
          throw new sfException("Il n'est pas permis de valider par ODG");
        }

        $this->drev = $this->getRoute()->getDRev();
        $this->secure(array(DRevSecurity::VALIDATION_ADMIN), $this->drev);
        $this->regionParam = $request->getParameter('region',null);

        $this->drev->validateOdg(null,$this->regionParam);
        $this->drev->save();

        if (!$this->drev->isPapier() && $this->drev->getValidationOdg()) {
            $this->sendDRevConfirmee($this->drev);
        }

        $this->getUser()->setFlash("notice", "La déclaration a bien été approuvée. Un email a été envoyé au télédéclarant.");

        $service = $request->getParameter("service");
        $params = array('sf_subject' => $this->drev, 'service' => isset($service) ? $service : null);
        if($this->regionParam){
          $params = array_merge($params,array('region' => $this->regionParam));
        }
        return $this->redirect('drev_visualisation', $params);
    }

    public function executeModificative(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();

        $drev_modificative = $drev->generateModificative();
        $drev_modificative->save();
        if(ConfigurationClient::getCurrent()->declaration->isRevendicationParLots()){
          return $this->redirect('drev_lots', $drev_modificative);
        }

        return $this->redirect('drev_edit', $drev_modificative);
    }

    public function executePDF(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::PDF, $drev);

        if (!$drev->validation) {
            $drev->cleanDoc();
        }

        $this->document = new ExportDRevPdf($drev, $this->getRequestParameter('region', null), $this->getRequestParameter('output', 'pdf'), false);
        $this->document->setPartialFunction(array($this, 'getPartial'));

        if ($request->getParameter('force')) {
            $this->document->removeCache();
        }

        $this->document->generate();

        $this->document->addHeaders($this->getResponse());

        return $this->renderText($this->document->output());
    }

    public function executeXML(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::VISUALISATION, $drev);
        if (!$drev->validation) {
            $drev->cleanDoc();
        }
		$xml = $this->getPartial('drev/xml', array('drev' => $drev));
        $this->getResponse()->setHttpHeader('md5', md5($xml));
        $this->getResponse()->setHttpHeader('LastDocDate', date('r'));
        $this->getResponse()->setHttpHeader('Last-Modified', date('r'));
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');
        $this->getResponse()->setContentType('text/xml');
        $this->getResponse()->setHttpHeader('Content-Disposition', "attachment; filename=".$drev->_id."_".$drev->_rev.".xml");
        return $this->renderText($xml);
    }

    public function executeSendoi(sfWebRequest $request) {
      
    	$drev = $this->getRoute()->getDRev();
    	$this->secure(DRevSecurity::VISUALISATION, $drev);
      $drevOi = new DRevOI($drev, null);
      $drevOi->send();

    	return $this->redirect('drev_visualisation', $drev);
    }

    public function executeDrPdf(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();
        $this->secure(DRevSecurity::VISUALISATION, $drev);

        $file = file_get_contents($drev->getAttachmentUri('DR.pdf'));

        if(!$file) {

            $this->forward404();
        }

        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', sprintf('attachment; filename="DR-%s-%s.pdf"', $drev->identifiant, $drev->campagne));
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');

        return $this->renderText($file);
    }

    public function executeDocumentDouanierPdf(sfWebRequest $request) {
        $drev = $this->getRoute()->getDRev();

        $this->getResponse()->setHttpHeader('Content-Type', 'application/pdf');
        $this->getResponse()->setHttpHeader('Content-disposition', sprintf('attachment; filename="'.$drev->getDocumentDouanierType().'-%s-%s.pdf"', $drev->identifiant, $drev->campagne));
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Pragma', '');
        $this->getResponse()->setHttpHeader('Cache-Control', 'public');
        $this->getResponse()->setHttpHeader('Expires', '0');

        return $this->renderText(file_get_contents($drev->getDocumentDouanier('pdf')));
    }

    public function executeMain()
    {
    }

    protected function getEtape($drev, $etape) {
        $drevEtapes = DrevEtapes::getInstance();
        if (!$drev->exist('etape')) {
            return $etape;
        }
        return ($drevEtapes->isLt($drev->etape, $etape)) ? $etape : $drev->etape;
    }

    protected function sendDRevValidation($drev) {
        $pdf = new ExportDRevPdf($drev, null, 'pdf', true);
        $pdf->setPartialFunction(array($this, 'getPartial'));
        $pdf->removeCache();
        $pdf->generate();
        Email::getInstance()->sendDRevValidation($drev);
    }

    protected function sendDrevConfirmee($drev) {
        Email::getInstance()->sendDrevConfirmee($drev);
    }

    protected function secure($droits, $doc) {
        if (!DRevSecurity::getInstance($this->getUser(), $doc)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function secureEtablissement($droits, $etablissement) {
        if (!EtablissementSecurity::getInstance($this->getUser(), $etablissement)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function forwardSecure() {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }

}
