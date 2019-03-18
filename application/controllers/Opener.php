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
            $datamodel = array('token' => '', 'email' => '', 'phone_number' => '',
                'firstname' => $_REQUEST['firstname'],
                'lastname' => $_REQUEST['lastname'],
                'password' => sha1($_REQUEST['userpassword']));
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
            //print_r($registerHandler); die('back');
            if( true == is_array($registerHandler)) {
                $xs = $this->prolib::qrGenerate( $registerHandler['userid'] );
                # on case of email or phone number
                switch ($credential[0]){
                    case 'phone':
                        # send sms
                        break;
                    case 'email':
                        # send email
                        echo $this->prolib::sendMail($registerHandler['email'],
                            'Coinless - Activation',
                            $this->load->view('emails/register_confirmation',
                                $registerHandler, TRUE));
                        break;
                }
            } else {
                echo 'error on registering';
            }

        }

    }

    public function confirm($token, $credential) {
        echo $token.' - '.$credential;
    }

    /** We'll check if credential already in DB.
     * @param $credential
     * @return bool
     */
    private function existingCredential($credential){
        return true;
    }


}