<?php include_partial('degustation/breadcrumb', array('tournee' => $tournee )); ?>
<?php include_partial('degustation/stepSaisie', array('tournee' => $tournee, 'active' => TourneeSaisieEtapes::ETAPE_SAISIE_DEGUSTATEURS)); ?>

<div class="page-header">
    <h2>Saisie des dégustateurs</h2>
</div>

<form action="<?php echo url_for("degustation_saisie_degustateurs", $tournee) ?>" method="post" class="form-horizontal">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="col-xs-11">Dégustateur</th>
                <th class="col-xs-1"></th>
            </tr>
        </thead>
        <tbody id="saisie_container">
            <?php foreach($form as $key => $formPrelevement): ?>
                <?php if(!preg_match("/^degustateur_/", $key)): continue; endif;?>
                <?php echo include_partial('degustation/saisieDegustateurItemForm', array('form' => $formPrelevement)); ?>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><button tabindex="-1" type="button" data-container="#saisie_container" data-template="#template_prevement_item" class="btn btn-xs btn-default-step dynamic-element-add"><span class="glyphicon-plus"></span> Ajouter</button></td>
            </tr>
        </tfoot>
    </table>
    <script id="template_prevement_item" type="text/x-jquery-tmpl">
        <?php echo include_partial('degustation/saisieDegustateurItemForm', array('form' => $form->getFormTemplate())); ?>
    </script>

    <div class="row row-margin row-button">
        <div class="col-xs-6">
            <a href="<?php echo url_for('degustation_saisie', $tournee) ?>" class="btn btn-primary btn-lg btn-upper">Précédent</a>
        </div>
        <div class="col-xs-6 text-right">
            <button class="btn btn-default btn-lg btn-dynamic-element-submit" type="submit">Continuer</button>
        </div>
    </div>
</form>
