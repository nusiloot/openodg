<?php use_helper("Date"); ?>
<?php use_javascript("lib/chart.min.js", "last") ?>
<?php include_partial('admin/menu', array('active' => 'tournees')); ?>

<form action="" method="post" class="form-horizontal">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>
    <div class="row">
        <div class="col-xs-10">
            <div class="form-group <?php if($form["date"]->hasError()): ?>has-error<?php endif; ?>">
                <?php echo $form["date_prelevement_debut"]->renderError(); ?>
                <?php echo $form["date_prelevement_debut"]->renderLabel("Date de début de demande des prélévements", array("class" => "col-xs-6 control-label")); ?>
                <div class="col-xs-6">
                    <div class="input-group date-picker">
                        <?php echo $form["date_prelevement_debut"]->render(array("class" => "form-control")); ?>
                        <div class="input-group-addon">
                            <span class="glyphicon-calendar glyphicon"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group <?php if($form["date"]->hasError()): ?>has-error<?php endif; ?>">
                <?php echo $form["date"]->renderError(); ?>
                <?php echo $form["date"]->renderLabel("Date de dégustation", array("class" => "col-xs-6 control-label")); ?>
                <div class="col-xs-6">
                    <div class="input-group date-picker">
                        <?php echo $form["date"]->render(array("class" => "form-control")); ?>
                        <div class="input-group-addon">
                            <span class="glyphicon-calendar glyphicon"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group <?php if($form["appellation"]->hasError()): ?>has-error<?php endif; ?>">
                <?php echo $form["appellation"]->renderError(); ?>
                <?php echo $form["appellation"]->renderLabel("Appellation / Mention", array("class" => "col-xs-6 control-label")); ?>
                <div class="col-xs-6">
                    <?php echo $form["appellation"]->render(array("class" => "form-control")); ?>
                </div>
            </div>
            <div class="form-group text-right">
                <div class="col-xs-6 col-xs-offset-6">
                    <button type="submit" class="btn btn-default btn-lg btn-block btn-upper">Créer</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-xs-12">
        <legend><small>Demandes de prélevements dans le temps</small></legend>
        <canvas id="graphique" width="920" class="col-xs-12" height="200"></canvas>
    </div>
</div>
<script type="text/javascript">
window.onload = function () {
        var ctx = document.getElementById("graphique").getContext("2d");
        var myNewChart = new Chart(ctx).Bar({
            labels: <?php echo json_encode(array_keys($demandes_alsace->getRawValue())) ?>,
            datasets: [
                {
                    label: "AOC Alsace",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: <?php echo json_encode(array_values($demandes_alsace->getRawValue())) ?>
                },
                {
                    label: "VT / SGN",
                    fillColor: "rgba(0,220,220,0.2)",
                    strokeColor: "rgba(0,220,220,1)",
                    pointColor: "rgba(0,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(0,220,220,1)",
                    data: <?php echo json_encode(array_values($demandes_vtsgn->getRawValue())) ?>
                },
            ]
        }, {multiTooltipTemplate: "<%= datasetLabel %> - <%= value %>"} );
};
</script>

<?php include_component('degustation', 'list'); ?>
