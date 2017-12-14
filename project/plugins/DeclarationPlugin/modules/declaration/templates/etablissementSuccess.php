<ol class="breadcrumb">

  <li><a href="<?php echo url_for('accueil'); ?>">Déclarations</a></li>
  <li><a href="<?php echo url_for('declaration_etablissement', $etablissement); ?>"><?php echo $etablissement->getNom() ?> (<?php echo $etablissement->identifiant ?>)</a></li>
  <li class="active"><a href=""><?php echo $campagne ?>-<?php echo $campagne +1 ?></a></li>
</ol>

<?php if ($sf_user->isAdmin() && class_exists("EtablissementChoiceForm")): ?>
<div class="row row-margin">
    <div class="col-xs-12">
        <?php include_partial('etablissement/formChoice', array('form' => $form, 'action' => url_for('declaration_etablissement_selection'), 'noautofocus' => true)); ?>
    </div>
</div>
<?php endif; ?>
<div class="page-header">
    <div class="pull-right">
        <?php if ($sf_user->isAdmin()): ?>
        <form method="GET" class="form-inline" action="">
            Campagne :
            <select class="select2SubmitOnChange form-control" name="campagne">
                <?php for($i=ConfigurationClient::getInstance()->getCampagneManager()->getCurrent(); $i > ConfigurationClient::getInstance()->getCampagneManager()->getCurrent() - 5; $i--): ?>
                    <option <?php if($campagne == $i): ?>selected="selected"<?php endif; ?> value="<?php echo $i ?>"><?php echo $i; ?>-<?php echo $i+1 ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-default">Changer</button>
        </form>
        <?php else: ?>
            <span style="margin-top: 8px; display: inline-block;" class="text-muted">Campagne <?php echo $campagne ?>-<?php echo $campagne + 1 ?></span>
        <?php endif; ?>
    </div>
    <h2>Eléments déclaratifs</h2>
</div>

<p>Veuillez trouver ci-dessous l'ensemble de vos éléments déclaratifs</p>
<div class="row">
    <?php include_component('drev', 'monEspace', array('etablissement' => $etablissement, 'campagne' => $campagne)); ?>
    <?php if(class_exists("DRevMarc")): ?>
    <?php include_component('drevmarc', 'monEspace', array('etablissement' => $etablissement, 'campagne' => $campagne)); ?>
    <?php endif; ?>
    <?php if(class_exists("TravauxMarc")): ?>
    <?php include_component('travauxmarc', 'monEspace', array('etablissement' => $etablissement, 'campagne' => $campagne)); ?>
    <?php endif; ?>
    <?php if(class_exists("Parcellaire")): ?>
    <?php include_component('parcellaire', 'monEspace', array('etablissement' => $etablissement, 'campagne' => ConfigurationClient::getInstance()->getCampagneManager()->getNext($campagne))); ?>
    <?php include_component('parcellaireCremant', 'monEspace', array('etablissement' => $etablissement, 'campagne' => ConfigurationClient::getInstance()->getCampagneManager()->getNext($campagne))); ?>
    <?php include_component('intentionCremant', 'monEspace', array('etablissement' => $etablissement, 'campagne' => ConfigurationClient::getInstance()->getCampagneManager()->getNext($campagne))); ?>
    <?php endif; ?>
    <?php if(class_exists("Tirage")): ?>
    <?php include_component('tirage', 'monEspace', array('etablissement' => $etablissement, 'campagne' => $campagne)); ?>
    <?php endif; ?>
    <?php include_component('fichier', 'monEspace', array('etablissement' => $etablissement)); ?>
</div>
<?php include_partial('fichier/history', array('etablissement' => $etablissement, 'history' => PieceAllView::getInstance()->getPiecesByEtablissement($etablissement->identifiant, $sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)), 'limit' => Piece::LIMIT_HISTORY)); ?>