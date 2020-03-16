<?php

class ParcellaireIntentionAffectationImportTask extends sfBaseTask
{
    public $combinaisons;
    public $currentCombinaison;

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('csv', sfCommandArgument::REQUIRED, "CSV intentions dpap"),
            new sfCommandArgument('date', sfCommandArgument::REQUIRED, "date intention dpap"),
        ));
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declaration'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));
        $this->namespace = 'intention-dpap';
        $this->name = 'import';
        $this->briefDescription = "Import de l'intention dpap";
        $this->detailedDescription = "";
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        if(!file_exists($arguments['csv'])) {
            echo sprintf("ERROR;Le fichier CSV n'existe pas;%s\n", $arguments['csv']);
            return;
        }
        if (!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $arguments['date'], $m)) {
            echo sprintf("ERROR;Le format date n'est pas valide (yyyy-mm-dd);%s\n", $arguments['date']);
            return;
        }
        $campagne = $m[1];
        $csvFile = new CsvFile($arguments['csv']);
        $csv = $csvFile->getCsv();
        $index = 0;
        foreach($csv as $ligne => $data) {
            $identifiant = $data[0];
            $idu = $data[1];
            $surface = round($this->formatFloat($data[2]),4);
            $cepage = $data[3];
            $dgc = $data[4];
            $etablissement = EtablissementClient::getInstance()->findByIdentifiant($identifiant);
            if (!$etablissement) {
                echo sprintf("ERROR;Etablissement non trouvé;%s\n", implode(';', $data));
                continue;
            }
            $intentionDpap = ParcellaireIntentionAffectationClient::getInstance()->createDoc($identifiant, $campagne, 1, $arguments['date']);
            if (!$intentionDpap->hasParcellaire()) {
                echo sprintf("ERROR;Pas de parcellaire;%s\n", implode(';', $data));
                continue;
            }
            $parcelles = $intentionDpap->getParcelles(); 
            /*
             * AFFECTATION PARFAITE : IDU + CEPAGE + SUPERFICIE
             */
            $find = false;
            foreach ($parcelles as $parcelle) {
                if ($parcelle->idu == $idu && $parcelle->cepage == $cepage && round($parcelle->superficie,4) == $surface) {
                    $parcelle->affectation = 1;
                    $parcelle->date_affectation = $arguments['date'];
                    $parcelle->superficie_affectation = $surface;
                    $find = true;
                    echo sprintf("SUCCESS;IDU+CEP+SUPERFICIE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, $parcelle->getHash());
                    break;
                }
            }
            if ($find) { $intentionDpap->save(); continue; }
            /*
             * AFFECTATION : IDU + SUPERFICIE (- CEPAGE)
             */
            foreach ($parcelles as $parcelle) {
                if ($parcelle->idu == $idu && round($parcelle->superficie,4) == $surface) {
                    $parcelle->affectation = 1;
                    $parcelle->date_affectation = $arguments['date'];
                    $parcelle->superficie_affectation = $surface;
                    $find = true;
                    echo sprintf("SUCCESS;IDU+SUPERFICIE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, $parcelle->getHash());
                    break;
                }
            }
            if ($find) { $intentionDpap->save(); continue; }
            /*
             * AFFECTATION IDU et/ou CEPAGE : 1 PARCELLE
             */
            $foundIduCep = array();
            $foundIdu = array();
            foreach ($parcelles as $parcelle) {
                if ($parcelle->idu == $idu && $parcelle->cepage == $cepage) {
                    $foundIduCep[] = $parcelle;
                } elseif ($parcelle->idu == $idu) {
                    $foundIdu[] = $parcelle;
                }
            }
            if (count($foundIduCep) == 1) {
                $parcelle = current($foundIduCep);
                $parcelle->affectation = 1;
                $parcelle->date_affectation = $arguments['date'];
                $parcelle->superficie_affectation = ($surface >= round($parcelle->superficie,4))? round($parcelle->superficie,4) : $surface ;
                $find = true;
                echo sprintf("SUCCESS;IDU + CEPAGE UNE PARCELLE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, $parcelle->getHash());
            }
            if ($find) { $intentionDpap->save(); continue; }
            if (count($foundIdu) == 1) {
                $parcelle = current($foundIdu);
                $parcelle->affectation = 1;
                $parcelle->date_affectation = $arguments['date'];
                $parcelle->superficie_affectation = ($surface >= round($parcelle->superficie,4))? round($parcelle->superficie,4) : $surface ;
                $find = true;
                echo sprintf("SUCCESS;IDU UNE PARCELLE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, $parcelle->getHash());
            }
            if ($find) { $intentionDpap->save(); continue; }
            /*
             * AFFECTATION : COMBINAISON IDU+CEPAGE
             */
            $index = 0;
            $this->combinaisons = array();
            foreach ($foundIduCep as $parcelle) {
                if ($find = $this->looping(array($parcelle), $foundIduCep, $index, $surface)) {
                    break;
                }
                $index++;
            }
            if ($find) {
                $parcellesHash = $this->affecteParcelles($this->currentCombinaison, $arguments['date']);
                echo sprintf("SUCCESS;COMBINAISON IDU+CEPAGE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, implode(",", $parcellesHash));
                // on squeeze la meilleure combinaison car tres peu de cas
            } elseif (1==2) {
                ksort($this->combinaisons);
                $combinaison = current($this->combinaisons);
                $totalSuperficie = $this->getSurface($combinaison);
                $diff = abs(round((($surface-$totalSuperficie)/$totalSuperficie)*100));
                if ($totalSuperficie >= $surface) {
                    usort($combinaison, array("ParcellaireIntentionAffectationImportTask", "sorting"));
                    $parcellesHash = array();
                    $superficie = 0;
                    foreach ($combinaison as $parcelle) {
                        $tmp = round($superficie+$parcelle->superficie,4);
                        $parcelle->affectation = 1;
                        $parcelle->date_affectation = $date;
                        $parcelle->superficie_affectation = ($tmp > $surface)? round($surface - $superficie,4) : round($parcelle->superficie,4);
                        $parcellesHash[] = $parcelle->getHash();
                        if (($tmp > $surface)) {
                            break;
                        }
                        $superficie = round($superficie+$parcelle->superficie,4);
                    }
                    $find = true;
                    echo sprintf("SUCCESS;MEILLEURE COMBINAISON IDU+CEPAGE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, implode(",", $parcellesHash));
                } elseif ($diff <= 10) {
                    $parcellesHash = $this->affecteParcelles($combinaison, $arguments['date']);
                    $find = true;
                    echo sprintf("SUCCESS;MEILLEURE COMBINAISON IDU+CEPAGE;%s;%s;%s\n", implode(';', $data), $intentionDpap->_id, implode(",", $parcellesHash));
                }
                
            }
            if ($find) { $intentionDpap->save(); continue; }
            $findIdu = false;
            $findIduCep = false;
            foreach ($parcelles as $parcelle) {
                if ($parcelle->idu == $idu) {
                    $findIdu = true;
                }
                if ($parcelle->idu == $idu && $parcelle->cepage == $cepage) {
                    $findIduCep = true;
                }
            }
            if ($findIduCep && $findIdu) {
                echo sprintf("ERROR;PARCELLE+CEP+SUPERCIFIE NON IDENTIFIEE;%s\n", implode(';', $data));
            }
            elseif (!$findIduCep && $findIdu) {
                echo sprintf("ERROR;PARCELLE+CEP NON IDENTIFIEE;%s\n", implode(';', $data));
            }
            else {
                echo sprintf("ERROR;PARCELLE NON IDENTIFIEE;%s\n", implode(';', $data));
            }
        }
    }
    /**
    * Recursive function 
    **/
    protected function looping($combinaisons, $parcelles, $index, $surface) {
        $combinaisonsSurface = $this->getSurface($combinaisons);
        $diff = abs(round((($surface-$combinaisonsSurface)/$combinaisonsSurface)*100));
        $this->combinaisons[$diff] = $combinaisons;
        $this->currentCombinaison = $combinaisons;
        if (round($combinaisonsSurface/$surface*100,1) >= 99.0 && round($combinaisonsSurface/$surface*100,1) <= 101.0) {
            return true;
        }
        if (round($combinaisonsSurface*10,4) == $surface || round($surface*10,4) == $combinaisonsSurface) {
            return true;
        }
        $nbParcelle = count($parcelles);
        for($i = ($index+1); $i < $nbParcelle; $i++) {
            $combinaisons[] = $parcelles[$i];
            if ($this->looping($combinaisons, $parcelles, $i, $surface)) {
                return true;
            }
        }
        return false;
    }
    
    protected function affecteParcelles(&$parcelles, $date) {
        $parcellesHash = array();
        foreach (parcelles as $parcelle) {
            $parcelle->affectation = 1;
            $parcelle->date_affectation = $date;
            $parcelle->superficie_affectation = round($parcelle->superficie,4);
            $parcellesHash[] = $parcelle->getHash();
        }
        return $parcellesHash;
    }
    
    protected function getSurface($combinaisons) {
        $sum = 0;
        foreach ($combinaisons as $parcelle) {
            $sum += $parcelle->superficie;
        }
        return round($sum, 4);
    }
    
    protected function formatFloat($value) {

        return str_replace(',', '.', $value)*1.0;
    }
    
    protected static function sorting($a, $b) {
        $al = round($a->superficie,4);
        $bl = round($b->superficie,4);
        if ($al == $bl) {
            return 0;
        }
        return ($al < $bl) ? +1 : -1;
    }
}