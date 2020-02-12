<?php

class ParcellaireAffectationEtapes extends Etapes {

	const ETAPE_EXPLOITATION = 'exploitation';
    const ETAPE_DENOMINATIONS = 'denominations';
    const ETAPE_AFFECTATIONS = 'affectations';
    const ETAPE_VALIDATION = 'validation';

    private static $_instance = null;
    public static $etapes = array(
        self::ETAPE_EXPLOITATION => 1,
        self::ETAPE_DENOMINATIONS => 2,
        self::ETAPE_AFFECTATIONS => 3,
        self::ETAPE_VALIDATION => 4
    );

    public static $links = array(
        self::ETAPE_EXPLOITATION => 'parcellaireaffectation_exploitation',
        self::ETAPE_DENOMINATIONS => 'parcellaireaffectation_denominations',
        self::ETAPE_AFFECTATIONS => 'parcellaireaffectation_affectations',
        self::ETAPE_VALIDATION => 'parcellaireaffectation_validation'
    );

    public static $libelles = array(
        self::ETAPE_EXPLOITATION => 'Exploitation',
        self::ETAPE_DENOMINATIONS => 'Sélection des dénominations complémentaires',
        self::ETAPE_AFFECTATIONS => 'Affectation des dénominations complémentaires',
        self::ETAPE_VALIDATION => 'Validation'
    );

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ParcellaireAffectationEtapes();
        }
        return self::$_instance;
    }

    public function getEtapesHash() {
        return self::$etapes;
    }

    public function getRouteLinksHash() {
        return self::$links;
    }

    public function getLibellesHash() {
        return self::$libelles;
    }

}
