<?php if(!count($drevsHistory) && !$etablissement->hasFamille(EtablissementClient::FAMILLE_VINIFICATEUR)): ?>
    <?php return; ?>
<?php endif; ?>

    <div class="col-xs-4">
         <?php if($etablissement->hasFamille(EtablissementClient::FAMILLE_VINIFICATEUR)): ?>
        <div class="panel <?php if ($drev && $drev->validation): ?>panel-success<?php else: ?>panel-primary<?php endif; ?> equal-height">
            <div class="panel-heading">
                <h3>Appellations&nbsp;Viticoles&nbsp;<?php echo ConfigurationClient::getInstance()->getCampagneManager()->getCurrent(); ?></h3>
            </div>
            <div class="panel-body">
                <?php if ($drev && $drev->validation): ?>
                    <p>
                        <a class="btn btn-lg btn-block btn-primary" href="<?php echo url_for('drev_visualisation', $drev) ?>">Visualiser</a>
                    </p>
                    <p>
                        <a class="btn btn-xs btn-danger pull-right" href="<?php echo url_for('drev_delete', $drev) ?>"><span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;Supprimer</a>
                    </p>
                <?php elseif ($drev): ?>
                    <p>
                        <a class="btn btn-lg btn-block btn-default" href="<?php echo url_for('drev_edit', $drev) ?>">Continuer</a>
                    </p>
                    <p>
                        <a class="btn btn-xs btn-danger pull-right" href="<?php echo url_for('drev_delete', $drev) ?>"><span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;Supprimer le brouillon</a>
                    </p>
                <?php else: ?>
                    <p>
                        <a class="btn btn-lg btn-block btn-default" href="<?php echo url_for('drev_create', $etablissement) ?>">Démarrer</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>