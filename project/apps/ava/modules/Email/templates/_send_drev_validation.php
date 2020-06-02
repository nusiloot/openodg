Bonjour,

Votre déclaration de Revendication <?php echo $drev->campagne; ?> a bien été validée et envoyée au service Appui technique de l'AVA.

Cette validation sera définitive lorsque votre déclaration aura été vérifiée et que les éventuelles pièces à joindre seront parvenues à notre service.
<?php if (count($drev->getOrAdd('documents')) > 0): ?>

Rappel des documents à envoyer :

<?php foreach($drev->getOrAdd('documents') as $document): ?>
- <?php echo DRevDocuments::getDocumentLibelle($document->getKey()) ?>

<?php endforeach; ?>

Le ou les document(s) annexes peuvent nous être envoyés par mail (<?php echo sfConfig::get('app_email_plugin_reply_to_adresse'); ?>) ou par voie postale :

Association des Viticulteurs d'Alsace
12 avenue de la Foire Aux Vins
BP 91 225
68012 Colmar Cedex
<?php endif; ?>

Vous pouvez à tout moment revenir sur votre compte pour consulter votre document : <?php echo sfContext::getInstance()->getRouting()->generate('drev_visualisation', $drev, true); ?>


Bien cordialement,

Le service Appui technique (via l'application de télédéclaration)
