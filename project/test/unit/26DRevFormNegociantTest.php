<?php

require_once(dirname(__FILE__).'/../bootstrap/common.php');

sfContext::createInstance($configuration);

$t = new lime_test(4);

$viti =  CompteTagsView::getInstance()->findOneCompteByTag('test', 'test_viti')->getEtablissement();

//Suppression des DRev précédentes
foreach(DRevClient::getInstance()->getHistory($viti->identifiant, acCouchdbClient::HYDRATE_ON_DEMAND) as $k => $v) {
    DRevClient::getInstance()->deleteDoc(DRevClient::getInstance()->find($k, acCouchdbClient::HYDRATE_JSON));
    $dr = DRClient::getInstance()->find(str_replace("DREV-", "DR-", $k), acCouchdbClient::HYDRATE_JSON);
    if($dr) { DRClient::getInstance()->deleteDoc($dr); }
    $sv12 = SV12Client::getInstance()->find(str_replace("DREV-", "SV12-", $k), acCouchdbClient::HYDRATE_JSON);
    if($sv12) { SV12Client::getInstance()->deleteDoc($sv12); }
    $sv11 = SV11Client::getInstance()->find(str_replace("DREV-", "SV11-", $k), acCouchdbClient::HYDRATE_JSON);
    if($sv11) { SV11Client::getInstance()->deleteDoc($sv11); }
}

$campagne = (date('Y')-1)."";

$config = ConfigurationClient::getCurrent();
foreach($config->getProduits() as $produit) {
    if(!$produit->getRendement()) {
        continue;
    }
    break;
}

$csvContentTemplate = file_get_contents(dirname(__FILE__).'/../data/sv12_douane.csv');

$csvTmpFile = tempnam(sys_get_temp_dir(), 'openodg.').".csv";
file_put_contents(
    $csvTmpFile,
    str_replace(
        array("%code_inao%", "%libelle_produit%"),
        array($produit->getCodeDouane(), $produit->getLibelleComplet()),
        $csvContentTemplate
    )
);

$dr = SV12Client::getInstance()->createDoc($viti->identifiant, $campagne);
$dr->setLibelle("SV12 $campagne issue de Prodouane (Papier)");
$dr->setDateDepot("$campagne-12-15");
$dr->save();
$dr->storeFichier($csvTmpFile);
$dr->save();

$drev = DRevClient::getInstance()->createDoc($viti->identifiant, $campagne);
$drev->save();

$drev->importFromDocumentDouanier();
$drev->save();

$t->is(count($drev->getProduits()), 1, "La DRev a repris 1 produit du csv de la SV12");

$produits = $drev->getProduits();

$produit1 = current($produits);
$produit_hash1 = $produit1->getHash();
next($produits);
$produit2 = current($produits);
$produit_hash2 = $produit2->getHash();

$t->is($produit1->recolte->superficie_total, 0.4579, "La superficie de récolte totale est récupéré sur csv");
$t->is($produit1->recolte->volume_total, 19.42, "Le volume total est récupéré du csv");
$t->is($produit1->recolte->recolte_nette, 19.42, "Le volume de récolte net est récupéré du csv");
