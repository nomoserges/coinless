<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usersmodel extends CI_Model {
    protected static $usersTable = tblUsers;

    private function generateUserID(){
        # $newUserID = random_string('numeric', 12).'-'.random_string('alnum', 32);
        # We keep the line above for important count of users.
        $newUserID = random_string('alnum', 32);
        $isFind = true;
        while ($isFind) {
            # Search the generate's ID in the table
            $sql = "SELECT userid FROM ".self::$usersTable." WHERE userid = ? LIMIT 1";
            $query = $this->db->query( $sql, $newUserID );
            # Check isFind
            if( $query->num_rows() > 0 ) {
                # code exist
                $isFind = true;
            }else {
                # this code do not exist
                $isFind = false;
                break;
            }
        }
        return $newUserID;
    }

    /** Registering
     * @param $data
     * @return array|bool
     */
    public function _register($data) {
        $userid = self::generateUserID();
        //$qrcode = $this->prolib::qrGenerate( $userid );
        $arr = array('userid' => $userid);
        $data = array_merge($data, $arr);
        if( $this->db->insert(self::$usersTable, $data) ){
            unset($data['password']);
            return $data;
        }else{
            # we put this $this->db->error(); on log
            return false;
        }
    }

    public function _confirm($data){
        $qsd = $data['credential'];
        # we check first if credential and token are in DB.
        $sql = "SELECT userid FROM ".self::$usersTable
            ." WHERE token = '".$data['token']."' AND ( userid = ? OR phone_number = ? OR email = ?) LIMIT 1";
        $query1 = $this->db->query($sql,
            array($qsd, $qsd, $qsd));
        if( $query1->num_rows() == 1){
            # we go to set is_activated to true.
            $updateQuery = "UPDATE ".self::$usersTable ." SET is_activated = 1, token='' WHERE userid = '".$data['credential']
                ."' OR email = '".$data['credential']."' OR phone_number = '".$data['credential']."' OR username = '".$data['credential']."' ";
            if( true === $this->db->query($updateQuery) ){
                # the update work properly : We create the qr code and return user's infos for localstorage.
                $dataX = $this->findWithCredentials($data['credential'], false, '');
                $this->_userQrCode($dataX);
                return $dataX;
            } else {   return false;   }
        } else {  return false;   }
    }

    /** Look credentials for user (login)
     * @param $credential
     * @param $withPassword: true or false
     * @param $password
     * @return mixed
     */
    public function findWithCredentials($credential, $withPassword, $password){
        # si la recherche se fait avec le mot de passe.
        $addPasswordCheck = ' ';
        if ($withPassword == true) {
            $addPasswordCheck = " AND password = '".sha1($password)."' ";
        }
        # we will set columns to avoid password and others
        $sql = "SELECT * "
            ."FROM ".self::$usersTable
            ." WHERE (userid = '".$credential."' OR username='".$credential."' OR email='".$credential
            ."' OR phone_number='".$credential."')".$addPasswordCheck."AND is_activated=1 LIMIT 1 ";
        $query = $this->db->query($sql);
        if ( false == $query ) {
            return $query;
        } else {
            return $query->row_array();
        }

    }

    public function _updateProfile($data){
        $updateQuery = "UPDATE ".self::$usersTable
            ." SET username = '".$data['username'].
            "', firstname = '".$data['firstname'].
            "', lastname = '".$data['lastname'].
            "', birth_date = '".$data['birth_date'].
            "', gender = '".$data['gender'].
            "', email = '".$data['email'].
            "', id_card = '".$data['id_card'].
            "', address = '".$data['address'].
            "' WHERE userid = '".$data['userid']."' AND is_activated=1 LIMIT 1 ";
        $wxc =$this->db->query($updateQuery);
        if ( false === $wxc ) {
            return $wxc;
        } else {
            $dataX = $this->findWithCredentials($data['userid'], false, '');
            $this->_userQrCode($dataX);
            return $dataX;
        }
    }

    /** We generate user QRCode on every update of account
     * @param $userData
     */
    public function _userQrCode($userData){
        $qrData = json_encode(array(
            'userid'=>$userData['userid'],
            'username'=>$userData['username'],
            'email'=>$userData['email'],
            'phone_number'=>$userData['phone_number'],
            'firstname'=>$userData['firstname'],
            'lastname'=>$userData['lastname'],
            'gender'=>$userData['gender'],
            'id_card'=>$userData['id_card'],
            'address'=>$userData['address']
        ));
        $this->prolib::qrGenerate($userData['userid'], $qrData );
    }

}