<?php use_helper('Date'); ?>
<?php use_javascript("organisation.js?201510010024", "last") ?>
<?php use_javascript('lib/leaflet/leaflet.js'); ?>
<?php use_javascript('lib/leaflet/marker.js'); ?>
<?php use_stylesheet('/js/lib/leaflet/leaflet.css'); ?>
<?php use_stylesheet('/js/lib/leaflet/marker.css'); ?>

<ol class="breadcrumb">
  <li><a href="<?php echo url_for('constats',array('jour' => date('Y-m-d'))); ?>">Constats VT-SGN</a></li>
  <li><a href="<?php echo url_for('constats_planification_jour', array('jour' => $jour)); ?>">Tournées du <?php echo ucfirst(format_date($jour, "P", "fr_FR")); ?></a></li>
  <li class="active"><a href="">Planification</a></li>
</ol>

<div class="page-header">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-1 text-left">
                    <h2><a class="text-muted ajax" href="<?php echo url_for('constats_planifications', array('date' => RendezvousClient::getPreviousDate($jour))); ?>">
                            <span class="glyphicon glyphicon-arrow-left"></span>
                        </a></h2>
                </div>
                <div class="col-xs-8 text-center">
                    <h2><?php echo ucfirst(format_date($jour, "P", "fr_FR")); ?></h2>
                </div>
                <div class="col-xs-1 text-right">
                    <h2><a class="text-muted ajax" href="<?php echo url_for('constats_planifications', array('date' => RendezvousClient::getNextDate($jour))); ?>">
                            <span class="glyphicon glyphicon-arrow-right"></span>
                        </a></h2>
                </div>
                <div class="col-xs-2 text-right" style="margin-top: 12px;">
                    <a href="<?php echo url_for('constats_planification_jour', array('jour' => $jour)); ?>" class="btn btn-upper btn-default btn-default-step ajax" ><span class="glyphicon glyphicon-list-alt"></span></a>

                </div>
            </div>
        </div>
    </div>
</div>

<form id="form_planification" action="<?php echo url_for('constats_planifications', array('date' => $jour)) ?>" method="post" class="form-horizontal ajaxForm">

    <div class="row">
        <div class="col-xs-12">
            <div class="btn-group">
                <btn class="active organisation-tournee btn btn-lg btn-default-step ajax" href="">Tous</btn>
                <?php foreach ($tournees as $t): ?>
                    <btn style="color: <?php echo $tourneesCouleur[$t->_id] ?>;" data-per-hour="4" data-hour="09:00" data-color="<?php echo $tourneesCouleur[$t->_id] ?>" id="<?php echo $t->_id ?>" class="organisation-tournee btn btn-lg btn-default-step ajax"><?php echo $t->getFirstAgent()->nom ?></btn>
                <?php endforeach; ?>
                <a href="<?php echo url_for('constats_planification_ajout_agent', array('jour' => $jour, 'retour' => 'planification')) ?>" class="btn btn-lg btn-default btn-default-step ajax"><span class="glyphicon glyphicon-plus"></span> Agent</a>
            </div>


        </div>
    </div>

    <div class="row row-margin">
        <div class="col-xs-6">
            <div class="well" style="padding: 0 5px; margin-bottom: 5px;">
                <h4 class="text-center" style="text-transform: uppercase;"><span class="glyphicon glyphicon-time"></span> En attente de planification</h4>
                <ul class="organisation-list-wait list-group">
                    <?php foreach ($rdvsPris as $rdv_id => $rdv): ?>
                        <?php $heure = ($rdv->type_rendezvous == RendezvousClient::RENDEZVOUS_TYPE_RAISIN)? 'data-hour="'.preg_replace("/^([0-9]+):[0-9]+$/", '\1:00', $rdv->heure).'"' : ''; ?>
                        <li id="<?php echo $rdv_id ?>" data-tournee="" <?php echo $heure ?> data-title="<?php echo $rdv->raison_sociale ?>" data-point="<?php echo $rdv->lat * 1 ?>,<?php echo $rdv->lon * 1 ?>" class="organisation-item list-group-item col-xs-12">

                            <input type="hidden" class="input-tournee" name="rdvs[<?php echo $rdv->_id ?>][tournee]" value="" />
                            <div class="col-xs-12">
                                <div style="margin-top: 6px;" class="pull-right">
                                    <button data-item="#<?php echo $rdv_id ?>" class="btn btn-success btn-sm hidden" type="button"><span class="glyphicon glyphicon-plus-sign"></span></button>
                                    <button data-item="#<?php echo $rdv_id ?>" class="btn btn-danger btn-sm hidden" type="button"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                </div>

                                <div style="padding-right: 16px; margin-top: 4px;" class="pull-right">
                                    <?php if ($rdv->type_rendezvous == RendezvousClient::RENDEZVOUS_TYPE_RAISIN): ?>
                                        <span style="font-size: 20px;" class="icon-raisins"></span>
                                        <span style="font-size: 16px;"><?php echo str_replace(":", "h", $rdv->heure) ?></span>
                                    <?php else: ?>
                                        <span style="font-size: 20px;" class="icon-mouts"></span>
                                    <?php endif; ?>
                                </div>
                                <div style="margin-right: 10px; margin-top: 9px;" class="pull-left">
                                    <span class="glyphicon glyphicon-map-marker" style="font-size: 24px; color: #e2e2e2"></span>
                                </div>
                                <?php echo $rdv->raison_sociale ?><br /><small class="text-muted"><?php echo $rdv->commune ?></small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="well" style="padding: 0 5px; ">
                <h4 class="text-center" style="text-transform: uppercase;"><span class="glyphicon glyphicon-check"></span> Planifié</h4>
                <ul class="organisation-list list-group sortable">
                    <?php foreach ($heures as $key_heure => $libelle_heure): ?>
                        <li data-value="<?php echo $key_heure ?>" class="organisation-hour list-group-item col-xs-12 disabled text-center">
                            <small><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;<?php echo $libelle_heure ?> h</small>
                        </li>
                        <?php foreach ($rdvs as $heure_rdv => $rendezvous): ?>
                            <?php
                            if ($heure_rdv == 'no-hour') {
                                continue;
                            }
                            if (preg_replace("/^([0-9]+):[0-9]+$/", '\1:00', $heure_rdv) != $key_heure) {
                                continue;
                            }
                            ?>
                            <?php foreach ($rendezvous as $tournee_id => $tourneeRdvs): ?>
                                <?php foreach ($tourneeRdvs as $rdv_id => $rdv): ?>
                                    <li id="<?php echo $rdv_id ?>" data-tournee="<?php echo $tournee_id ?>" data-title="<?php echo $rdv->compte_raison_sociale ?>" data-point="<?php echo $rdv->compte_lat * 1 ?>,<?php echo $rdv->compte_lon * 1 ?>" data-hour="<?php echo preg_replace("/^([0-9]+):[0-9]+$/", '\1:00', $rdv->heure) ?>" class="organisation-item list-group-item col-xs-12">
                                        <input type="hidden" class="input-tournee" name="rdvs[<?php echo $rdv_id ?>][tournee]" value="<?php echo $tournee_id ?>" />
                                        <div class="col-xs-12">
                                            <div style="margin-top: 6px;" class="pull-right">
                                                <button data-item="#<?php echo $rdv_id ?>" class="btn btn-success btn-sm hidden" type="button"><span class="glyphicon glyphicon-plus-sign"></span></button>
                                                <button data-item="#<?php echo $rdv_id ?>" class="btn btn-danger btn-sm" type="button"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                            </div>
                                            <div style="padding-right: 16px; margin-top: 4px;" class="pull-right">
                                                <span style="font-size: 20px;" class="icon-raisins"></span>
                                                <span style="font-size: 16px;"><a href="<?php echo url_for('rendezvous_modification', array('id' => $rdv_id, 'retour' => 'planification')); ?>" class="btn btn-default btn-default-step ajax"><?php echo str_replace(":", "h", $rdv->heure) ?></a></span>
                                            </div>
                                            <div style="margin-right: 10px; margin-top: 9px;" class="pull-left">
                                                <span class="glyphicon glyphicon-map-marker" style="font-size: 24px; color: <?php echo $tourneesCouleur[$tournee_id] ?>"></span>
                                            </div>
                                            <?php echo $rdv->compte_raison_sociale ?>
                                            <br /><small class="text-muted"><?php echo $rdv->compte_commune ?></small>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php if (isset($rdvs['no-hour'])): ?>
                        <?php foreach ($rdvs['no-hour'] as $tournee_id => $tourneeRdvs): ?>
                            <?php foreach ($tourneeRdvs as $rdv_id => $rdv): ?>
                                <li id="<?php echo $rdv_id ?>" data-tournee="<?php echo $tournee_id ?>" data-title="<?php echo $rdv->compte_raison_sociale ?>" data-point="<?php echo $rdv->compte_lat * 1 ?>,<?php echo $rdv->compte_lon * 1 ?>" class="organisation-item list-group-item col-xs-12">
                                    <input type="hidden" class="input-tournee" name="rdvs[<?php echo $rdv_id ?>][tournee]" value="<?php echo $tournee_id ?>" />
                                    <div class="col-xs-12 <?php if (isset($rdvsRealises[$rdv_id])): ?> list-group-item-success <?php endif; ?> <?php if (isset($rdvsAnnules[$rdv_id])): ?> list-group-item-danger <?php endif; ?>">
                                        <?php if (!isset($rdvsRealises[$rdv_id]) && !(isset($rdvsAnnules[$rdv_id]))): ?>
                                            <div style="margin-top: 6px;" class="pull-right">
                                                <button data-item="#<?php echo $rdv_id ?>" class="btn btn-success btn-sm hidden" type="button"><span class="glyphicon glyphicon-plus-sign"></span></button>
                                                <button data-item="#<?php echo $rdv_id ?>" class="btn btn-danger btn-sm" type="button"><span class="glyphicon glyphicon-minus-sign"></span></button>
                                            </div>
                                        <?php endif; ?>
                                        <div style="padding-right: 16px; margin-top: 4px;" class="pull-right">
                                            <span style="font-size: 20px;" class="icon-mouts"></span>
                                        </div>
                                        <div style="margin-right: 10px; margin-top: 9px;" class="pull-left">
                                            <span class="glyphicon glyphicon-map-marker" style="font-size: 24px; color: <?php echo $tourneesCouleur[$tournee_id] ?>"></span>
                                        </div>
                                        <?php echo $rdv->compte_raison_sociale ?>&nbsp;<?php echo ($rdv->nom_agent_origine) ? '(' . $rdv->nom_agent_origine . ')' : ''; ?>
                                        <br /><small class="text-muted"><?php echo $rdv->compte_commune ?>&nbsp;
                                            <?php if (isset($rdvsRealises[$rdv_id])): ?>
                                                (Réalisé)
                                            <?php endif; ?>
                                            <?php if (isset($rdvsAnnules[$rdv_id])): ?>
                                                (Annulé)
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="col-xs-12" id="carteOrganisation" style="height: 650px;"></div>
        </div>
    </div>
    <div class="row row-margin">
        <div class="col-xs-12 text-right">
            <a href="<?php echo url_for('constats_planification_jour', array('jour' => $jour)) ?>" class="btn btn-lg btn-default btn-upper ajax">Valider</a>
        </div>
    </div>
</form>
