<?php use_javascript('lib/leaflet/leaflet.js'); ?>
<?php use_stylesheet('/js/lib/leaflet/leaflet.css'); ?>
<?php use_stylesheet('/js/lib/leaflet/marker.css'); ?>

<div id="map" class="col-12" style="height: 350px; margin-bottom: 20px;">
	<button id="refreshButton" onclick="zoomOnMap()"><i class="glyphicon glyphicon-fullscreen"></i></button>
	<button id="locate-position"><i class="glyphicon glyphicon-screenshot"></i></button>
</div>



<?php $parcellaire_client = ParcellaireClient::getInstance();
$import = $parcellaire_client->getParcellaireGeoJson($parcellaire->getEtablissementObject()->getIdentifiant(), $parcellaire->getEtablissementObject()->getCvi());
	$list_communes = implode("|", $parcellaire_client->getDelimitations($parcellaire->declaration->getCommunes()));
?>

<script type="text/javascript">
	var parcelles = JSON.parse('<?php echo addslashes($import); ?>');
	var delimitation = '<?php echo addslashes($list_communes); ?>';
</script>
<?php use_javascript('lib/leaflet/parcelles-maker.js'); ?>
