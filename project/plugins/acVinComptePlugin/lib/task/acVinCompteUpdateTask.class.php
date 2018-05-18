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
 * acVinComptePlugin task.
 *
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Mathurin Petit <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acVinCompteUpdateTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'Document ID'),
    ));


    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declaration'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'compte';
    $this->name             = 'update';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $compte = CompteClient::getInstance()->find($arguments['doc_id']);
    $identifiant = $compte->identifiant;
    $etb = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiant);
    if($etb->compte != $compte->_id){
        throw new sfException("Le compte d'identifiant  $compte->_id n'est pas conforme avec l'identifiant de compte présent dans l'établissement $etb->compte");
    }
    $compte->add('origines',array($etb->_id));
    $compte->compte_type = 'ETABLISSEMENT';
    $compte->save();
    echo "SAVE DU COMPTE $compte->_id\n";
  }
}
