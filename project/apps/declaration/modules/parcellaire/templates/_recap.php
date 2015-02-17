<?php use_helper("Date");
$last = $parcellaire->getParcellaireLastCampagne();
?><div class="row">
    <div class="col-xs-12">
        <?php
    foreach ($parcellaire->declaration->getAppellations() as $kappellation => $appellation):
            ?><h3><strong> <?php echo "Appellation " . $appellation->getLibelleComplet(); ?></strong> <span class="small right" style="text-align: right;"><?php echo $appellation->getSuperficieTotale() . ' (ares)'; ?></span></h3>
<?php if (! $appellation->getSuperficieTotale()) {echo "<i>Vous n'avez pas déclaré de produit pour cette appellation</i>"; continue;} ?>
            <table class="table table-striped table-condensed">
                <tbody>
<?php
    foreach ($appellation->getDetailsSortedByParcelle() as $detail):
$styleline = '';
$styleproduit = '';
$styleparcelle = '';
$stylesuperficie = '';
if (isset($diff) && $diff) {
    if (!$last->exist($detail->getHash())) {
        $styleline = 'border-style: solid; border-width: 1px; border-color: darkgreen;';
    }else {
        if ($detail->getParcelleIdentifiant() != $last->get($detail->getHash())->getParcelleIdentifiant()) {
            $styleparcelle = 'border-style: solid; border-width: 1px; border-color: darkorange;';
        }
        if ($detail->getSuperficie() != $last->get($detail->getHash())->getSuperficie()) {
            $styleline = (!$detail->superficie)? 'text-decoration: line-through; border-style: solid; border-width: 1px; border-color: darkred' : '';
            $stylesuperficie = (!$detail->superficie)? 'border-style: solid; border-width: 1px; border-color: darkred' : 'border-style: solid; border-width: 1px; border-color: darkorange';
        }
    }
    if (! $detail->getSuperficie()) {
        $stylesuperficie = 'border-style: solid; border-width: 1px; border-color: darkred';
    }
}
?>
                            <tr style="<?php echo $styleline;?>">
                                <td class="col-xs-3" style="<?php echo $styleproduit; ?>">
                                    <?php echo $detail->getLieuLibelle(); ?>
                                </td>   
                                <td class="col-xs-3" style="<?php echo $styleproduit; ?>">
                                    <?php echo $detail->getCepageLibelle();; ?>
                                </td>   
                                <td class="col-xs-4" style="text-align: right; <?php echo $styleparcelle; ?>">
                                    <?php echo $detail->getParcelleIdentifiant(); ?>
                                </td>   
                                <td class="col-xs-2" style="text-align: right; <?php echo $stylesuperficie;?>">
                                    <?php echo $detail->superficie . '&nbsp;ares'; ?>
                                </td>   
                            </tr> 
                    <?php endforeach; ?>
                </tbody>
            </table>
    <p>Ces produits sont destinés à être vignifiés <?php
    $libelledestination = array('SUR_PLACE' => 'sur place', 'CAVE_COOPERATIVE' => 'en caves coopératives', 'NEGOCIANT' => 'par des négociants');
    $acheteurs = $appellation->getAcheteursNode();
    $i = 0;
    foreach ($acheteurs as $type => $acheteurs) {
          if ($i > 0) if ($i == count($acheteurs))
              echo ' et ';
          else
              echo ', ';
          $i++;
                        echo $libelledestination[$type]." ";
                        if ($type != 'SUR_PLACE')  {
                        echo "(";
                        $y = 0;
                        foreach($acheteurs as $cvi => $a) {
                            if ($y) echo ", ";
                            print $a->nom;
                            $y = 1;
                        }
                        echo ")";}
                    }?>.</p>
        <?php endforeach; ?>
    </div>
</div>