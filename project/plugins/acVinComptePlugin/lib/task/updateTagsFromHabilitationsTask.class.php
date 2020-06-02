<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * updateTagsFromHabilitationsTask
 *
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class updateTagsFromHabilitationsTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'compte';
        $this->name = 'updateTagsFromHabilitations';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array()) {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $context = sfContext::createInstance($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        $etbsWithHabilitations = HabilitationClient::getInstance()->getAllEtablissementsWithHabilitations();
        foreach ($etbsWithHabilitations as $key => $etbWithHabilitation) {
          $h = HabilitationClient::getInstance()->getLastHabilitation($etbWithHabilitation);
          $etb = EtablissementCLient::getInstance()->find($h->getIdentifiant());
          $compte = CompteClient::getInstance()->find($etb->getCompte());
          $compte->tags->remove('produit');
          $compte->tags->add('produit');
          $compte->tags->remove('activite');
          $compte->tags->add('activite');
          $compte->tags->remove('statuts');
          $compte->tags->add('statuts');
          $activiteTags = array();
          $statutsTags = array();
          foreach ($h->getProduits(false) as $prod) {
            $hasOneActivite = false;
            foreach ($prod->activites as $keyActivite => $hActivite) {
              if($hActivite->isHabilite()){
                $activitetag = HabilitationClient::getInstance()->getLibelleActivite($keyActivite);
                $activiteTags[$activitetag] = $activitetag;
                $hasOneActivite = true;
              }
              if($hActivite->exist("date") && $hActivite->date){
                $statutTag = HabilitationClient::$statuts_libelles[$hActivite->getStatut()];
                $statutsTags[$statutTag] = $statutTag;
              }
            }
            if($hasOneActivite){
              $compte->addTag("produit", KeyInflector::unaccent(str_replace("'","",$prod->getLibelle())));
              echo "affecte au compte ".$compte->_id." le produit ".$prod->getLibelle()."\n";
            }
          }
          foreach ($activiteTags as $activiteTagToSet) {
            $compte->addTag("activite", KeyInflector::unaccent(str_replace("'","",$activiteTagToSet)));
            echo "affecte au compte ".$compte->_id." l'activité ".$activiteTagToSet."\n";
          }
          foreach ($statutsTags as $statutTags) {
            $compte->addTag("statuts", KeyInflector::unaccent(str_replace("'","",$statutTags)));
            echo "affecte au compte ".$compte->_id." le statut ".$statutTags."\n";
          }
          $compte->save();
        }
      }
}
