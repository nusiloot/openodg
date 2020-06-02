<?php

/**
 * produit actions.
 *
 * @package    declarvin
 * @subpackage produit
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class produitActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
      if(!$request->getParameter('date')) {

        return $this->redirect('produits', array('date' => date('Y-m-d')));
      }
      set_time_limit(0);

      $this->date = $request->getParameter('date');
      $this->config = ConfigurationClient::getConfiguration($this->date);
      $this->produits = $this->config->declaration->getProduits($this->date);
      $this->notDisplayDroit = true;
  }

  public function executeOdg(sfWebRequest $request) {
      $this->odg = $request->getParameter('odg');
      if(!$request->getParameter('date')) {

        return $this->redirect('produits_odg', array('odg' => $this->odg, 'date' => date('Y-m-d')));
      }
      set_time_limit(0);

      $this->date = $request->getParameter('date');
      $this->config = ConfigurationClient::getConfiguration($this->date);

      $this->notDisplayDroit = true;
      $this->produits = array();
      $this->produitsOdg = DRevConfiguration::getInstance()->getOdgProduits($this->odg);
      $this->odgInfos = DRevConfiguration::getInstance()->getOdgRegionInfos($this->odg);
      foreach($this->config->declaration->getProduits($this->date) as $produit) {
          foreach($this->produitsOdg as $produitOdgHash) {
              if(!preg_match('|'.$produitOdgHash.'|', $produit->getHash())) {
                  continue;
              }
              $this->produits[$produit->getHash()] = $produit;
          }
      }
  }

  public function executeModification(sfWebRequest $request)
  {
    //throw new sfException("Edition de l'arbre produit désactivé pour le moment");
  	$this->forward404Unless($request_noeud = $request->getParameter('noeud', null));
  	$this->forward404Unless($hash = str_replace('-', '/', $request->getParameter('hash', null)));

  	$this->interpro = 'INTERPRO-declaration';
  	$this->produit = ConfigurationClient::getCurrent()->getOrAdd(str_replace('-', '/', $hash));
    $this->noeud = $this->produit->get($request_noeud);

  	$this->form = new ProduitDefinitionForm($this->noeud);

  	if ($request->isMethod(sfWebRequest::POST)) {
      $this->form->bind($request->getParameter($this->form->getName()));
  		if ($this->form->isValid()) {
        $this->form->save();
		$this->getUser()->setFlash("notice", 'Le produit a été modifié avec success.');

        return $this->redirect('produits');
      }
    }
  }

  public function executeNouveau(sfWebRequest $request)
  {
  	$configuration = ConfigurationClient::getCurrent();
  	$this->form = new ProduitNouveauForm($configuration);
  	if (!$request->isMethod(sfWebRequest::POST)) {

      return sfView::SUCCESS;
    }

    $this->form->bind($request->getParameter($this->form->getName()));
		if ($this->form->isValid()) {
			$noeud_to_edit = $this->form->save();
      $produit = $this->form->getProduit();

			$this->getUser()->setFlash("notice", 'Le produit a été ajouté avec success.');

      return $this->redirectModification($produit->getHash(), $noeud_to_edit);
		}
  }

  protected function redirectModification($hash, $noeud_to_edit = array()) {
    if(!count($noeud_to_edit)) {

      return $this->redirect('produits');
    }

    $noeud = $noeud_to_edit[0];
    unset($noeud_to_edit[0]);

    $hash = str_replace('/', '-', $hash);

    return $this->redirect('produit_modification', array('noeud' => $noeud, 'hash' => $hash, 'noeud_to_edit' => implode("|", $noeud_to_edit)));
  }

  public function executeHabilitation(sfWebRequest $request) {
    $this->odg = $request->getParameter('odg');
    $this->odgInfos = DRevConfiguration::getInstance()->getOdgRegionInfos($this->odg);

    $this->getResponse()->setHttpHeader('Content-Type', 'application/csv');
    $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
    $this->getResponse()->setHttpHeader('Pragma', '');
    $this->getResponse()->setHttpHeader('Cache-Control', 'public');
    $this->getResponse()->setHttpHeader('Expires', '0');

    $inao_fichier = $this->odgInfos["inao"];
    
    return $this->renderText(file_get_contents(sfConfig::get('sf_root_dir').'/'.$inao_fichier));
    }

}
