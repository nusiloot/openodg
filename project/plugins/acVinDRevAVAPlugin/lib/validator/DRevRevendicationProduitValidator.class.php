<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DRevRevendicationProduitValidator
 *
 * @author mathurin
 */
class DRevRevendicationProduitValidator extends sfValidatorSchema {

    public function configure($options = array(), $messages = array()) {
        $this->addMessage('invalid_volume', '[key]Vous devez saisir la superficie et le volume pour vos produits revendiqués.');
        $this->addMessage('invalid_superficie', '[key]Vous devez saisir la superficie et le volume pour vos produits revendiqués.');
    }

    protected function doClean($values) {

        $errorSchema = new sfValidatorErrorSchema($this);
        $produits = $values['produits'];
        if(!is_array($produits)) {
            if (count($errorSchema)) { 
                throw new sfValidatorErrorSchema($this, $errorSchema);
            }
            return $values;
        }
        foreach ($produits as $key => $produit) {
            if (is_array($produit) && array_key_exists('superficie_revendique', $produit)) {
                if (is_null($produit['superficie_revendique']) && !is_null($produit['volume_revendique'])) {
                    $this->setMessage('invalid_superficie', str_replace('[key]', '['.$key.'][superficie_revendique]', $this->getMessage('invalid_superficie')));
                    $errorSchema->addError(new sfValidatorError($this, 'invalid_superficie'), 'superficie_revendique');
                    throw new sfValidatorErrorSchema($this, $errorSchema);
                    return $values;
                }
                if (!is_null($produit['superficie_revendique']) && is_null($produit['volume_revendique'])) {
                    $this->setMessage('invalid_volume', str_replace('[key]', '['.$key.'][volume_revendique]', $this->getMessage('invalid_volume')));
                    $errorSchema->addError(new sfValidatorError($this, 'invalid_volume'), 'volume_revendique');
                    throw new sfValidatorErrorSchema($this, $errorSchema);
                    return $values;
                }
            } else {
                if (array_key_exists('volume_revendique', $produit) && is_null($produit['volume_revendique'])) {  
                    $this->setMessage('invalid_volume', str_replace('[key]', '['.$key.'][volume_revendique]', $this->getMessage('invalid_volume')));
                    $errorSchema->addError(new sfValidatorError($this, 'invalid_volume'),'volume_revendique'); 
                    throw new sfValidatorErrorSchema($this, $errorSchema);
                    return $values;
                }
            }
        }
        if (count($errorSchema)) { 
            throw new sfValidatorErrorSchema($this, $errorSchema);
        }
        return $values;
    }

}
