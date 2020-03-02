<?php

class ParcellaireAffectationProduitsForm extends acCouchdbObjectForm {

    public function configure() {
		foreach ($this->getObject()->declaration as $key => $value) {
			$this->embedForm($key, new ParcellaireAffectationProduitAffectesForm($value));
		}

        $this->widgetSchema->setNameFormat('parcelles[%s]');
    }


    protected function doUpdateObject($values) {
        parent::doUpdateObject($values);
        foreach ($values as $produit => $value) {
            if (!is_array($value)) continue;
            foreach ($value as $detail => $items) {
                $node = $this->getObject()->declaration->get($produit);
                $node = $node->detail->get($detail);
                foreach ($items as $k => $v) {
                    $node->add($k, $v);
                }
            }
        }
    }

}
