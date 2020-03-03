<?php foreach ($parcellaireAffectation->declaration->getParcellesByCommune(true) as $commune => $parcelles): ?>
<div class="row">
    <div class="col-xs-12">
        <h3><?php echo $commune; ?></h3>
    </div>
</div>
<table id="parcelles_<?php echo $commune; ?>" class="table table-bordered table-condensed table-striped duplicateChoicesTable tableParcellaire">
	<thead>
    	<tr>
            <th class="col-xs-2">Lieu-dit</th>
            <th class="col-xs-1">Section /<br />N° parcelle</th>
            <th class="col-xs-2">Cépage</th>
            <th class="col-xs-1">Année plantat°</th>
            <th class="col-xs-2">Dénom. compl.</th>
            <th class="col-xs-1" style="text-align: right;">Surf. affectable&nbsp;<span class="text-muted small">(ha)</span></th>
            <?php if($parcellaireAffectation->isValidee()): ?>
            <th class="col-xs-1">Date d'affectation</th>
            <?php endif; ?>
        </tr>
	</thead>
	<tbody>
	<?php
		foreach ($parcelles as $parcelle):
	?>
		<tr class="vertical-center">
            <td><?php echo $parcelle->lieu; ?></td>
            <td style="text-align: center;"><?php echo $parcelle->section; ?> <span class="text-muted">/</span> <?php echo $parcelle->numero_parcelle; ?></td>
            <td><?php echo $parcelle->cepage; ?></td>
            <td><?php echo $parcelle->campagne_plantation; ?></td>
            <td><?php echo $parcelle->getDgcLibelle(); ?></td>
            <td style="text-align: right;"><?php if ($parcelle->superficie_affectation != $parcelle->superficie): ?><span style="margin: 3px;" class="pull-left glyphicon glyphicon-exclamation-sign"  data-toggle="tooltip" title="Surface totale <?php echo $parcelle->superficie; ?> ha">&nbsp;</span><?php endif; ?><span><?php echo $parcelle->superficie_affectation; ?></span></td>
            <?php if($parcellaireAffectation->isValidee()): ?>
            <td class="text-center"><?php echo format_date($parcelle->date_affectation, "dd/MM/yyyy", "fr_FR"); ?></td>
            <?php endif; ?>
        </tr>
    <?php  endforeach; ?>
    </tbody>
</table>
<?php  endforeach; ?>