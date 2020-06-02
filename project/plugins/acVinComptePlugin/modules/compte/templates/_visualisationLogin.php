<div class="row">
<div style="margin-bottom: 5px;" class="col-xs-3  text-muted">Login&nbsp;:</div>
<div style="margin-bottom: 5px;" class="col-xs-3"><?php echo $compte->getLogin(); ?></div>
<?php if (preg_match('/{TEXT}(.*)/', $compte->getSociete()->getMasterCompte()->mot_de_passe, $m)) : ?>
<div style="margin-bottom: 5px;" class="col-xs-3 text-muted">Code de création&nbsp;:</div>
<div style="margin-bottom: 5px;" class="col-xs-3"><?php echo $m[1]; ?></div>
<?php elseif (preg_match('/{OUBLIE}(.*)/', $compte->getSociete()->getMasterCompte()->mot_de_passe, $m)) : ?>
<div style="margin-bottom: 5px;" class="col-xs-6"><span class="text-muted">Mot de passe oublié&nbsp;:</span><br/>
<?php echo "https://".$_SERVER['HTTP_HOST'].url_for("compte_teledeclarant_mot_de_passe_oublie_login", array("login" => $compte->getSociete()->identifiant, "mdp" => $m[1])); ?></div>
<?php else: ?>
<div style="margin-bottom: 5px;" class="col-xs-6 text-muted">Mot de passe déjà créé</div>
<?php endif; ?>
<?php if ($compte->getSociete()->getMasterCompte()->exist('alternative_logins') && count($compte->getSociete()->getMasterCompte()->alternative_logins)) : ?>
<div style="margin-bottom: 5px;" class="col-xs-3  text-muted">Logins DéclarVins&nbsp;:</div>
<div style="margin-bottom: 5px;" class="col-xs-9"><?php echo implode(', ', $compte->getSociete()->getMasterCompte()->alternative_logins->getRawValue()->toArray()); ?></div>
<?php endif; ?>
<?php if($compte->exist('droits') && count($compte->getRawValue()->droits->toArray(true, false)) > 0): ?>
<div style="margin-bottom: 5px;" class="col-xs-3  text-muted">Droits&nbsp;:</div>
<div style="margin-bottom: 5px;" class="col-xs-9"><?php echo implode(", ", $compte->getRawValue()->droits->toArray(true, false)); ?></div>
<?php endif; ?>
</div>
