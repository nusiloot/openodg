<?php

require_once(dirname(__FILE__).'/../bootstrap/common.php');

$t = new lime_test(19);

$viti =  EtablissementClient::getInstance()->find('ETABLISSEMENT-7523700100');

//Suppression des DRev précédentes
foreach(DRevClient::getInstance()->getHistory($viti->identifiant, acCouchdbClient::HYDRATE_ON_DEMAND) as $k => $v) {
    $drev = DRevClient::getInstance()->find($k);
    $drev->delete(false);
}

$campagne = (date('Y')-1)."";
$templateFacture = TemplateFactureClient::getInstance()->find("TEMPLATE-FACTURE-AOC-".$campagne);

//Début des tests
$t->comment("Création d'une DRev");

$drev = DRevClient::getInstance()->createDoc($viti->identifiant, $campagne);
$drev->save();

$t->is($drev->identifiant, $viti->identifiant, "L'identifiant est celui du viti : ".$viti->identifiant);
$t->is($drev->campagne, $campagne, "La campagne est ".$campagne);

$drev->storeDeclarant();
$drev->save();

$t->is($drev->declarant->raison_sociale, $viti->raison_sociale, "La raison sociale est celle du viti : ".$viti->raison_sociale);

$t->comment("Saisie des volumes");

$produits = $drev->getConfigProduits();
foreach($produits as $produit) {
    $produit_hash1 = $produit->getHash();
    break;
}
foreach($produits as $produit) {
    $produit_hash2 = $produit->getHash();
}

$produit1 = $drev->addProduit($produit_hash1);
$produit2 = $drev->addProduit($produit_hash2);

$drev->save();

$drev->declaration->reorderByConf();

$drev->save();

$t->is($drev->exist($produit_hash1), true, "La produit ajouté existe");
$t->is($drev->get($produit_hash1)->getHash(), $produit_hash1, "La produit ajouté est récupérable");
$produit1 = $drev->get($produit_hash1);
$produit2 = $drev->get($produit_hash2);

$produit1->superficie_vinifiee = 100;
$produit1->superficie_revendique = 200;
$produit1->volume_revendique = 80;

$produit2->superficie_vinifiee = 150;
$produit2->superficie_revendique = 150;
$produit2->volume_revendique = 110;

$drev->save();

$t->is(count($drev->getProduits()), 2, "La drev a 2 produits");
$t->is($drev->declaration->getTotalTotalSuperficie(), 350, "La supeficie revendiqué totale est 350");
$t->is($drev->declaration->getTotalSuperficieVinifiee(), 2.5, "La superficie vinifié totale est 250");
$t->is($drev->declaration->getTotalVolumeRevendique(), 190, "Le volume revendiqué totale est 190");

$t->comment("Validation");

$drev->validate();
$drev->save();

$compteIdentifiant = $viti->getCompte()->identifiant;

$t->is($drev->validation, date('Y-m-d'), "La DRev a la date du jour comme date de validation");
$t->is(count($drev->mouvements->get($compteIdentifiant)), 1, "La DRev a 1 mouvement");

$mouvement = $drev->mouvements->get($compteIdentifiant)->getFirst();

$t->is($mouvement->getKey(), $templateFacture->_id, "La clé du mouvement est ".$templateFacture->_id);
$t->ok($mouvement->facture === 0, "Le mouvement est non facture");
$t->ok($mouvement->facturable === 1, "Le mouvement est facturable");

$t->comment("Génération d'une modificatrice");

$drevM1 = $drev->generateModificative();
$drevM1->save();

$t->is($drevM1->_id, $drev->_id."-M01", "L'id de la drev est ".$drev->_id."-M01");
$t->ok($drevM1->validation === null && $drevM1->validation_odg === null, "La drev modificatrice est dévalidée");

$drevMaster = DRevClient::getInstance()->findMasterByIdentifiantAndCampagne($viti->identifiant, $campagne);
$t->is($drevM1->_id, $drevMaster->_id, "La récupération de la drev master renvoi la drev ".$drevM1->_id);

$produit1M1 = $drevM1->get($produit1->getHash());
$produit1M1->superficie_vinifiee = 120;

$t->ok($drevM1->isModifiedMother($produit1->getHash(), 'superficie_vinifiee'), "La superficie vinifiee est marqué comme modifié par rapport à la précedente");

$t->comment("Validation de la modificatrice");

$drevM1->validate();
$drevM1->save();

$t->is(count($drevM1->mouvements->get($compteIdentifiant)), 1, "La DRev modificatrice a 1 un seul mouvement");

$mouvementM1 = $drevM1->mouvements->get($compteIdentifiant)->getFirst();
