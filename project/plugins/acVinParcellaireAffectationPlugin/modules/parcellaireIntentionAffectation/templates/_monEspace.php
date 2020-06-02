<?php use_helper('Date'); ?>
<div class="col-sm-6 col-md-4 col-xs-12">
    <div class="block_declaration panel <?php if ($intentionParcellaireAffectation): ?>panel-success<?php else: ?>panel-primary<?php endif; ?>">
        <div class="panel-heading">
            <h3 class="panel-title">Identification parcellaire</h3>
        </div>
          <div class="panel-body">
			  <p><?php if ($intentionParcellaireAffectation): ?>Mettre à jour<?php else: ?>Saisir<?php endif; ?> l'identification parcellaire<br />&nbsp;</p>
              <div style="margin: 50px 0 29px 0;">
              	<a class="btn btn-default btn-block" href="<?php echo url_for('parcellaireintentionaffectation_edit', array('sf_subject' => $etablissement, 'campagne' => $campagne)) ?>"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;<?php if ($intentionParcellaireAffectation): ?>Mettre à jour<?php else: ?>Saisir<?php endif; ?> l'identification</a>
              </div>
          </div>
    </div>
</div>
            