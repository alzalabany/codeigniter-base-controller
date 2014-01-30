<?php
if ( !defined( 'BASEPATH' ) )
    exit( 'No direct script access allowed' );
/**
 * A base controller for CodeIgniter 
 *
 * @link http://github.com/alzalabany
 * @copyright Copyright (c) 2014, alzalabany@gmail.com
 */

class MY_Controller extends CI_Controller {
    
    //Sections inside your base template.
    protected $body = 'base/body', $title = 'Smart HealtNile 1.0.0', //txt
        $js = array(), //filename
        $inline_js = '', //script
        $css = array(), $inline_css = '', //style
        $breadcrumb = FALSE, //<li><a>
        $content = array(), //html
        $noEcho = FALSE, $action_user = null, $onSuccess = null; //NOT USED
    
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler( 1 );
        //#listen for post attempts;
        $this->validate();
        
        //#set action_user; return null if no session else return user object
        $this->action_user = $this->session->userdata( 'action_user' );
        
        //#extra check step
        if ( $this->user->pwd_has_changed( $this->action_user ) ) {
            $this->session->sess_destroy();
            alerts( 'error', 'the password you used to login has changed ! please relogin' );
            return $this->failed_login();
        } //$this->user->pwd_has_changed( $this->action_user )
        
        //DEFAULTS
        $this->out( $this->router->fetch_class(), 'page_title' ); //set section page_title = current class
        $this->_asset( 'jquery-2.0.3.min.js' );
        
        
    }
    public function _set( $var, $value ) {
        if ( isset( $this->$var ) )
            $this->$var = $value;
        return $this;
    }
    public function alerts() {
        return die( json_encode( alerts() ) );
    }
    public function logout() {
        $this->session->sess_destroy();
        redirect();
    }
    
    
    //AUTH
    private function failed_login() {
        $callers = debug_backtrace();
        echo '<br/>';
        alerts( 'warning', 'failed login' . ' ' . $callers[ 1 ][ 'function' ] );
        //ob_clean();//clear flush just to make sure !
        $this->content = array();
        if ( $this->input->is_ajax_request() )
            $this->outv( 'base/ajax/landing' )->_flush();
        else
            $this->outv( 'base/landing' )->_flush();
        
        die( $this->output->get_output() );
        //kill request and load landing in same uri. 
        //this in case he attempt login again he will be at same url; also helps with partials views
    }
    private function success_login() {
        unset( $_POST[ 'login' ], $_POST[ 'password' ] );
    }
    private function validate() {
        if ( !$this->input->post( 'login' ) || !$this->input->post( 'password' ) )
            return FALSE;
        //$this->session->sess_destroy();#destroy session
        //1. validation
        $this->form_validation->set_rules( 'login', 'User Login', 'required|min_length[4]|max_length[12]|xss_clean' );
        $this->form_validation->set_rules( 'password', 'Password', 'required|min_length[4]|max_length[12]|xss_clean' );
        
        //1.2 Failed validation
        if ( !$this->form_validation->run() )
            return alerts( 'error', $this->form_validation->error_string(), false );
        
        //2. Login
        $this->user->login( set_value( 'login' ), set_value( 'password' ) );
    }
    public function auth( $role = null ) {
        $filter = array(
             'alerts',
            'logout' 
        ); //skip access validation for logout and alerts
        if ( in_array( $this->router->fetch_method(), $filter ) )
            return FALSE;
        
        if ( !isset( $this->action_user->id ) )
            return alerts( 'error', "this is a users restricted area", $this->failed_login() );
        
        //ACCESS LEVELS CONDITIONS
        if ( $this->user->in_group( $this->action_user->id, $role ) )
            return $this->success_login();
        else
            return alerts( 'error', "this is a {$role} restricted area", $this->failed_login() );
    }
    //should never be called before $this->validate();
    //END AUTH
    
    //#TEMPLATE SETTERS
    function _title( $txt ) {
        $this->title = $txt;
        return $this;
    }
    protected function out( $txt = '', $section = 'content' ) {
        if ( !isset( $this->content[ $section ] ) )
            $this->content[ $section ] = array();
        $this->content[ $section ][] = $txt;
        return $this;
    }
    protected function outv( $view, $data = array(), $section = 'content' ) {
        if ( !isset( $this->content[ $section ] ) )
            $this->content[ $section ] = array();
        $this->content[ $section ][] = $this->load->view( $view, $data, TRUE );
        return $this;
    }
    function _asset( $link, $txt = FALSE ) {
        
        if ( $txt !== FALSE ) {
            if ( $txt == 'js' )
                $this->inline_js[] = $txt;
            elseif ( $txt == 'css' )
                $this->inline_css[] = $txt;
            return $this;
        } //$txt !== FALSE
        else {
            if ( pathinfo( $link, PATHINFO_EXTENSION ) == 'css' )
                $this->css[] = link_tag( base_url( 'assets/css/' . trim( $link, "/\\" ) ) ); //css()/js() is @ helpers/html_helper
            else
                $this->js[] = '<script src="' . base_url( 'assets/js/' . trim( $link, "/\\" ) ) . '"></script>';
        }
        return $this;
    }
    function _bread( $li, $html = FALSE ) {
        //hide breadcrumb
        if ( $li == FALSE ) {
            $this->breadcrumb = FALSE;
            return $this;
        } //$li == FALSE
        
        if ( $html ) {
            $this->breadcrumb[] = $li;
        } //$html
        else {
            if ( is_array( $li ) )
                foreach ( $li as $l )
                    $this->breadcrumb[] = '<li>' . $l . '</li>';
            else
                $this->breadcrumb[] = '<li>' . $li . '</li>';
        }
        
        return $this;
    }
    //#END TEMPLATE SETTERS
    
    //flush output
    function _flush( $protocol = 'html' ) {
        if ( $this->noEcho )
            ob_clean();
            
        if ( $protocol == 'json' )
            return $this->output->set_content_type( 'application/json' )->set_output( json_encode( $this->content[ 'json' ] ) );
        
        $data[ 'title' ] = $this->title;
        
        $data[ 'css' ]        = is_array( $this->css ) ? implode( "\n", $this->css ) : 'NO CSS';
        $data[ 'js' ]         = is_array( $this->js ) ? implode( "\n", $this->js ) : 'NO JS';
        $data[ 'inline_css' ] = ( $this->inline_css ) ? '<style>' . implode( "\n", $this->inline_css ) . '</style>' : '';
        $data[ 'inline_js' ]  = ( $this->inline_js ) ? implode( "\n", $this->inline_js ) : '';
        
        $data[ 'breadcrumb' ] = is_array( $this->breadcrumb ) ? implode( "\n", $this->breadcrumb ) . "\n" : $this->breadcrumb;
        
        foreach ( $this->content as $section => $content ) {
            $data[ $section ] = is_array( $content ) ? implode( "\n\n\n ", $content ) : $content;
        } //$this->content as $section => $content
        
        if ( $this->body ) {
            $this->load->view( $this->body, $data );
        } //$this->body
        else {
            //debuging data sent to view
            echo '<pre>' . html_escape( print_r( $data, TRUE ) ) . "</pre>"; //@todo loop data and echo in divs
        }
        
    }
    
}
/*
End my controller
*/
