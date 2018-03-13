<?php

class exportEtablissementsCsvTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'etablissements-csv';
        $this->briefDescription = "Export csv des établissements";
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $results = EtablissementClient::getInstance()->findAll();

        echo "Identifiant;Famille;Inititule;Raison sociale;Adresse;Adresse complémentaire;Code postal;Commune;CVI;SIRET;Téléphone bureau;Téléphone mobile;Téléphone perso;Fax;Email;Habilitation Activités;Habilitation Statut;Ordre;Région;Code comptable;Statut;Date de dernière modification;Commentaire;\n";

        foreach($results->rows as $row) {
            $etablissement = EtablissementClient::getInstance()->find($row->id, acCouchdbClient::HYDRATE_JSON);
            $societe = SocieteClient::getInstance()->find($etablissement->id_societe, acCouchdbClient::HYDRATE_JSON);
            $compte = CompteClient::getInstance()->find($etablissement->compte, acCouchdbClient::HYDRATE_JSON);
            $habilitation = HabilitationClient::getInstance()->getLastHabilitation($etablissement->identifiant, acCouchdbClient::HYDRATE_JSON);

            $habilitationStatut = null;
            $activites = array();
            if(isset($habilitation)) {
                foreach($habilitation->declaration as $produit) {
                    foreach($produit->activites as $activiteKey => $activite) {
                        if(!$activite->statut) {
                            continue;
                        }
                        $activites[] = HabilitationClient::getInstance()->getLibelleActivite($activiteKey);
                        $habilitationStatut = HabilitationClient::getInstance()->getLibelleStatut($activite->statut);
                    }
                }
            }

            sort($activites);

            $ordre = null;

            if($etablissement->region && $etablissement->famille == EtablissementFamilles::FAMILLE_PRODUCTEUR) {
                $ordre = 'CP ';
            }
            if($etablissement->region && $etablissement->famille == EtablissementFamilles::FAMILLE_COOPERATIVE) {
                $ordre = 'CC ';
            }
            if($etablissement->region && $etablissement->famille == EtablissementFamilles::FAMILLE_NEGOCIANT) {
                $ordre = 'N';
            }
            if($etablissement->region) {
                $ordre .= substr($etablissement->code_postal, 0, 2);
            }

            $intitules = "EARL|EI|ETS|EURL|GAEC|GFA|HOIRIE|IND|M|MM|Mme|MME|MR|SA|SARL|SAS|SASU|SC|SCA|SCE|SCEA|SCEV|SCI|SCV|SFF|SICA|SNC|SPH|STE|STEF";
            $intitule = null;
            $raisonSociale = $etablissement->raison_sociale;

            if(preg_match("/^(".$intitules.") /", $raisonSociale, $matches)) {
                $intitule = $matches[1];
                $raisonSociale = preg_replace("/^".$intitule." /", "", $raisonSociale);
            }

            if(preg_match("/ \((".$intitules.")\)$/", $raisonSociale, $matches)) {
                $intitule = $matches[1];
                $raisonSociale = preg_replace("/ \((".$intitule.")\)$/", "", $raisonSociale);
            }

            echo
            $societe->identifiant.";".
            $etablissement->famille.";".
            $intitule.";".
            $raisonSociale.";".
            str_replace('"', '', $etablissement->adresse).";".
            $etablissement->adresse_complementaire.";".
            $etablissement->code_postal.";".
            $etablissement->commune.";".
            $etablissement->cvi.";".
            $etablissement->siret.";".
            $etablissement->telephone_bureau.";".
            $etablissement->telephone_mobile.";".
            $etablissement->telephone_perso.";".
            $etablissement->fax.";".
            $etablissement->email.";".
            implode("|", $activites).";". // Activité habilitation
            $habilitationStatut.";". // Statut habilitation
            $ordre.";". // Ordre
            $etablissement->region.";".
            $societe->code_comptable_client.";".
            $etablissement->statut.";".
            $compte->date_modification.";".
            str_replace("\n", '\n', $etablissement->commentaire).
            "\n";

        }
    }
}
