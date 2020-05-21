<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* 2020 (Use mask)
*/

class Webmail {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function send($to, $subject, $view, $data) {

		if( filter_var($to, FILTER_VALIDATE_EMAIL) ) {

			$data['to']			= $to;
			$data['subject']	= $subject;
			$html 				= $this->CI->load->view($view, $data, true);

			$config = [
			    'protocol' 	=> 'smtp',
			    'smtp_host' => '',
			    'smtp_port' => 465,
			    'smtp_user' => '',
			    'smtp_pass' => '',
			    'mailtype'  => 'html', 
			    'charset'   => 'utf-8',
			    'crlf' 		=> "\r\n",
  				'newline' 	=> "\r\n"
			];

			$this->CI->load->library('email', $config);

			$this->CI->email->from('your@email.com', 'Your Name');
			$this->CI->email->to($to);
			$this->CI->email->subject($subject);
			$this->CI->email->	message($html);

			return $this->CI->email->send();

		} else return false;

	}

}