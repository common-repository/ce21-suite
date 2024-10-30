<?php
class session_helper_ce21{

    public $first_name;
    public $last_name;
    public $email;
    public $TenantId;
    const TIMEOUT = 3600; //in seconds
	public $CreatedTime;
    public $tenantId_ce21;

    public function __construct($tenantId){
        $this->tenantId_ce21 = $tenantId;

        if( self::session_exist($tenantId) ) {
            $this->first_name = sanitize_text_field($_SESSION['ce21_'.$this->tenantId_ce21.'_firstName']);
            $this->last_name = sanitize_text_field($_SESSION['ce21_'.$this->tenantId_ce21.'_lastName']);
            $this->email = sanitize_email($_SESSION['ce21_'.$this->tenantId_ce21.'_user_email']);
            $this->TenantId = sanitize_text_field($_SESSION['ce21_'.$this->tenantId_ce21.'_tenantId']);
        }
    }

    public function create_session($first_name, $last_name, $email, $TenantId, $CreatedTime ) {

        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->tenantId_ce21 = $TenantId;
        $this->CreatedTime = $CreatedTime;

        if (!isset($_SESSION['ce21_'.$this->tenantId_ce21.'_created'])) {
            $_SESSION['ce21_'.$this->tenantId_ce21.'_created'] = $this->CreatedTime;
        }

        $_SESSION['ce21_'.$this->tenantId_ce21.'_user_email']  =  $this->email;
        $_SESSION['ce21_'.$this->tenantId_ce21.'_tenantId']    =  $this->TenantId;
        $_SESSION['ce21_'.$this->tenantId_ce21.'_firstName']   =  $this->first_name;
        $_SESSION['ce21_'.$this->tenantId_ce21.'_lastName']    =  $this->last_name;

        setcookie('wordpress_no_cache', true, strtotime('+1 day'), COOKIEPATH, COOKIE_DOMAIN );
    }

    public function session_exist($tenantId) {
        $this->valid_session();
        if( isset($_SESSION['ce21_'.$tenantId.'_user_email']) ){
            $email      =  sanitize_email($_SESSION['ce21_'.$tenantId.'_user_email']);
            $first_name =  sanitize_text_field($_SESSION['ce21_'.$this->tenantId_ce21.'_firstName']);
            $last_name  =  sanitize_text_field($_SESSION['ce21_'.$this->tenantId_ce21.'_lastName']);
            $_SESSION['ce21_'.$this->tenantId_ce21.'_created'] = time();
            return true;
        } else {
            $email='';
            return false;
        }
    }

    public function valid_session() {
        if( isset($_SESSION['ce21_'.$this->tenantId_ce21.'_created']) ) {
            if (time() - $_SESSION['ce21_' . $this->tenantId_ce21 . '_created'] > self::TIMEOUT) {
                $this->unset_session();
            }
        }

    }

    public function unset_session() {
        unset ( $_SESSION['ce21_' . $this->tenantId_ce21 . '_user_email'] );
        unset ( $_SESSION['ce21_' . $this->tenantId_ce21 . '_tenantId'] );
        unset ( $_SESSION['ce21_' . $this->tenantId_ce21 . '_firstName'] );
        unset ( $_SESSION['ce21_' . $this->tenantId_ce21 . '_lastName'] );
        unset ( $_SESSION['ce21_' . $this->tenantId_ce21 . '_created'] );
        setcookie('wordpress_no_cache', null, strtotime('-1 day'), COOKIEPATH, COOKIE_DOMAIN );

    }

}