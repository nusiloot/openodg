<?php use_helper('Date'); ?>
<div class="col-sm-6 col-md-4 col-xs-12">
    <div class="block_declaration panel <?php if ($intentionParcellaireAffectation): ?>panel-success<?php else: ?>panel-primary<?php endif; ?>">
        <div class="panel-heading">
            <h3 class="panel-title">Intention d'affectation parcellaire</h3>
        </div>
          <div class="panel-body">
			  <p><?php if ($intentionParcellaireAffectation): ?>Mettre à jour<?php else: ?>Saisir<?php endif; ?> les intentions d'affectation parcellaire</p>
              <div style="margin: 50px 0 29px 0;">
              	<a class="btn btn-default btn-block" href="<?php echo url_for('parcellaireintentionaffectation_edit', array('sf_subject' => $etablissement, 'campagne' => $campagne)) ?>"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;<?php if ($intentionParcellaireAffectation): ?>Mettre à jour<?php else: ?>Saisir<?php endif; ?> la déclaration</a>
              </div>
          </div>
    </div>
</div>
            