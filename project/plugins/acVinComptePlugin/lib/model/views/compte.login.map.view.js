function(doc) {
    if (doc.type && doc.type != "Compte") {
  	   return ;
    }
    emit([doc.identifiant], doc._id);
    if (doc.alternative_logins) {
        for (login in doc.alternative_logins) {
            emit([doc.alternative_logins[login]], doc._id);
        }
    }
 }
