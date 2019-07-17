<?php

class DRevSecurity extends DocumentSecurity implements SecurityInterface {

    protected $doc;
    protected $user;

    public static function getInstance($user, $doc) {

        return new DRevSecurity($user, $doc);
    }

    public function isAdmin() {
	
	return $this->user->hasTeledeclarationDrevAdmin() || parent::isAdmin();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        $authorized = parent::isAuthorized($droits);

        if(!$authorized) {

            return false;
        }

        if(in_array(self::VALIDATION_ADMIN, $droits) && !$this->doc->hasCompleteDocuments()) {

            //return false;
        }

        return true;
    }

}
