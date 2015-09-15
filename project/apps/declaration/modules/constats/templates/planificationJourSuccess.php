<?php
use_helper("Date");
$nbTotalAgent = count($tourneesJournee->tourneesJournee);
$pourcentRealises = $tourneesJournee->pourcentTotalRealise;
$nbRaisins = $tourneesJournee->nbTotalRdvRaisin;
$nbVolume = $tourneesJournee->nbTotalRdvVolume;
?>
<?php include_partial('admin/menu', array('active' => 'constats')); ?>
<div class="row row-margin text-center">
    <h2>Tournée de la journée</h2>
</div>
<div class="page-header">
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <div class="row">
                <div class="col-xs-2 text-left">
                    <h2><a class="text-muted" href="<?php echo url_for('constats_planification_jour', array('jour' => Date::addDelaiToDate("-1 day", $jour))); ?>">
                            <span class="glyphicon glyphicon-arrow-left"></span>
                        </a></h2>
                </div>
                <div class="col-xs-8 text-center">
                    <h2><?php echo ucfirst(format_date($jour, "P", "fr_FR")); ?></h2>
                </div>
                <div class="col-xs-2 text-right">
                    <h2><a class="text-muted" href="<?php echo url_for('constats_planification_jour', array('jour' => Date::addDelaiToDate("+1 day", $jour))); ?>">
                            <span class="glyphicon glyphicon-arrow-right"></span>
                        </a></h2>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row row-margin">
    <div class="col-xs-12">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th class="col-xs-6"><?php echo $nbTotalAgent ?> Agents</th>
                    <th class="text-center col-xs-2"><?php echo $pourcentRealises ?> % Réalisé </th>
                    <th class="text-center col-xs-2"><?php if ($nbRaisins): ?><?php echo $nbRaisins ?>&nbsp;<span class="icon-raisins size-36"></span>&nbsp;<?php endif; ?><?php if ($nbVolume): ?>&nbsp;<?php echo $nbVolume ?>&nbsp;<span class="icon-mouts size-36"></span><?php endif; ?></th>
                    <th></th>
                </tr>
            </thead>
            <?php foreach ($tourneesJournee->tourneesJournee as $tourneeObj) : ?>
                <tr>
                    <td><?php echo $tourneeObj->agent->nom_a_afficher; ?></td>
                    <td class="text-center"><?php echo $tourneeObj->pourcentRealise; ?> %</td>
                    <td class="text-center"><?php if ($tourneeObj->nbRdvRaisin): ?><?php echo $tourneeObj->nbRdvRaisin; ?>&nbsp;<span class="icon-raisins size-36"></span>&nbsp;<?php endif; ?><?php if ($tourneeObj->nbRdvVolume): ?>&nbsp;<?php echo $tourneeObj->nbRdvVolume; ?>&nbsp;<span class="icon-mouts"></span><?php endif; ?></td>
                    <td>
                        <a href="<?php echo url_for('tournee_rendezvous_agent', $tourneeObj->tournee) ?>" class="btn btn-default btn-default-step">Accéder à la tournée</a>
                    </td>
                </tr>

            <?php endforeach; ?>
        </table>
        <a class="btn btn-warning btn-upper" href="<?php echo url_for('constats', array('jour' => $jour)) ?>"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;&nbsp;Accueil</a>
        <a class="btn btn-default btn-default-step btn-upper" href="<?php echo url_for('constats_planification_ajout_agent', array('jour' => $jour)) ?>"><span class="glyphicon glyphicon-plus-sign"></span>&nbsp;&nbsp;Ajouter un agent</a>

        <a href="<?php echo url_for('constats_planifications', array('date' => $jour)) ?>" class="btn btn-lg btn-default btn-upper pull-right"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;Retour à la planification</a>


    </div>
</div>


