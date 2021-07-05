<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* Created by github.com/matmper
* Permission to copy, use and edit is free, but change the names and credits when you do this
* Use is at the user's own risk, no guarantee for support, updates, code or security
* 2020 (Use mask)
*/

// $data        = array
// $where       = int || array || string ('all')
// $limit       = $limit (1) || $limit, $offset (1,0)
// $orderBy     = array (['id' => 'ASC']) || string ('id ASC')

/*

YOUR CONSTRUCT MODEL EXAMPLE:

public function __construct() {
    $this->database = 'default';
    $this->table = 'table_name';
    $this->primaryKey = 'id';
    $this->softDelete = true; // default is false
    parent::__construct();
}

- - - - - - - - - - - - - - - - - - - - - - - -

Insert Data:
$this->name_model->insert(['name' => 'Teste']);

Get Row:
$this->name_model->first();
$this->name_model->first(1);
$this->name_model->first(['name' => 'Teste'], ['name' => 'DESC']);
$this->name_model->first(['name' => 'Teste'], ['name DESC']);

Get Result:
$this->name_model->get();
$this->name_model->get(['id >=', 2]);
$this->name_model->get(['id >=', 2], ['name' => 'DESC'], {limit}, {offset});
$this->name_model->get(false, ['name DESC'], {limit}, {offset});

Update Data:
$this->name_model->insert(['name' => 'Teste'], 1);
$this->name_model->insert(['name' => 'Teste'], ['id' => 1]);
$this->name_model->insert(['name' => 'Teste'], 'all');

Delete - Use soft delete if "$this->_softDelete = true":
$this->name_model->delete(2);
$this->name_model->delete('all');
$this->name_model->delete(['id >=', 2]);

Delete - Delete forever from yout database
$this->name_model->delete_hard(2);
$this->name_model->delete_hard('all');
$this->name_model->delete_hard(['id >=', 2]);

- - - - - - - - - - - - - - - - - - - - - - - -

*/

class MY_Model extends CI_Model
{
    protected ?string $database = null;
    protected ?string $table = null;
    protected ?string $primaryKey = 'id';

    protected bool $_timestamps = true;
    protected bool $_softDelete = false;
    protected string $_timestampsFormat = 'Y-m-d H:i:s';

    protected string $_createdAt = 'created_at';
    protected string $_updatedAt = 'updated_at';
    protected string $_deletedAt = 'deleted_at';

    protected string $_select = '*';

    protected string $_result = 'default'; // default, object or array

    public function __construct()
    {
        parent::__construct();
        $this->_db = $this->private_set_conn();
    }

    /**
     * Get a row
     *
     * @param mixed $where
     * @param mixed $orderBy
     * @return null|object|array
     */
    public function first($where = null, $orderBy = null)
    {
        $this->private_where($where);
        $this->private_order_by($orderBy);
        $this->private_limit(1);
       
        return $this->private_get('row');
    }

    /**
     * Get many rows
     *
     * @param mixed     $where
     * @param mixed     $orderBy
     * @param int|null  $limit
     * @param int|null  $offset
     * @return null|object|array
     */
    public function get($where = null, $orderBy = null, ?int $limit = null, ?int $offset = null)
    {
        $this->private_where($where);
        $this->private_order_by($orderBy);
        $this->private_limit($limit, $offset);

        return $this->private_get('result');
    }

    /**
     * Insert a new row
     *
     * @param array $data
     * @return integer|null
     */
    public function insert(array $data): ?int
    {
        if (empty($data)) {
            return null;
        }

        $this->private_timestamp(true, false, false);
        $this->_db->insert($this->table, $data);

        if ($this->_db->affected_rows() > 0) {
            return $this->_db->insert_id();
        }

        return null;
    }

    /**
     * Update rows
     *
     * @param array $data
     * @param mixed $where
     * @return integer|null
     */
    public function update(array $data, $where): ?int
    {
        $this->private_where_validate($where);

        $this->private_where($where);

        $this->private_timestamp(false, true, false);

        $this->_db->update($this->table, $data);

        return $this->_db->affected_rows() ?: null;
    }

    /**
     * Delete a row (can use soft delete)
     *
     * @param mixed $where
     * @return int|null
     */
    public function delete($where): ?int
    {
        if ($this->_softDelete === false) {
            return $this->delete_hard($where);
        }

        $this->private_where_validate($where);
        
        $this->private_where($where);

        $this->private_timestamp(false, false, true);

        $this->_db->update($this->table);

        return $this->_db->affected_rows() ?: null;
    }

    /**
     * Delete a row forever
     *
     * @param mixed $where
     * @return int|null
     */
    public function delete_hard($where): ?int
    {
        $this->private_where_validate($where);

        $this->private_where($where);

        $this->_db->delete($this->table);

        return $this->_db->affected_rows() ?: null;
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    //
    // P R I V A T E S
    //
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    private function private_get(string $type = 'result')
    {
        $this->__type = $type;

        $this->__query = $this->_db->select($this->_select)
            ->from($this->table)
            ->get();

        return $this->private_get_type();
    }

    /**
     * Return query result (object or array)
     *
     * @return object|array
     */
    private function private_get_type()
    {
        $return = $this->__type === 'row'
            ? $this->__query->row()
            : $this->__query->result();

        switch ($this->_result) {
            case 'array':
                $return = json_decode(json_encode($return), true);
                // $return = $this->__type === 'row'
                //     ? $this->__query->row_array()
                //     : $this->__query->result_array();
                break;
            case 'object':
                $return = (object) $return;
                // $return = $this->__type === 'row'
                //     ? $this->__query->row_object()
                //     : $this->__query->result_object();
                break;
        }

        if ($this->__type === 'row' && empty($return)) {
            return null;
        }

        return $return;
    }

    /**
     * Validate where conditions
     *
     * @param mixed $where
     * @return object|null
     */
    private function private_where($where): ?object
    {
        if ($this->_softDelete !== false) {
            $this->_db->where("{$this->_deletedAt} IS NULL", null, false);
        }

        if (is_int($where) && !empty($where)) {
            return $this->_db->where($this->primaryKey, $where);
        }
        
        if (is_array($where) && !empty($where)) {
            return $this->_db->where($where);
        }

        return null;
    }

    /**
     * Validate if where is valid and can be used
     *
     * @param mixed $where
     * @return boolean
     */
    private function private_where_validate($where): bool
    {
        if (!empty($where) && ($where === 'all' || is_int($where) || is_array($where))) {
            return true;
        }

        return $this->set_error('The "where" conditions provided are invalid', true);
    }

    /**
     * private function private_limit to set limit and offset to query
     *
     * @param integer|null $limit
     * @param integer|null $offset
     * @return object|null
     */
    private function private_limit(?int $limit, ?int $offset = null): ?object
    {
        if ($limit > 0) {
            return $this->_db->limit($limit, $offset);
        }

        return null;
    }

    /**
     * Private function private_limit to set order by and offset to query
     *
     * @param array|string $orderBy
     * @return object|null
     */
    private function private_order_by($orderBy): ?object
    {
        if (is_array($orderBy) && !empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $this->_db->order_by($key, $value);
            }
        }
        
        if (is_string($orderBy) && !empty($orderBy)) {
            return $this->_db->order_by($orderBy);
        }

        return null;
    }

    /**
     * Private function private_timestamp to set datetime field
     *
     * @param boolean $created_at
     * @param boolean $updated_at
     * @param boolean $deleted_at
     * @return void
     */
    private function private_timestamp(bool $created_at, bool $updated_at, bool $deleted_at): void
    {
        if ($this->_timestamps) {
            $now = date($this->_timestampsFormat);

            if ($created_at) {
                $this->_db->set($this->_createdAt, $now);
            }

            if ($updated_at) {
                $this->_db->set($this->_updatedAt, $now);
            }

            if ($this->_softDelete !== false && $deleted_at) {
                $this->_db->set($this->_deletedAt, $now);
            }
        }
    }

    /**
     * Set a database connection
     *
     * @return void
     */
    private function private_set_conn(): object
    {
        if (isset($this->database)) {
            return $this->load->database($this->database, true);
        }
        
        return $this->db;
    }

    /**
     * Explode a exception
     *
     * @param string    $message
     * @param boolean   $forceException
     * @return Exception|null
     */
    private function set_error(string $message, bool $forceException = false): ?Exception
    {
        if (ENVIRONMENT === 'development' || $forceException !== false) {
            throw new Exception($message);
        }

        return null;
    }

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    //
    // H E L P E R S
    //
    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    /**
     * Prepare data to select specific fields
     *
     * @param string $value
     * @return object
     */
    public function select(string $value = '*'): object
    {
        $this->_select = $value ?: $this->_select;
        return $this;
    }

    /**
     * Prepare data to select specific fields
     *
     * @param string $value
     * @return object
     */
    public function result(string $value = 'default'): object
    {
        if (!in_array($value, ['default', 'object', 'array'])) {
            return $this->set_error('Your result type must be "object" or "array"');
        }

        $this->_result = $value;
        return $this;
    }
}
