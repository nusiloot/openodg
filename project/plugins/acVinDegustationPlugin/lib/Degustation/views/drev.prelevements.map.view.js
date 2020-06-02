function(doc) {

    if(doc.type != "DRev") {

        return;
    }

    if(doc.lecture_seule) {

        return;
    }

    if(!doc.validation_odg) {
        return;
    }

    for(key in doc.prelevements) {
        var prelevement = doc.prelevements[key];
        chai = doc.chais['cuve_'];
        if(prelevement.date) {
            force = 0;
            if (prelevement.force) {force = prelevement.force;}
            emit([key, prelevement.date, doc.identifiant, doc.declarant.raison_sociale, chai.adresse, chai.code_postal, chai.commune, force], prelevement.total_lots);
        }
    }
}
