<?php

class DRevUpdateRevendiqueVCITask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, "Document DRev ID"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declaration'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'drev';
        $this->name = 'update-revendique-vci';
        $this->briefDescription = 'Mise à jour du volume revendiqué VCI à partir de la DRev';
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $drev = DRevClient::getInstance()->find($arguments['doc_id']);

        $jsonOriginal = new acCouchdbJsonNative($drev->getData());

        if(!$drev) {

            echo sprintf("ERROR;DRev introuvable %s\n", $arguments['doc_id']);
            return;
        }

        if(!$drev->validation) {

            //return;
        }

        $drev->calculateVolumeRevendiqueVCI();
        if($drev->validation) {
            $drev->cleanDoc();
        }
        $jsonFinal = new acCouchdbJsonNative($drev->getData());
        if($jsonOriginal->equal($jsonFinal)) {
            return;
        }

        echo $drev->_id.";".$drev->declaration->getTotalSuperficieVinifiee()." ares".";".(($drev->isNonRecoltant()) ? "non_recoltant" : "")."\n";
        //print_r($jsonOriginal->diff($jsonFinal));
        //print_r($jsonFinal->diff($jsonOriginal));
    }
}
