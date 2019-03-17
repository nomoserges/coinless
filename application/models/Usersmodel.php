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
            $sql = "SELECT userid FROM ". self::$usersTable ." WHERE userid = '".$newUserID."' LIMIT 1";
            $query = $this->db->query( $sql );
            # Check isFind
            if( intval($query->num_rows()) > 0 ) {
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

    public function _register($data) {
        $arr = array('userid' => self::generateUserID());
        $data = array_merge($data, $arr);
        if( $this->db->insert(tblUsers, $data) ){
            unset($data['password']);
            return $data;
        }else{
            # we put this $this->db->error(); on log
            return false;
        }
    }

}