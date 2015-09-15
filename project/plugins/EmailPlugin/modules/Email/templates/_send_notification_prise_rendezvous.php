<?php use_helper('Date') ?>
Bonjour,

Le rendez-vous de <?php echo $rendezvous->raison_sociale ?> au <?php echo $rendezvous->adresse.' '.$rendezvous->code_postal.' '.$rendezvous->commune ?> a été pris pour le <?php echo $rendezvous->getDateHeureFr(); ?>.

Il n'est actuellement pas planifié dans la planification du <?php echo ucfirst(format_date($this->getDate(), "P", "fr_FR")); ?> .

Veuillez-vous rendre ici pour le planifier : <?php echo sfContext::getInstance()->getRouting()->generate('constats_planifications', array('date' => $this->getDate()), true); ?>

Bien Cordialement,

L'application de télédéclaration pour le service Appui technique de l'AVA.