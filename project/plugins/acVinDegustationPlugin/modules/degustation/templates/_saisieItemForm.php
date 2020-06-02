<tr class="dynamic-element-item" id="saisie_item_<?php echo $form->getName() ?>">
    <td class="form-group">
        <?php echo $form['numero']->render(array("class" => "form-control", "placeholder" => "N°", "data-increment" => "numero")); ?>
    </td>
    <td class="form-group">
        <?php echo $form['produit']->render(array("class" => "select2autocomplete form-control select2", "placeholder" => "Séléctionner un produit", "data-copie" => "produit")); ?>
    </td>
    <td class="form-group">
        <?php echo $form["denomination_complementaire"]->render(array("class" => "form-control", "placeholder" => "Dénomination...", "data-norequired" => true)); ?>
    </td>
    <td class="form-group">
        <?php echo $form["etablissement"]->render(array("placeholder" => "Chercher un établissement")); ?>
    </td>
    <td class="form-group text-center">
        <?php echo $form['commission']->render(array("class" => "form-control", "placeholder" => "N°", "data-copie" => "commission")); ?>
    </td>
    <td class="form-group text-center">
        <button tabindex="-1" type="button" data-add=".dynamic-element-add" data-lines="#saisie_container .dynamic-element-item" data-line="#saisie_item_<?php echo $form->getName() ?>" class="btn btn-default-step dynamic-element-delete"><span class="glyphicon glyphicon-remove"></span></button>
    </td>
</tr>
