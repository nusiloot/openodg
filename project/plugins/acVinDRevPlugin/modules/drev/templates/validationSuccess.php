<?php include_partial('drev/breadcrumb', array('drev' => $drev )); ?>
<?php include_partial('drev/step', array('step' => 'validation', 'drev' => $drev)) ?>

<div class="page-header no-border">
    <h2>Validation de votre déclaration</h2>
</div>

<form role="form" action="<?php echo url_for('drev_validation', $drev) ?>#engagements" method="post" id="validation-form">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php if(isset($form["date"])): ?>
    <div class="row">
        <div class="form-group <?php if ($form["date"]->hasError()): ?>has-error<?php endif; ?>">
            <?php if ($form["date"]->hasError()): ?>
                <div class="alert alert-danger" role="alert"><?php echo $form["date"]->getError(); ?></div>
            <?php endif; ?>
            <?php echo $form["date"]->renderLabel(null, array("class" => "col-xs-4 control-label")); ?>
            <div class="col-xs-4">
                <div class="input-group date-picker">
                    <?php echo $form["date"]->render(array("class" => "form-control")); ?>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($validation->hasPoints()): ?>
        <?php include_partial('drev/pointsAttentions', array('drev' => $drev, 'validation' => $validation)); ?>
    <?php endif; ?>
    <?php include_partial('drev/recap', array('drev' => $drev)); ?>
	<?php  if (!$drev->isPapier() && count($validation->getPoints(DrevValidation::TYPE_ENGAGEMENT)) > 0): ?>
    	<?php include_partial('drev/engagements', array('drev' => $drev, 'validation' => $validation, 'form' => $form)); ?>
    <?php endif; ?>

    <div style="padding-top: 10px;" class="row row-margin row-button">
        <div class="col-xs-4">
            <a href="<?php echo ($drev->isModificative())? url_for("drev_lots", $drev) : url_for("drev_revendication", array('sf_subject' => $drev, 'prec' => true)); ?>" class="btn btn-default btn-upper"><span class="glyphicon glyphicon-chevron-left"></span> Retourner à l'étape précédente</a>
        </div>
        <div class="col-xs-4 text-center">
            <a href="<?php echo url_for('drev_document_douanier_pdf', $drev); ?>" class="btn btn-default pull-left" >
                <span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;<?php echo $drev->getDocumentDouanierType() ?>
            </a>
            <a href="<?php echo url_for("drev_export_pdf", $drev) ?>" class="btn btn-primary">
                <span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Prévisualiser
            </a>
        </div>
        <div class="col-xs-4 text-right">
            <button type="submit" id="btn-validation-document" data-toggle="modal" data-target="#drev-confirmation-validation" <?php if($validation->hasErreurs() && $drev->isTeledeclare() && !$sf_user->hasDrevAdmin()): ?>disabled="disabled"<?php endif; ?> class="btn btn-success btn-upper"><span class="glyphicon glyphicon-check"></span>&nbsp;&nbsp;Valider la déclaration</button>
        </div>
    </div>
</form>
<?php include_partial('drev/popupConfirmationValidation'); ?>
