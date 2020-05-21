<?php

/*
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* Use is at the user's own risk, no guarantee for support, updates, code or security
* 2020 (Use mask)
*/

class MY_Controller extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// define a timezone to your system (controller, model, view)
		$timezone 		= $this->config->item('time_reference') ?: 'America/Sao_Paulo';
		date_default_timezone_set($timezone);

		// import session library in autoload.php
		$this->id 		= $this->session->userdata('session');

		// import model Log in autoload.php
		//$this->log_model->insert();

	}

	/**
	 * return a clean json with http response
	 * @param success boolen
	 * @param message string with a response message
	 * @param code http header response
	 */
	protected function resp($success, $message = false, $code = 200) {

		http_response_code($code);

		print json_encode([
			'success'	=> $success ? true : false,
			'message'	=> $message,
			'datetime'	=> date('Y-m-d H:i:s')
		]);

		exit();

	}

	/**
	 * returns the view quickly and easily
	 * @param array with your data
	 * @param string with your default layout name
	 * @param string with a custom view name
	 */
	protected function load_view($data, $layout = 'layouts/default', $view = false) {

		// use this to create your view name like "yourController/yourFunction"
		if( !$view )
			$view 		= "{$this->router->class}/{$this->router->method}";

		// use $this->load->view($view)
		$data['view']	= $view;

		$this->load->view($layout, $data);

	}

}

/* CLASS - SESSION */
class MY_Admin extends MY_Controller {
    
    public function __construct() {
        parent::__construct();

        if( $this->id <= 0)
	        redirect( base_url('login') );

    }

}
	
/* CLASS - public */
class MY_Public extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        
    }

}