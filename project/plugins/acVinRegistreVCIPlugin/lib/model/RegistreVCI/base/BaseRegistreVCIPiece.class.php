<?php
/**
 * BaseRegistreVCIPiece
 * 
 * Base model for RegistreVCIPiece

 * @property string $identifiant
 * @property string $date_depot
 * @property string $libelle
 * @property string $mime
 * @property integer $visibilite
 * @property string $source
 * @property acCouchdbJson $fichiers

 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getDateDepot()
 * @method string setDateDepot()
 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getMime()
 * @method string setMime()
 * @method integer getVisibilite()
 * @method integer setVisibilite()
 * @method string getSource()
 * @method string setSource()
 * @method acCouchdbJson getFichiers()
 * @method acCouchdbJson setFichiers()
 
 */

abstract class BaseRegistreVCIPiece extends Piece {
                
    public function configureTree() {
       $this->_root_class_name = 'RegistreVCI';
       $this->_tree_class_name = 'RegistreVCIPiece';
    }
                
}