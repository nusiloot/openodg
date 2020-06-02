<ol class="breadcrumb">
    <?php if(!$sf_user->isAdmin()): ?>
        <li><a href="<?php echo url_for('accueil'); ?>">Accueil</a></li>
    <?php endif; ?>
  <li><a href="<?php if($sf_user->isAdmin()): ?><?php echo url_for('facturation'); ?><?php else: ?><?php echo url_for('facturation_declarant', $compte); ?><?php endif; ?>">Facturation</a></li>
  <li class="active"><a href=""><?php echo $compte->getNomAAfficher() ?> (<?php echo $compte->getIdentifiantAAfficher() ?>)</a></li>
</ol>

<?php use_helper('Date'); ?>
<?php use_helper('Float'); ?>
<?php use_helper('Generation'); ?>

<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="alert alert-success" role="alert"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif; ?>

<?php if ($sf_user->hasFlash('error')): ?>
    <div class="alert alert-danger" role="alert"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif; ?>


<div class="page-header">
    <h2>Espace Facture</h2>
</div>

<h3>Liste des factures</h3>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-xs-1">Date</th>
            <th class="col-xs-1">Numéro</th>
            <th class="col-xs-2">Type</th>
            <th class="col-xs-4">Libellé</th>
            <th class="col-xs-2 text-right">Montant TTC Facture</th>
            <th class="col-xs-2 text-right">Montant payé</th>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <th style="witdth: 0;"></th>
            <?php endif; ?>
            <th style="witdth: 0;"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($factures as $facture) : ?>
        <tr>
            <td><?php echo format_date($facture->date_facturation, "dd/MM/yyyy", "fr_FR"); ?></td>
            <td>N°&nbsp;<?php echo $facture->numero_archive ?></td>
            <td><?php if($facture->isAvoir()): ?>AVOIR<?php else: ?>FACTURE<?php endif; ?></td>
            <td><?php if(!$facture->isAvoir()): ?><?php echo $facture->getTemplate()->libelle ?><?php endif; ?></td>
            <td class="text-right"><?php echo Anonymization::hideIfNeeded(echoFloat($facture->total_ttc)); ?>&nbsp;€</td>
            <td class="text-right"><?php echo echoFloat($facture->getMontantPaiement()); ?>&nbsp;€</td>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <td class="text-center dropdown">
              <button type="button" class="btn btn-default btn-default-step btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span>&nbsp;<span class="caret"></span></button>
              <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                  <?php if(!$facture->isAvoir() && !$facture->versement_comptable_paiement && !$facture->exist('avoir')): ?>
                    <li>
                      <a href="<?php echo url_for("facturation_avoir_defacturant", array("id" => $facture->_id)) ?>" >
                          <span class="glyphicon glyphicon-repeat"></span> Créér un avoir
                    </a>
                  </li>
                  <?php else: ?>
                    <li  class="disabled"><a href=""><span class="glyphicon glyphicon-repeat"></span> Créér un avoir</a></li>
                  <?php endif; ?>

                  <?php if(!$facture->isAvoir() && !$facture->versement_comptable_paiement): ?>
                    <li><a href="<?php echo url_for("facturation_paiements", array("id" => $facture->_id)) ?>">Saisir / modifier les paiements</a></li>
                  <?php else: ?>
                    <li class="disabled"><a href="">Saisir / modifier les paiements</a></li>
                  <?php endif; ?>

              </ul>
            </td>
           <?php endif; ?>
            <td class="text-right">
                <a href="<?php echo url_for("facturation_pdf", array("id" => $facture->_id)) ?>" class="btn btn-sm btn-default-step"><span class="glyphicon glyphicon-file"></span>&nbsp;Visualiser</a>
            </td>
        </tr>
        <?php endforeach;
          if(!count($factures)):
        ?>
        <tr>
            <td colspan="<?php echo intval($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN))+6 ?>">Aucune factures éditées</td>
        </tr>
      <?php endif; ?>
    </tbody>
</table>

<hr />

<?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
<h3>Génération de facture</h3>
<form method="post" action="" role="form" class="form-horizontal">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>
    <div class="row">
        <div class="col-sm-8 col-xs-12">
          <?php if(isset($form["modele"])): ?>
            <div class="form-group <?php if($form["modele"]->hasError()): ?>has-error<?php endif; ?>">
                <?php echo $form["modele"]->renderError() ?>
                <?php echo $form["modele"]->renderLabel("Type de facture", array("class" => "col-xs-4 control-label")); ?>
                <div class="col-xs-8">
                <?php echo $form["modele"]->render(array("class" => "form-control")); ?>
                </div>
            </div>
          <?php endif; ?>
            <div class="form-group <?php if($form["date_facturation"]->hasError()): ?>has-error<?php endif; ?>">
                <?php echo $form["date_facturation"]->renderError(); ?>
                <?php echo $form["date_facturation"]->renderLabel("Date de facturation", array("class" => "col-xs-4 control-label")); ?>
                <div class="col-xs-8">
                    <div class="input-group date-picker-week">
                        <?php echo $form["date_facturation"]->render(array("class" => "form-control", "placeholder" => "Date de facturation")); ?>
                        <div class="input-group-addon">
                            <span class="glyphicon-calendar glyphicon"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group text-right">
                <div class="col-xs-6 col-xs-offset-6">
                    <button class="btn btn-default btn-block btn-upper" type="submit">Générer la facture</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<?php if(count($mouvements) && $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
  <h3>Mouvements en attente de facturation</h3>
  <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="col-xs-1">Document</th>
            <th class="col-xs-1">Campagne</th>
            <th class="col-xs-4">Cotisation</th>
            <th class="col-xs-1">Quantite</th>
            <th class="col-xs-1">Prix unit.</th>
            <th class="col-xs-1">Tva</th>
            <th class="col-xs-2">Montant HT</th>
        </tr>
    </thead>
    <tbody>

  <?php foreach ($mouvements as $keyMvt => $mvt): ?>
    <tr>
        <td><?php echo $mvt->getDocument()->getType();?></td>
        <td><?php echo format_date($mvt->date, "dd/MM/yyyy", "fr_FR"); ?></td>
        <td><?php echo ucfirst($mvt->categorie); ?> <?php echo $mvt->type_libelle; ?></td>
        <td class="text-right"><?php echo echoFloat($mvt->quantite); ?></td>
        <td class="text-right"><?php echo echoFloat($mvt->taux); ?></td>
        <td class="text-right"><?php echo echoFloat($mvt->tva); ?>&nbsp;%</td>
        <td class="text-right"><?php echo echoFloat($mvt->taux * $mvt->quantite); ?>&nbsp;€</td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
