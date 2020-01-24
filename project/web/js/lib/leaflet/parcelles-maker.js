var parcelles = window.parcelles;
var delimitationStr = window.delimitation;
var myMarker;
var mygeojson;
var myLayer=[];
var fitBound;
var minZoom = 17;
var listIdLayer=[];
var myidus= [];
var filters;
var error = true;

function parseString(dlmString){
    var mydlm = [];
    dlmString.split("|").forEach(function(str){
        mydlm.push(JSON.parse(str));
    });
    return mydlm;
}

var dlmJson = parseString(delimitationStr);

var map = L.map('map');


L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
    maxZoom: 30,
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> creator, ' +
        '<a href="https://www.24eme.fr/">24eme Société coopérative</a>, ' +
        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    id: 'mapbox.light'
}).addTo(map);
var er;
$('#locate-position').on('click', function(){
    map.locate({setView: true});
});
var icon = L.divIcon({className: 'glyphicon glyphicon-user'});
function onLocationFound(e) {
    var radius = e.accuracy / 100;
    L.marker(e.latlng,{icon: icon}).addTo(map);
    L.circle(e.latlng, radius).addTo(map);
    map.setView(e.latlng, minZoom);    
}
function onLocationError(e) {
    alert("Vous n'êtes actuellement pas localisable. Veuillez activer la localisation.");
}

map.on('locationfound', onLocationFound);

map.on('locationerror', onLocationError);

function getColor(d) {

    return d.includes("rouge") ? '#790000' :
           d.includes("rosé") ? '#f95087':
           d.includes("blanc") ? '#efeef3':'#2b0c0c';
}



function style(feature) {
    var color;
    color = getColor(feature.properties.parcellaires['0'].Produit);
    return {
        fillColor: color,
        weight: 2,
        opacity: 2,
        color: color,
        dashArray: '1',
        fillOpacity: 1
    };
}

function styleDelimitation(){
    return {
        fillColor: '#d0f3fb',
        weight: 2,
        opacity: 2,
        color: 'white',
        dashArray: '5',
        fillOpacity: 0.7
    }
}

function closeDisplayer(){
    var res = false;
    
    if(myMarker){
        map.removeLayer(myMarker);//remove preview marker, show one marker at the same time
        res = true;
    }
    if(map._popup != null){
        map.closePopup();//close popup if is opened
        res = true;
    }
    return res;
}

function loadGeoJson(){
    mygeojson = L.geoJSON(parcelles, {
    style: style,
    onEachFeature: onEachFeature,
    }).addTo(map);

    zoomOnMap();
}


function zoomOnMap(){

    closeDisplayer();
    myMarker = null;

    map.fitBounds(mygeojson.getBounds());
}

mygeojson = L.geoJSON(dlmJson,{
    style: styleDelimitation
}).addTo(map);
zoomOnMap();

loadGeoJson(); //Create map layer from geojson coordonates 


function zoomToFeature(e) {
    if(!closeDisplayer() || map.getZoom() < minZoom){

        myMarker = L.marker(e.target.getCenter()).addTo(map); 
        var f = map.fitBounds(e.target.getBounds());
    }else{
        map.openPopup(e.target._popup);
        var popup = $(".leaflet-popup-content")[0];
        minPopupWidth = popup.style.width;
        var width = (e.target.feature.properties.parcellaires.length +1) * 80 +"px";
        if(width > minPopupWidth){
            popup.style.overflowX = "scroll";
        }   
    }
}

function onEachFeature(feature, layer) {
    layer.on({
        click: zoomToFeature,
    });
    
    var Cepages = "<th>Produits et cepages</th>";
    var numParcelles = "<th>Parcelle N°</th>";
    var Superficies = "<th>Superficies  <span>(ha)</span></th>";
    var ecartPied = "<th>Écart Pieds</th>";
    var ecartRang = "<th>Écart Rang</th>";
    var compagnes = "<th>Année plantat°</th>";
    feature.properties.parcellaires.forEach(function(parcelle){
        numParcelles += '<td>'+parcelle["Numero parcelle"]+'</td>';
        Cepages += '<td><span class="text-muted">'+parcelle.Produit+'</span> '+parcelle.Cepage+'</td>';
        compagnes += '<td>'+parcelle.Campagne+'</td>';
        Superficies += '<td>'+parcelle.Superficie+'</td>';
        ecartPied += '<td>'+parcelle["Ecart pied"]+'</td>';
        ecartRang +='<td>'+parcelle["Ecart rang"]+'</td>';
    });
    
    var popupContent ='<table class="table table-bordered table-condensed table-striped"><tbody>'+
                    '<tr>'+numParcelles+'</tr>'+
                    '<tr>'+Cepages+'</tr>'+
                    '<tr>'+compagnes+'</tr>'+
                    '<tr>'+Superficies+'</tr>'+
                    '<tr>'+ecartPied+'</tr>'+
                    '<tr>'+ecartRang+'</tr>'+
                    '</tbody></table>';

    if (feature.properties && feature.properties.popupContent) {
        popupContent += feature.properties.popupContent;
    }

    layer.bindPopup(popupContent);

    layer._events.click.reverse();

}

function showParcelle(id, htmlObj){
    if(this.map) {
        this.map.eachLayer(function(layer) {
            if(layer.feature){
                if(layer.feature.id == id){
                    error = false;
                    closeDisplayer();
                    this.myLayer = layer;
                    center = myLayer.getCenter();
                    this.myMarker = L.marker(center,  {

                    }).addTo(map);
                    
                    this.map.fitBounds(this.myLayer.getBounds());
                    $(window).scrollTop(0);
                }   
            }
        });
        if(error){
            alert("Erreur: Cette parcelle n'existe pas au cadastre.");
        }        
    }else{
        alert("Error: Map empty !");
    }
}
/**
* On select words filter, we filter map layers also.
* myfilters it's input element  
**/
function filterMapOn(myfilters){
    filters = myfilters;
    $(".hamzastyle-item").each(function(i, val){
        var words = val.getAttribute("data-words");
        if(filters.value && eval(words).includes(filters.value)){
            myidus.push(val.lastElementChild.firstElementChild.getAttribute("id"));            
        }
    });
    
    if(filters.value && myidus.length){
        layerFilter(styleDelimitation(), myidus);
    }else{
        myidus = [];
        layerFilter("default", myidus);
    }   
}

/**
* hide layer(s) by changing color filling (function styleDelimitation)
* show layer(s) by changing color filling with produit color (function style) 
**/
function layerFilter(styleCss, myidus){
    if(map) {
        closeDisplayer();
        map.eachLayer(function(layer) {
            if(layer.feature){
                if(typeof(styleCss) == 'object' && !myidus.includes(layer.feature.id)){
                   layer.setStyle(styleCss);                    
                }else if(layer.feature.properties.hasOwnProperty('parcellaires')){
                    console.log(layer.feature.id,typeof(styleCss));
                    layer.setStyle(style(layer.feature));
                }
            }
        });
    }
}

$(window).on("load", function() {
    filters = $("#hamzastyle")[0];
    if(filters){
        filterMapOn(filters);
    }
});

