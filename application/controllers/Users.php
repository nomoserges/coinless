<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    public function index() {
        $this->load->view('welcome_message');
        //echo $this->prolib->coors();
    }

    /** Les informations du profil */
    public function profileData(){
        $this->prolib::jsonInput();
        $profileData = $this->usersmodel->findWithCredentials($_REQUEST['userid']);
        if(false == $profileData) {
            $this->prolib->jsonOutput('error', 'User data', 'Error on loading data profil');
        } else {
            $this->prolib->jsonOutput('info', '1', $profileData);
        }
    }

    public function updateProfile(){
        $this->prolib::jsonInput();
        # email, id_card, username, firstname, lastname, birth_date, gender, address, userid
        //print_r($_REQUEST); die('df');
        $updateProfile = $this->usersmodel->_updateProfile($_REQUEST);
        if(false == $updateProfile) {
            $this->prolib->jsonOutput('error', 'User data', 'Error on loading data profil');
        } else {
            $this->prolib->jsonOutput('info', '1', $updateProfile);
        }
    }

    /** Creer un abonnement.    */
    public function subscription(){
        $this->prolib::jsonInput();
        # userid, cost_value, num_days
        $data = array(
            'userid' => $_REQUEST['userid'],
            'cost_value' => $_REQUEST['cost_value'],
            'num_days' => $_REQUEST['num_days']
        );
        $qsd = $this->accountsmodel->_newSubscription($data);
        if ($qsd == false) {
            $this->prolib->jsonOutput('error', 'Subcription:Error on subscription', []);
        } else {
            $this->prolib->jsonOutput('info', 'Subscription valid', []);
        }
    }

    /** List of user's subscriptions.   */
    public function currentsubscription(){
        $this->prolib::jsonInput();
        # userid
        $qsd = $this->accountsmodel->_mySubscription($_REQUEST['userid']);
        if(false == $qsd) {
            $this->prolib->jsonOutput('error', 'User data', 'Error on loading data');
        } else {
            $this->prolib->jsonOutput('info', 'Current subscription', $qsd);
        }
    }

    /** Make a deposit. */
    public function deposit(){
        $this->prolib::jsonInput();
        # userid, amount, funds_origin, description
        $data = array(
            'type_operation' =>   'deposit',
            'funds_origin' =>   $_REQUEST['funds_origin'],
            'user_from' => $_REQUEST['userid'],
            'user_to' => $_REQUEST['userid'],
            'amount' => $_REQUEST['amount'],
            'description' => $_REQUEST['description']
        );
        $qsd = $this->emissionsmodel->_newEmission($data);
        if(false == $qsd) {
            $this->prolib->jsonOutput('error', 'User data', 'Error on loading data');
        } else {
            $this->prolib->jsonOutput('info', 'Deposit', []);
        }
    }

    /** Make a deposit. */
    public function payment(){
        $this->prolib::jsonInput();
        # userid, amount, funds_origin, description
        $data = array(
            'type_operation' =>   'payment',
            'funds_origin' =>   $_REQUEST['funds_origin'],
            'user_from' => $_REQUEST['userid'],
            'user_to' => $_REQUEST['userid_to'],
            'amount' => $_REQUEST['amount'],
            'description' => $_REQUEST['description']
        );
        $qsd = $this->emissionsmodel->_newEmission($data);
        if(false == $qsd) {
            $this->prolib->jsonOutput('error', 'Payment', 'Error on loading data');
        } else {
            $this->prolib->jsonOutput('info', 'Payment', []);
        }
    }
}
