<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opener extends CI_Controller {


    public function index(){
        $credential = [];
        # we check if the credential is an email or phone number
        if( false === $this->prolib->checkEmail($_REQUEST['credential']) ){
            $cx = $this->prolib->sanitizePhoneNumber($_REQUEST['credential']);
            if( 'invalid' !== $cx ) { $credential = array('phone', $cx); }
            else {  $credential = array('invalid', 'invalid'); }
        } else {    $credential = array('email', $_REQUEST['credential']);  }
        # phone or email is ok
        if( $credential[0] !== 'invalid' ){
            # we check password
            # create array of data model

            switch ($credential[0]){
                case 'phone':
                    $token = random_string('numeric', 6);
                    $email = '';
                    $phone_number = $credential[1];
                    break;
                case 'email':
                    $token = random_string('alnum', 32);
                    $email = $credential[1];
                    $phone_number = '';
                    break;
            }
            # we prepare data for model
            $datamodel = array(
                'email' => $email
            );
        }
        print_r($credential);
        if( $credential[0] == 'phone' ) {
            $token = random_string('numeric', 6);
        } elseif ( $credential[0] == 'email') {
            $token = random_string('alnum', 32);
        }

    }

    /** We'll check if credential already in DB.
     * @param $credential
     * @return bool
     */
    private function existingCredential($credential){
        return true;
    }


}