<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* Use is at the user's own risk, no guarantee for support, updates, code or security
* 2020 (Use mask)
*/

// $data        = array
// $where       = int || array || string ('all')
// $limit       = $limit (1) || $limit, $offset (1,0)
// $order_by    = array (['id' => 'ASC']) || string ('id ASC')

/**

- - - - - - - - - - - - - - - - - - - - - - - - 

YOUR CONSTRUCT MODEL EXAMPLE:
 
public function __construct() {
    $this->table                = 'table_name';
    $this->primary_key          = 'id';
    parent::__construct();
}

// Start other database connection with this (before parent)
* $this->database             = 'db_name';

- - - - - - - - - - - - - - - - - - - - - - - - 

Insert Data:
$this->name_model->insert(['name' => 'Teste']);

Get Row:
$this->name_model->get(1);
$this->name_model->get(['name' => 'Teste'], ['name' => 'DESC']);
$this->name_model->get(['name' => 'Teste'], ['name DESC']);

Get Result:
$this->name_model->get_all(['id >=', 2]);
$this->name_model->get_all(['id >=', 2], ['name' => 'DESC'], {limit}, {offset});
$this->name_model->get_all(false, ['name DESC'], {limit}, {offset});

Update Data:
$this->name_model->insert(['name' => 'Teste'], 1);
$this->name_model->insert(['name' => 'Teste'], ['id' => 1]);
$this->name_model->insert(['name' => 'Teste'], 'all');

Delete:
$this->name_model->delete(2);
$this->name_model->delete('all');
$this->name_model->delete(['id >=', 2]);

Delete (Soft) - Require "deleted_at" field:
$this->name_model->delete_soft(2);
$this->name_model->delete_soft('all');
$this->name_model->delete_soft(['id >=', 2]);

- - - - - - - - - - - - - - - - - - - - - - - - 

*/

class MY_Model extends CI_Model
{

    public $database            = FALSE;
    public $table               = FALSE;
    public $primary_key         = 'id';

    public $_timestamps         = TRUE;
    public $_timestamps_format  = 'Y-m-d H:i:s';

    public $_created_at         = 'created_at';
    public $_updated_at         = 'updated_at';
    public $_deleted_at         = 'deleted_at';

    public $_select             = '*';

    public $_result             = 'object'; //object or array

    public function __construct()
    {

        parent::__construct();
        $this->_db              = $this->private_set_conn();

    }

    /*
    */
    public function insert($data)
    {

        if( is_array($data) && count($data) > 0 ) {

            $this->private_timestamp(true, false, false);
            $this->_db->insert($this->table, $data);

            if( $this->_db->affected_rows() > 0 ) {
                return $this->_db->insert_id();
            } else return false;

        } else return false;

    }

    /*
    */
    public function get($where = false, $order_by = false)
    {

        $this->private_where($where);
        $this->private_order_by($order_by);
        $this->private_limit(1);
       
        return $this->private_get('row');

    }

    /*
    */
    public function get_all($where = false, $order_by = false, $limit = false, $offset = false)
    {

        $this->private_where($where);
        $this->private_order_by($order_by);
        $this->private_limit($limit, $offset);

        return $this->private_get('result');

    }

    /*
    */
    public function update($data, $where = false) {

        if( $this->private_where_count($where) ) {

            $this->private_where($where);

            $this->private_timestamp(false, true, false);
            $this->_db->update($this->table, $data);

            if( $this->_db->affected_rows() > 0 ) {
                return $this->_db->affected_rows();
            } else return false;

        }

    }

    /*
    */
    public function delete($where = false)
    {

        if( $this->private_where_count($where) ) {

            $this->private_where($where);

            $this->_db->delete($this->table);

            if( $this->db->affected_rows() > 0 ) {
                return $this->db->affected_rows();
            } else return false;

        } else return false;

    }

    /*
    */
    public function delete_soft($where = false)
    {

        if( $this->private_where_count($where) ) {

            $this->private_where($where);

            $this->private_timestamp(false, true, true);
            $this->_db->update($this->table);

            if( $this->db->affected_rows() > 0 ) {
                return $this->db->affected_rows();
            } else return false;

        } else return false;

    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
    //
    // P R I V A T E S
    //
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    public function private_get($type = 'result')
    {

        $this->_db->select($this->_select);
        $this->_db->from($this->table);

        $query = $this->_db->get();

        if( $query->num_rows() > 0 ) {

            switch ($this->_result) {
                case 'array':
                    return $type == 'row' ? $query->row_array() : $query->result_array();
                break;
                default:
                    return $type == 'row' ? $query->row() : $query->result();
                break;
            }

        } else return false;

    }

    /**
    * private function private_where
    * define where conditions
    */
    private function private_where($where)
    {

        if( is_numeric($where) && $where > 0 ) {

            return $this->_db->where($this->primary_key, $where);

        } else if( is_array($where) && count($where) > 0 ) {

            return $this->_db->where($where);

        } else return false;

    }

    /*
    * private function private_where_count
    * validate if where is array or numeric or is 'all'
    */
    private function private_where_count($where)
    {

        if( $where == 'all' || $where > 0 || (is_array($where) && count($where) > 0 )) {
            return true;
        } else return false;

    }

    /*
    * private function private_limit
    * set limit and offset to query
    */
    private function private_limit($limit, $offset = false)
    {

        if( $limit > 0 && $offset > 0 ) {

            return $this->_db->limit($limit, $offset);

        } else if( $limit > 0 ) {

            return $this->_db->limit($limit);

        } else return false;

    }

    /*
    * private function private_limit
    * set order by and offset to query
    */
    private function private_order_by($order_by)
    {

        if( is_array($order_by) && count($order_by) > 0 ) {

            foreach ($order_by as $key => $value)
                $this->_db->order_by($key, $value);

        } else if( is_string($order_by) ) {

            $this->_db->order_by($order_by);

        }

        return true;

    }

    /**
    * private function private_timestamp
    * set datetime field
    */
    private function private_timestamp($created_at, $updated_at, $deleted_at)
    {

        if( $this->_timestamps ) {

            $now = date($this->_timestamps_format);

            if( $created_at )
                $this->_db->set($this->_created_at, $now);

            if( $updated_at )
                $this->_db->set($this->_updated_at, $now);

            if( $deleted_at )
                $this->_db->set($this->_deleted_at, $now);

            return true;

        } else return false;

    }


    /**
     * private function private_set_conn()
     * Change and set a the connection to $this->db
     */
    private function private_set_conn()
    {

        if( isset($this->database) && $this->database ) {
            return $this->load->database($this->database, TRUE);
        } else {
            return $this->db;
        }

    }

}