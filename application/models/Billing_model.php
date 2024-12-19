<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Billing_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function activate($id)
    {

        $this->db->where('client_user', $id);
        $this->db->update(db_prefix() . 'client_details', ['deactivate' => 0]);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deactivate($id)
    {

        $this->db->where('client_user', $id);
        $this->db->update(db_prefix() . 'client_details', ['deactivate' => 1]);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
