<?php use_helper('Date') ?>

<?php include_partial('drev/breadcrumb', array('drev' => $drev )); ?>
<?php $hasValidationOdg = DrevConfiguration::getInstance()->hasValidationOdg(); ?>
<?php $isValidateOdgRegion = ($regionParam)? $drev->isValidateOdgByRegion($regionParam) : null; ?>
<?php if (isset($form)): ?>
    <form action="<?php echo url_for('drev_visualisation', $drev) ?>" method="post">
        <?php echo $form->renderHiddenFields(); ?>
        <?php echo $form->renderGlobalErrors(); ?>
<?php endif; ?>

<div class="page-header no-border">
    <h2>Déclaration de Revendication <?php echo $drev->campagne ?>
    <?php if($drev->isPapier()): ?>
    <small class="pull-right"><span class="glyphicon glyphicon-file"></span> Déclaration papier<?php if($drev->validation && $drev->validation !== true): ?> reçue le <?php echo format_date($drev->validation, "dd/MM/yyyy", "fr_FR"); ?><?php endif; ?>
      <?php if($drev->isSauvegarde()): ?> <span class="text-danger">Non facturé</span><?php endif; ?>
    <?php elseif($drev->validation): ?>
    <small class="pull-right">Télédéclaration<?php if($drev->validation && $drev->validation !== true): ?> validée le <?php echo format_date($drev->validation, "dd/MM/yyyy", "fr_FR"); ?><?php endif; ?>
      <?php if($drev->isSauvegarde()): ?> <span class="text-danger">Non facturable</span><?php endif; ?> <?php if(!$drev->isNonFactures()): ?><span class="btn btn-default-step btn-xs">Facturé</span><?php endif; ?>
    <?php endif; ?>
    <?php if ($sf_user->isAdmin() && $drev->exist('envoi_oi') && $drev->envoi_oi) { echo ", envoyée à l'OI le ".format_date($drev->envoi_oi, 'dd/MM/yyyy') ; } ?>
    <?php if ($sf_user->isAdmin() && $drev->validation_odg): ?><a href="<?php echo url_for('drev_send_oi', $drev); echo ($regionParam)? '?region='.$regionParam : ''; ?>" onclick="return confirm('Êtes vous sûr de vouloir envoyer la DRev à l\'OI ?');"  class="btn btn-default btn-xs btn-warning"><span class="glyphicon glyphicon-copy"></span> Envoyer à l'OI</a><?php endif; ?>
  </small>
    </h2>
</div>

<?php if ($sf_user->hasFlash('notice')): ?>
    <div class="alert alert-success" role="alert"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif; ?>

<?php if(!$drev->validation): ?>
<div class="alert alert-warning">
    La saisie de cette déclaration n'est pas terminée elle est en cours d'édition
</div>
<?php endif; ?>

<?php if(!$drev->isMaster()): ?>
    <div class="alert alert-info">
      Ce n'est pas la <a class="" href="<?php echo ($drev->getMaster()->isValidee())? url_for('drev_visualisation', $drev->getMaster()) :  url_for('drev_edit', $drev->getMaster()) ?>"><strong>dernière version</strong></a> de la déclaration, le tableau récapitulatif n'est donc pas à jour.

    </div>
<?php endif; ?>

<?php if($drev->validation && !$drev->validation_odg && $sf_user->isAdmin()): ?>
    <div class="alert alert-warning">
        Cette déclaration est en <strong>attente de validation</strong> par l'ODG
    </div>
<?php endif; ?>

<?php if(isset($validation) && $validation->hasPoints()): ?>
    <?php include_partial('drev/pointsAttentions', array('drev' => $drev, 'validation' => $validation, 'noLink' => true)); ?>
<?php endif; ?>

<?php include_partial('drev/recap', array('drev' => $drev, 'form' => $form)); ?>

<?php //include_partial('drev/documents', array('drev' => $drev, 'form' => isset($form) ? $form : null)); ?>

<div class="row row-margin row-button">
    <div class="col-xs-4">
        <a href="<?php if(isset($service)): ?><?php echo $service ?><?php else: ?><?php echo url_for("declaration_etablissement", array('identifiant' => $drev->identifiant, 'campagne' => $drev->campagne)); ?><?php endif; ?>" class="btn btn-default btn-upper"><span class="glyphicon glyphicon-chevron-left"></span> Retour</a>
    </div>
    <div class="col-xs-4 text-center">
      <a href="<?php echo url_for('drev_document_douanier_pdf', $drev); ?>" class="btn btn-default pull-left" >
          <span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;<?php echo $drev->getDocumentDouanierType() ?>
      </a>

            <a href="<?php echo url_for("drev_export_pdf", $drev) ?>" class="btn btn-default">
                <span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Visualiser
            </a>
    </div>

    <div class="col-xs-2 text-right">
        <?php if ($drev->validation && DRevSecurity::getInstance($sf_user, $drev->getRawValue())->isAuthorized(DRevSecurity::DEVALIDATION) && !$drev->isFactures()): ?>
                    <a class="btn btn-xs btn-default pull-right" href="<?php echo url_for('drev_devalidation', $drev) ?>" onclick="return confirm('Êtes-vous sûr de vouloir dévalider cette DRev ?');"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;&nbsp;Dévalider</a>
        <?php elseif ($drev->validation && $sf_user->isAdmin() && !$drev->isLectureSeule() && !$drev->isFactures()): ?>
                  <a class="btn btn-xs btn-default-step pull-right hidden-xs" onClick="return confirm('Attention, cette DRev a sans doute été facturée. Si vous changez un volume, pensez à en faire part au service comptable !!');" href="<?php echo url_for('drev_devalidation', $drev) ?>"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;&nbsp;Réouvrir</a>
        <?php elseif ($drev->isFactures()): ?>
                  <span class="text-mutted">DRev facturée.</span>
        <?php endif; ?>
        <?php if(!$drev->validation): ?>
                <a href="<?php echo url_for("drev_edit", $drev) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;Continuer la saisie</a>
        <?php elseif(!$drev->validation_odg && ($sf_user->isAdmin() || $sf_user->hasDrevAdmin()) && $hasValidationOdg && !$isValidateOdgRegion): ?>
        <?php $params = array("sf_subject" => $drev, "service" => isset($service) ? $service : null); if($regionParam): $params=array_merge($params,array('region' => $regionParam)); endif; ?>
                <a onclick='return confirm("Êtes vous sûr de vouloir approuver cette déclaration ?");' href="<?php echo url_for("drev_validation_admin", $params) ?>" class="btn btn-success btn-upper"><span class="glyphicon glyphicon-ok-sign"></span>&nbsp;&nbsp;Approuver</a>
        <?php endif; ?>

<?php if (isset($form)): ?>
</form>
<?php endif; ?>
