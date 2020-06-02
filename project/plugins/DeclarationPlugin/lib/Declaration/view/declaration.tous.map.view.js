function(doc) {

    if(doc.type != "DRev" && doc.type != "RegistreVCI" && doc.type != "DRevMarc" && doc.type != "Tirage" && doc.type != "TravauxMarc" && doc.type != "ParcellaireIrrigable"  && doc.type != "ParcellaireIrrigue" && doc.type != "ParcellaireIntentionAffectation" && doc.type != "ParcellaireAffectation" && doc.type != "Parcellaire") {

        return;
    }

    if(!doc.campagne) {
        return;
    }

    if(!doc.declarant) {

        return;
    }

    if(doc.lecture_seule) {

        return;
    }

    var nb_doc_en_attente = 0;

    if(doc.documents) {
        for(key in doc.documents) {
            if(doc.documents[key].statut != "RECU") {
                nb_doc_en_attente++;
            }
        }
    }

    var validation = null;
    if(doc.validation) {
    validation = doc.validation;
    }

    var validation_odg = null;
    if(doc.validation_odg) {
    validation_odg = doc.validation_odg;
    }

    var etape = null;
    if(doc.etape) {
    etape = doc.etape;
    }

    var papier = 0;
    if(doc.papier) {
        papier = 1;
    }

    var automatique = 0;
    if(doc.automatique) {
        automatique = 1;
    }

    var raison_sociale = null;
    if(doc.declarant && doc.declarant.raison_sociale) {
        raison_sociale = doc.declarant.raison_sociale;
    }

    var commune = null;
    if(doc.declarant && doc.declarant.commune) {
        commune = doc.declarant.commune;
    }

    var email = null;
    if(doc.declarant && doc.declarant.email) {
        email = doc.declarant.email;
    }

    var cvi = null;
    if(doc.declarant && doc.declarant.cvi) {
        cvi = doc.declarant.cvi;
    }

    var mode = "Télédeclaration";

    if(doc.automatique) {
        mode = "Importé";
    }

    if(doc.papier) {
        mode = "Saisie interne";
    }

    var statut = "Brouillon";
    var infos = "Étape " + doc.etape;
    if(validation_odg) {
	    statut = "Approuvé";
        infos = null;
        if(validation_odg !== false && validation_odg !== true) {
            infos = validation_odg.replace(/([0-9]+)-([0-9]+)-([0-9]+)/, "$3/$2/$1");
        }
    }

    if(validation && !validation_odg) {
	    statut = "À approuver";
        infos = null;
        if(nb_doc_en_attente) {
            statutProduit = "En attente";
           infos = nb_doc_en_attente + " pièce(s) en attente";
	    }
    }

    var type = doc.type;

    var date = null;
    if(validation && validation !== false && validation !== true) {
	    date = validation;
    }

    if(doc._id.indexOf('PARCELLAIRECREMANT') > -1) {
	    type = "Parcellaire Crémant";
    }

    if(doc._id.indexOf('INTENTIONCREMANT') > -1) {
	    type = "Intention Crémant";
    }

    var nb_emits = 0;
    if(doc.type == "DRev" && !doc.declaration.certification){
           for (key in doc.declaration) {
              statutProduit = statut;
              for(detailKey in doc.declaration[key]){
                statutProduit = statut;
                if(doc.declaration[key][detailKey].validation_odg){
                  statutProduit = "Approuvé";
                }
    	          emit([type, doc.campagne, doc.identifiant, mode, statutProduit, key, date, infos, raison_sociale, commune, email, cvi], 1);
                  nb_emits = nb_emits + 1;
              }
           }
    }

    if(!nb_emits){
        emit([type, doc.campagne, doc.identifiant, mode, statut, null, date, infos, raison_sociale, commune, email, cvi], 1);
    }
}
