<div id="saisie_constat_volume_{{ keyConstatNode}}" ng-show="active == 'saisie'" class="col-xs-12 print-margin-bottom">
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-xs-12" style="font-size: 17px;">
                {{ constat.produit_libelle }} {{ constat.denomination_lieu_dit }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12" style="font-size: 17px;">
                <span class="icon-raisins"></span>&nbsp;{{ constat.nb_contenant }} {{ constat.contenant_libelle }}<span ng-show="constat.nb_contenant > 1">s</span>, {{ constat.degre_potentiel_raisin }}° Potentiel
                
            </div>
           
        </div>
        <div ng-show="constat.commentaire_raisin" class="form-group" style="font-size: 17px;">
            <div class="col-xs-12">
                <span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;{{ constat.commentaire_raisin }}
            </div>
        </div>
        <div class="form-group">
            <div ng-class="{ 'hidden': !constat.erreurs['degre_potentiel_volume'] }" class="alert alert-danger">
                Valeur incohérente
            </div>
            <div class="col-xs-6">
                <input placeholder="Degré potentiel" id="degre_potentiel_volume{{ keyConstatNode}}" ng-model="constat.degre_potentiel_volume" type="number" lang="en" step="0.01" class="form-control input-lg" ng-keydown="blurOnEnter($event)" ng-blur="blur()" min="10" max="32" />
            </div>
            <div class="col-xs-6 lead">
                <p style="margin: 0;" class="form-control-static">° potentiel</p>
            </div>
        </div>
        <div class="form-group">
            <div ng-class="{ 'hidden': !constat.erreurs['volume_obtenu'] }" class="alert alert-danger">
                Vous devez saisir le volume obtenu
            </div>
            <div class="col-xs-6">
                <input placeholder="Volume obtenu" id="volume_obtenu{{ keyConstatNode}}" ng-model="constat.volume_obtenu" type="number" lang="en" step="0.01" class="form-control input-lg" ng-keydown="blurOnEnter($event)" ng-blur="blur()" />
            </div>
            <div class="col-xs-6 lead">
                <p style="margin: 0;" class="form-control-static">hl obtenu</p>
            </div>
        </div>
        <div class="form-group">
            <div ng-class="{ 'hidden': !constat.erreurs['type_vtsgn'] }" class="alert alert-danger">
                Vous devez choisir le type de constat VT ou SGN
            </div>
            <div class="col-xs-12">
                <label class="btn btn-default btn-lg btn-default-step">
                    <input type="radio" name="type_vtsgn" ng-model="constat.type_vtsgn" ng-value="'VT'">&nbsp;&nbsp;VT
                </label>
                <label class="btn btn-default btn-lg btn-default-step">
                    <input type="radio"  name="type_vtsgn" ng-model="constat.type_vtsgn" ng-value="'SGN'">&nbsp;&nbsp;SGN
                </label>
            </div>
        </div>
    </div>
    <div class="row row-margin">
        <div class="col-md-6  col-xs-12" style="margin-bottom: 10px;">
            <a href="" ng-click="signature(constat, 'signature_' + keyConstatNode)" class="btn btn-default btn-lg col-xs-6 btn-block btn-upper link-to-section"><span class=" glyphicon glyphicon-ok-circle"></span> Approuver</a>
        </div>
        <div class="col-md-3 col-xs-12" style="margin-bottom: 10px;">
            <a href="" ng-click="refuserConfirmation(constat)" class="btn btn-danger btn-lg col-xs-6 btn-block btn-upper link-to-section"><span class="glyphicon glyphicon-remove-circle"></span> Refuser</a>
        </div>

        <div class="col-md-3 col-xs-12" style="margin-bottom: 10px;">
            <a href="" ng-click="assembleConfirmation(constat)" class="btn btn-warning btn-lg col-xs-6 btn-block btn-upper link-to-section"><span style="font-size: 18px; margin-right: 6px;" class="icon-mouts"></span>+&nbsp;&nbsp;<span style="font-size: 18px; margin-right: 6px;" class="icon-mouts"></span>Constats assemblés</a>
        </div>
    </div>
</div>
<div ng-show="active == 'refuser_confirmation'" class="form-horizontal">
    <div class="col-xs-12">
        <div class="col-xs-12">
            <p class="lead text-center"> 
                {{ constat.produit_libelle}} {{ constat.denomination_lieu_dit }}<br />
                <span ng-show="constat.type_vtsgn">{{ constat.type_vtsgn }}<br /></span>
                <span ng-show="constat.degre_potentiel_volume">{{ constat.degre_potentiel_volume }} degrés potentiels<br /></span>
                <span ng-show="constat.volume_obtenu !== null">{{ constat.volume_obtenu }} hl<br /></span>
            </p>
        </div>
        <div class="form-group">
            <label for="raison_refus_raisin_{{ keyConstatNode}}" class="col-xs-4 control-label lead">Raison du refus</label>
            <div class="col-xs-8">
                <select id="raison_refus_raisin_{{ keyConstatNode}}" ng-change="updateRaisonRefus(constat)" ng-model="constat.raison_refus" ng-options="raison_refus_key as raison_refus_libelle for (raison_refus_key, raison_refus_libelle) in raisons_refus" class="hidden-print form-control input-lg"></select>
            </div>
        </div>
    </div>

    <div class="row row-margin">
        <div class="col-xs-12">
            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                <a href="" ng-click="refuserConstatVolume(constat)" class="btn btn-default btn-lg col-xs-6 btn-block btn-upper link-to-section">Confirmer</a>
            </div>
            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                <a href="" ng-click="remplir(constat)" class="btn btn-danger btn-lg col-xs-6 btn-block btn-upper link-to-section">Annuler</a>
            </div>
        </div>
    </div>
</div>
<div ng-show="active == 'assemble_confirmation'" class="form-horizontal">
    <div class="col-xs-12">
        <div class="col-xs-12">
            <p class="lead text-center"> 
                {{ constat.produit_libelle}} {{ constat.denomination_lieu_dit }}<br />
                <span ng-show="constat.type_vtsgn">{{ constat.type_vtsgn }}<br /></span>
                <span ng-show="constat.degre_potentiel_volume">{{ constat.degre_potentiel_volume }} degrés potentiels<br /></span>
                <span ng-show="constat.volume_obtenu !== null">{{ constat.volume_obtenu }} hl<br /></span>
            </p>
        </div>

    </div>
    <div class="row row-margin">
        <div class="col-xs-12">
            <div class="col-xs-12">
                <p class="lead text-center"> 
                    <span style="font-size: 18px; margin-right: 6px;" class="icon-mouts"></span>+&nbsp;&nbsp;<span style="font-size: 18px; margin-right: 6px;" class="icon-mouts"></span>
                    Vous êtes sur le point de faire passer ce constat en assemblé. Il sera dès lors considéré comme non constatable et assemblé avec un autre produit.
                </p>
            </div>
        </div>
    </div>
    <div class="row row-margin">
        <div class="col-xs-12">
            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                <a href="" ng-click="remplir(constat)" class="btn btn-danger btn-lg col-xs-6 btn-block btn-upper link-to-section">Annuler</a>
            </div>
            <div class="col-xs-12 col-sm-6" style="margin-bottom: 10px;">
                <a href="" ng-click="assemblerConstatVolume(constat)" class="btn btn-default btn-lg col-xs-6 btn-block btn-upper link-to-section">Confirmer l'assemblage</a>
            </div>
        </div>
    </div>
</div>
<div id="signature_{{ keyConstatNode}}" ng-show="active == 'signature'" class="col-xs-12">
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-12">
              <div class="checkbox">
                <label>
                  <input ng-model="constat.papier" type="checkbox" ng-true-value="1" ng-false-value="0"> Constat saisi sur papier
                </label>
              </div>
            </div>
        </div>
        <div ng-show="!constat.papier">
        <label class="text-muted">Signature de l'opérateur : </label> <button class="btn btn-link btn-sm signature-pad-clear">(<span class="glyphicon glyphicon-trash"></span> Recommencer)</button>
        <div ng-class="{ 'hidden': !constat.erreurs['signature'] }" class="alert alert-danger">
            L'opérateur doit signer le constat
        </div>
        <div class="signature-pad well" style="width: 290px; padding: 5px;">
            <canvas style="width: 100%; height: 200px;" height="200"></canvas>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <label class="text-muted">Envoyer le constat à l'adresse email :</label>
                <input placeholder="Adresse email" ng-model="constat.email" type="email" class="form-control input-lg" ng-keydown="blurOnEnter($event)" ng-blur="blur()" />
            </div>
        </div>
        </div>
        <div class="row row-margin">
            <div class="col-xs-12">
                <a href="" ng-click="approuverConstatVolume(constat)" class="btn btn-default btn-lg col-xs-6 btn-block btn-upper link-to-section">Approuver et terminer</a>
            </div>
        </div>
    </div>
</div>
