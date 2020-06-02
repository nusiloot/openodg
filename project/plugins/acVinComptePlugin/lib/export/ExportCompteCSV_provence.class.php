<?php

/**
 * Description of ExportParcellairePdf
 *
 * @author mathurin
 */
class ExportCompteCsv_provence implements InterfaceDeclarationExportCsv {

    protected $compte = null;
    protected $header = false;

    public static function getHeaderCsv() {

        return "numéro de compte;intitulé;type (client/fournisseur);abrégé;adresse;address complément;code postal;ville;pays;n° identifiant;n° siret;statut;téléphone;fax;email;site\n";
    }

    public function __construct($compte, $header = true) {
        $this->compte = $compte;
        $this->header = $header;
    }

    public function getFileName() {

        return $this->compte->_id . '_' . $this->compte->_rev . '.csv';
    }

    public function export() {
        $csv = "";
        if($this->header) {
            $csv .= self::getHeaderCsv();
        }

        $csv .= sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
                            $this->compte->getCodeComptable(),
                            $this->compte->nom_a_afficher,
                            "CLIENT",
                            $this->compte->nom_a_afficher,
                            $this->compte->adresse,
                            $this->compte->adresse_complementaire,
                            $this->compte->code_postal,
                            $this->compte->commune,
                            $this->compte->pays,
                            $this->compte->identifiant,
                            $this->compte->societe_informations->siret,
                            $this->compte->statut,
                            ($this->compte->telephone_bureau) ? $this->compte->telephone_bureau : $this->compte->telephone_mobile,
                            $this->compte->fax,
                            $this->compte->email,
                            "https://declaration.syndicat-cotesdeprovence.com/societe/".$this->compte->identifiant."/visualisation"
                          );

        return $csv;
    }

    protected function formatFloat($value) {

        return str_replace(".", ",", $value);
    }
}
