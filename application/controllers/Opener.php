<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opener extends CI_Controller {


    public function index(){
        # do stuff here.
        echo 'bonjour';
    }

    public function register(){
        # credential, lastname, firstname, userpassword, gender
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
            $datamodel = array('token' => '', 'email' => '', 'phone_number' => '',
                'firstname' => $_REQUEST['firstname'],
                'lastname' => $_REQUEST['lastname'],
                'gender' => $_REQUEST['gender'],
                'password' => sha1($_REQUEST['userpassword']));
            # on test la valeur du credential pour renseigner l'email ou le telephone
            switch ($credential[0]){
                case 'phone':
                    $datamodel['token'] = random_string('numeric', 3).'-'.random_string('numeric', 3);
                    $datamodel['email'] = '';
                    $datamodel['phone_number'] = $credential[1];
                    break;
                case 'email':
                    $datamodel['token'] = random_string('alnum', 32);
                    $datamodel['email'] = $credential[1];
                    $datamodel['phone_number'] = '';
                    break;
            }
            # we prepare data for model
            $registerHandler = $this->usersmodel->_register($datamodel);
            # si l'inscription est passee, on obtient le tableau des infos
            if( true == is_array($registerHandler)) {
                # on case of email or phone number
                switch ($credential[0]){
                    case 'phone':
                        # send sms information.
                        $this->prolib::jsonOutput('info', 'Confirmer inscription avec le code envoyé', $registerHandler);
                        break;
                    case 'email':
                        # send email
                        echo $this->prolib::sendMail($registerHandler['email'],
                            'Coinless - Activation',
                            $this->load->view('emails/register_confirmation',
                                $registerHandler, TRUE));
                        $this->prolib::jsonOutput('info', 'Confirmer inscription avec le mail envoyé', $registerHandler);
                        break;
                }
            } else {
                echo 'error on registering';
                $this->prolib::jsonOutput('error', "Echec de l'inscription. Veuillez ré-essayer", []);
            }

        }
    }

    /** Confirm of registration */
    public function confirm() {
        # token, credential
        $xcv = $this->usersmodel->_confirm($_REQUEST);
        # Une erreur lors de la confirmation
        if( false == $xcv ){
            $this->prolib::jsonOutput('error', 'Echec de la confirmation', []);
        } else {
            # tout est ok.
            $this->prolib::jsonOutput('info', '1', $xcv);
        }

    }

    /** Login.  */
    public function login() {
        # credential, password
        $wxc = $this->usersmodel->findWithCredentials($_REQUEST['credential'], true, $_REQUEST['password']);
        if( false === $wxc ) {
            $this->prolib::jsonOutput('error', 'Echec de la connexion', []);
        } else {
            $this->prolib::jsonOutput('info', '1', $wxc);
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