<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* Use is at the user's own risk, no guarantee for support, updates, code or security
* 2020 (Use mask)
*/

class Log_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function _insert()
    {
        if (in_array($this->uri->segment(1), ['admin', 'dashboard', 'panel'])) {
            $admin = 1;
            $register = $this->uri->segment(4) > 0 ? $this->uri->segment(4) : null;
        } else {
            $admin = 0;
            $register = $this->uri->segment(3) > 0 ? $this->uri->segment(3) : null;
        }

        $insert['user_id'] = $this->id;
        $insert['admin'] = $admin;
        $insert['ip'] = $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0';
        $insert['controller'] = $this->router->class;
        $insert['function'] = $this->router->method;
        $insert['register'] = $register;
        $insert['user_agent'] = $this->user_agent();
        $insert['referer'] = $_SERVER['HTTP_REFERER'] ?? null;
        $insert['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('logs', $insert);
    }

    private function user_agent()
    {
        // import agent library in config/autoload.php
        //$this->load->library('agent');

        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser().' '.$this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $agent = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $agent = $this->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }

        return $agent;
    }
}

// DROP TABLE IF EXISTS `logs`;
// CREATE TABLE IF NOT EXISTS `logs` (
//  `id` int(11) NOT NULL AUTO_INCREMENT,
//  `user_id` int(11) DEFAULT NULL,
//  `admin` int(1) NOT NULL,
//  `ip` varchar(75) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//  `controller` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
//  `function` varchar(75) COLLATE utf8mb4_unicode_ci NOT NULL,
//  `register` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'unique_id from a register',
//  `referer` text COLLATE utf8mb4_unicode_ci,
//  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
//  `created_at` datetime NOT NULL,
//  PRIMARY KEY (`id`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
