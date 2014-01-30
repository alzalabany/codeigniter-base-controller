<?php
if ( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );

class User extends CI_Model {
    protected $action_user_id = 1, $table = 'users';
    private $cached_users = array();
    
    public function __construct( $id = null ) {
        parent::__construct();
    }
    
    public function login( $u, $p ) {
        if ( !$u || !$p )
            return alerts( 'error', 'are you kidding me ? how did u do it :D !!', false );
        
        $query = $this->db->get_where( $this->table, array(
             'login' => $u 
        ), 1 );
        
        if ( $query->num_rows() == 0 )
            return alert( 'error', "ERROR OMG THE USER NAME DOESNOT EXIST :(,,RUNN", false );
        
        $query = $query->first_row();
        
        $hashedpwd = md5( $p . $query->salt );
        
        if ( $query->password != $hashedpwd )
            return alerts( 'error', "OH NO MR. THIS IS INCORRECT PASSWORD<br/>", false );
        
        //#USER HAS LOGED IN ;;;DO WT EVER U LIKE TO DO NOW;;
        
        $this->session->set_userdata( 'action_user', $query ); //SAVE HIM TO SESSION
        
        //#UPDATE USER LOGIN TIME
        
        return alerts( 'success', "success login for {$query->login} @ date('Y-m-d H:i:s')", true );
        
    }
    
    public function in_group( $obj, $role ) {
        return true;
    }
    
    public function pwd_has_changed() {
        return FALSE;
    }
    
    public function delete() {
    }
    
}
/*========
End model/User.php
========*/
