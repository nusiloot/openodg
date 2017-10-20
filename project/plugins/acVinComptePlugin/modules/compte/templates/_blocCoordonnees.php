<?php if (!$compte->isSameAdresseThanSociete() || isset($forceCoordonnee)): ?>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-3 text-muted">
                Adresse&nbsp;:
            </div>
            <div class="col-xs-9">
                <address style="margin-bottom: 0;">
                    <?php echo $compte->adresse; ?><br />
                    <?php if ($compte->adresse_complementaire) : ?><?php echo $compte->adresse_complementaire ?><br /><?php endif ?>
                    <span <?php if($compte->insee): ?>title="<?php echo $compte->insee ?>"<?php endif; ?>><?php echo $compte->code_postal; ?></span> <?php echo $compte->commune; ?> <?php if($compte->pays): ?><small class="text-muted">(<?php echo $compte->pays; ?>)<?php endif; ?></small>
                </address>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (!$compte->isSameContactThanSociete() || isset($forceCoordonnee)): ?>
    <div style="margin-top: 10px;" class="col-xs-12">
        <?php if ($compte->email) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Email&nbsp;:
                </div>
                <div class="col-xs-9">
                    <a href="mailto:<?php echo $compte->email; ?>"><?php echo $compte->email; ?></a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($compte->telephone_perso) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Tél.&nbsp;perso&nbsp;:
                </div>
                <div class="col-xs-9">
                    <a href="callto:<?php echo $compte->telephone_perso; ?>"><?php echo $compte->telephone_perso; ?></a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($compte->telephone_bureau) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Tél.&nbsp;bureau&nbsp;:
                </div>
                <div class="col-xs-9"><a href="callto:<?php echo $compte->telephone_bureau; ?>"><?php echo $compte->telephone_bureau; ?></a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($compte->telephone_mobile) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Tél.&nbsp;mobile&nbsp;:
                </div>
                <div class="col-xs-9">
                    <a href="callto:<?php echo $compte->telephone_mobile; ?>"><?php echo $compte->telephone_mobile; ?></a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($compte->fax) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Fax&nbsp;:
                </div>
                <div class="col-xs-9">
                    <a href="callto:<?php echo $compte->fax; ?>"><?php echo $compte->fax; ?></a>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($compte->exist('site_internet') && $compte->site_internet) : ?>
            <div class="row">
                <div class="col-xs-3 text-muted">
                    Site&nbsp;Internet&nbsp;:
                </div>
                <div class="col-xs-9">
                    <a href="<?php echo $compte->site_internet; ?>"><?php echo $compte->site_internet; ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
