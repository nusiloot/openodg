<?php

require_once(dirname(__FILE__).'/../bootstrap/common.php');


foreach (CompteTagsView::getInstance()->listByTags('test', 'test') as $k => $v) {
    if (preg_match('/SOCIETE-([^ ]*)/', implode(' ', array_values($v->value)), $m)) {
      $soc = SocieteClient::getInstance()->findByIdentifiantSociete($m[1]);
      foreach($soc->getEtablissementsObj() as $k => $etabl) {
        if ($etabl->etablissement) {
        //   foreach (VracClient::getInstance()->retrieveBySoussigne($etabl->etablissement->identifiant)->rows as $k => $vrac) {
        //     $vrac_obj = VracClient::getInstance()->find($vrac->id);
        //     $vrac_obj->delete();
        //   }
        //   foreach (DRMClient::getInstance()->viewByIdentifiant($etabl->etablissement->identifiant) as $id => $drm) {
        //     $drm = DRMClient::getInstance()->find($id);
        //     $drm->delete(false);
        //   }
          $etabl->etablissement->delete();
        }
      }
    }
}


$t = new lime_test(27);
$t->comment('création des différentes établissements');

$societeviti = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_viti_societe')->getSociete();
$societeviti->siret = "00112244557788";
$societeviti->save();

$etablissementviti = $societeviti->createEtablissement(EtablissementFamilles::FAMILLE_PRODUCTEUR);
$etablissementviti->region = EtablissementClient::REGION_CVO;
$etablissementviti->nom = "Etablissement viticulteur";

$etablissementviti->email = "email@etb.com";
$etablissementviti->site_internet = "www.etb.fr";
$etablissementviti->telephone_perso = "44 44 44 44 44";
$etablissementviti->telephone_bureau = "55 55 55 55 55";
$etablissementviti->telephone_mobile = "66 66 66 66 66";
$etablissementviti->fax = "77 77 77 77 77";


$etablissementviti->adresse = "etb Adresse 1 ";
$etablissementviti->adresse_complementaire = "etb Adresse 2 ";
$etablissementviti->code_postal = '00000';
$etablissementviti->commune = "cummune etb";
$etablissementviti->pays = "FR";
$etablissementviti->insee = "98475";
$etablissementviti->ppm = "P123456798";


$etablissementviti->save();

$societeviti = $etablissementviti->getSociete();

$t->is($etablissementviti->identifiant, $societeviti->identifiant."01", "L'identifiant de l'établissement respecte celui de la société : ".$etablissementviti->identifiant);

$id = $etablissementviti->getSociete()->getidentifiant();
$compteviti = CompteClient::getInstance()->findByIdentifiant($id."01");
$compteviti->addTag('test', 'test');
$compteviti->addTag('test', 'test_viti');
$compteviti->save();
$t->is($compteviti->tags->automatique->toArray(true, false), array('etablissement','producteur'), "Création d'un etablissement viti met à jour le compte $compteviti->_id");
$t->is($etablissementviti->region, EtablissementClient::REGION_CVO, "L'établissement est en région CVO après le save");

$t->is($compteviti->_get('email'), $etablissementviti->_get('email'), "L'établissement a le même email que le compte");
$t->is($compteviti->_get('site_internet'), $etablissementviti->_get('site_internet'), "L'établissement a le même site_internet que le compte");
$t->is($compteviti->_get('telephone_perso'), $etablissementviti->_get('telephone_perso'), "L'établissement a le même telephone_perso que le compte");
$t->is($compteviti->_get('telephone_bureau'), $etablissementviti->_get('telephone_bureau'), "L'établissement a le même telephone bureau que le compte");
$t->is($compteviti->_get('telephone_mobile'), $etablissementviti->_get('telephone_mobile'), "L'établissement a le même telephone_mobile que le compte");
$t->is($compteviti->_get('fax'), $etablissementviti->_get('fax'), "L'établissement a le même fax que le compte");


$t->is($compteviti->_get('adresse'), $etablissementviti->_get('adresse'), "L'établissement a la même adresse que le compte");
$t->is($compteviti->_get('adresse_complementaire'), $etablissementviti->_get('adresse_complementaire'), "L'établissement a la même adresse_complementaire que le compte");
$t->is($compteviti->_get('code_postal'), $etablissementviti->_get('code_postal'), "L'établissement a le même code_postal que le compte");
$t->is($compteviti->_get('commune'), $etablissementviti->_get('commune'), "L'établissement a la même commune que le compte");
$t->is($compteviti->_get('pays'), $etablissementviti->_get('pays'), "L'établissement a le même pays que le compte");
$t->is($compteviti->_get('insee'), $etablissementviti->_get('insee'), "L'établissement a le même insee que le compte");
$t->is($compteviti->_get('etablissement_informations')->_get('ppm'), $etablissementviti->_get('ppm'), "L'établissement a le même ppm que le compte");

$t->is($societeviti->siret, $etablissementviti->_get('siret'), "L'établissement a le siret de la société");


$societenego = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_nego_region_societe')->getSociete();
$etablissementnego = $societenego->createEtablissement(EtablissementFamilles::FAMILLE_NEGOCIANT);
$etablissementnego->region = EtablissementClient::REGION_CVO;
$etablissementnego->nom = "Etablissement negociant de la région";
$etablissementnego->save();
$id = $etablissementnego->getSociete()->getidentifiant();
$comptenego = CompteClient::getInstance()->findByIdentifiant($id."01");
$comptenego->addTag('test', 'test');
$comptenego->save();
$t->is($comptenego->tags->automatique->toArray(true, false), array('etablissement','negociant'), "Création d'un etablissement nego met à jour le compte");
$t->is($etablissementnego->region, EtablissementClient::REGION_CVO, "L'établissement est en région CVO après le save");

$etablissementnego = EtablissementClient::getInstance()->find($etablissementnego->_id);
$etablissementviti = EtablissementClient::getInstance()->find($etablissementviti->_id);
$etablissementnego->addLiaison('COOPERATEUR', $etablissementviti->_id, true);
$etablissementnego->save();
$l_array = $etablissementnego->liaisons_operateurs->toArray(1,0);
$liaisons = array_shift($l_array);
$t->is($liaisons['type_liaison'], "COOPERATEUR", "L'établissement a une liaison Coopérateur");
$t->is($liaisons['id_etablissement'], $etablissementviti->_id, "La liaison est vers l''établissement $etablissementviti->_id");

$societenego = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_nego_region_2_societe')->getSociete();
$etablissementnego = $societenego->createEtablissement(EtablissementFamilles::FAMILLE_NEGOCIANT);
$etablissementnego->region = EtablissementClient::REGION_CVO;
$etablissementnego->nom = "Etablissement negociant 2 de la région";
$etablissementnego->save();
$id = $etablissementnego->getSociete()->getidentifiant();
$comptenego = CompteClient::getInstance()->findByIdentifiant($id."01");
$comptenego->addTag('test', 'test');
$comptenego->save();
$t->is($comptenego->tags->automatique->toArray(true, false), array('etablissement','negociant'), "Création d'un etablissement nego 2 met à jour le compte");

$societenego_horsregion = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_nego_horsregion_societe')->getSociete();
$etablissementnego_horsregion = $societenego_horsregion->createEtablissement(EtablissementFamilles::FAMILLE_NEGOCIANT);
$etablissementnego_horsregion->region = EtablissementClient::REGION_HORS_CVO;
$etablissementnego_horsregion->nom = "Etablissement negociant hors région";
$etablissementnego_horsregion->save();
$id = $etablissementnego_horsregion->getSociete()->getidentifiant();
$comptenego_horsregion = CompteClient::getInstance()->findByIdentifiant($id."01");
$comptenego_horsregion->addTag('test', 'test');
$comptenego_horsregion->save();
$t->is($comptenego_horsregion->tags->automatique->toArray(true, false), array('etablissement', 'negociant'), "Création d'un etablissement nego_horsregion met à jour le compte");
$t->is($etablissementnego_horsregion->region, EtablissementClient::REGION_HORS_CVO, "L'établissement est hors région CVO après le save");

$societecourtier = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_courtier_societe')->getSociete();
$etablissementcourtier = $societecourtier->createEtablissement(EtablissementFamilles::FAMILLE_COURTIER);
$etablissementcourtier->nom = "Etablissement de courtage";
$etablissementcourtier->save();
$id = $etablissementcourtier->getSociete()->getidentifiant();
$comptecourtier = CompteClient::getInstance()->findByIdentifiant($id."01");
$comptecourtier->addTag('test', 'test');
$comptecourtier->save();
$t->is($comptecourtier->tags->automatique->toArray(true, false), array('etablissement', 'courtier'), "Création d'un etablissement courtier met à jour le compte");

$societeintermediaire = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_intermediaire_societe')->getSociete();
$etablissementintermediaire = $societeintermediaire->createEtablissement(EtablissementFamilles::FAMILLE_REPRESENTANT);
$etablissementintermediaire->region = EtablissementClient::REGION_CVO;
$etablissementcourtier->nom = "Etablissement d'intermediaire de la région";
$etablissementintermediaire->save();
$id = $etablissementintermediaire->getSociete()->getidentifiant();
$compteintermediaire = CompteClient::getInstance()->findByIdentifiant($id."01");
$compteintermediaire->addTag('test', 'test');
$compteintermediaire->save();
$t->is($compteintermediaire->tags->automatique->toArray(true, false), array('etablissement', 'representant'), "Création d'un etablissement intermediaire met à jour le compte");

$societecoop = CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_cooperative_societe')->getSociete();
$etablissementcoop = $societecoop->createEtablissement(EtablissementFamilles::FAMILLE_COOPERATIVE);
$etablissementcoop->region = EtablissementClient::REGION_CVO;
$etablissementcoop->nom = "Etablissement coopérative de la région";
$etablissementcoop->save();
$id = $etablissementcoop->getSociete()->getidentifiant();
$comptecoop = CompteClient::getInstance()->findByIdentifiant($id."01");
$comptecoop->addTag('test', 'test');
$comptecoop->save();
$t->is($comptecoop->tags->automatique->toArray(true, false), array( 'etablissement','cooperative'), "Création d'un etablissement coop met à jour le compte");
