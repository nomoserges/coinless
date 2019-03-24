<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Accountsmodel: Gestion des abonnements.
 */
class Accountsmodel extends CI_Model {
    protected static $table = tblAccounts;

    /** Nouvel abonnement.  */
    public function _newSubscription($data){
        # on regarde s'il y'a un abonnement en cours
        # on recupere le nombre de jours restant.
        $current = $this->_mySubscription($data['userid']);
        $data['prev_num_days'] = 0;
        if( is_array($current) ){
            $data['prev_num_days'] = $current['remaining_days'];
        }
        # on ferme d'abord tous les abonnements precedents
        if( $this->db->query("UPDATE ".self::$table." SET reg_status = 0 WHERE userid='".$data['userid']."' ") ){
            if( $this->db->insert(self::$table, $data) ){
                return true;
            }else{
                # we put this $this->db->error(); on log
                return false;
            }
        } else {    return false;   }
    }

    /** Liste des abonnements utilisateur
     * @param $userid: id utilisateur.
     * @return mixed.
     */
    public function _mySubscription($userid) {
        $sql = "SELECT userid, prev_num_days, num_days, total_days, datediff(ended_at, now()) AS remaining_days,"
            ."created_at, ended_at "
            ."FROM ".self::$table." WHERE userid='$userid' AND reg_status = 1 "
            ."ORDER BY created_at LIMIT 1";
        $query = $this->db->query($sql);
        if ( false == $query ){ return $query;  }
        else {    return $query->row_array();   }
    }

    /** Les abonnements encours.    */
}
