<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Gestion des transaction.
 * Class Emissionsmodel
 */
class Emissionsmodel extends CI_Model {
    protected static $table = tblEmissions;
    protected static $balancesTable = tblBalances;

    /** Ajout d'une transaction
     * @param $data
     * @return bool
     */
    public function _newEmission($data) {
        if( $this->db->insert(self::$table, $data) ){
            return true;
        }else{
            # we put this $this->db->error(); on log
            return false;
        }
    }

    public function _insertBalance($userid, $deposit, $credit, $debit, $retirement){
        # userid, deposit, credit, debit, retirement
        $sql = "INSERT INTO ".self::$balancesTable."(userid, deposit, credit, debit, retirement) "
            ."VALUES('".$userid."', $deposit, $credit, $debit, $retirement)";

    }
}
