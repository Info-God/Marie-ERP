<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Saleschannel_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_saleschannel($data)
    {

        if (!empty($data)) {
            $insert = [
                "name" => $data['name'],
                "description" => $data['description'],
            ];

            $name = $data['name'];
            $description = $data['description'];

            $repeated = $this->check_saleschannel($name, $description);

            if ($repeated == false) {
                $this->db->insert(db_prefix() . 'restaurant_reach', $insert);
                if ($this->db->affected_rows() > 0) {

                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = 'New Saleschannel Created';
                    return $data;
                } else {
                    return false;
                }
            }
            $data['alert'] = 'Saleschannel Already Exists';
            return $data;
        }
        return false;
    }

    public function update_saleschannel($data, $id)
    {

        if (!empty($data)) {
            $update = array(
                "name" => $data['name'],
                "description" => $data['description'],
            );

            $id = $this->input->post('id');

            $name = $data['name'];
            $description = $data['description'];

            $repeated = $this->check_saleschannel($name, $description);

            if ($repeated == false) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'restaurant_reach', $update);
                if ($this->db->affected_rows() > 0) {
                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = 'Sales Channel updated';
                    return $data;
                } else {
                    return false;
                }
            }
            $data['alert'] = 'Saleschannel Already Exists';
            return $data;
        }
        return array('success' => false, 'alert' => 'Failed to update sales channel.');
    }

    public function delete_saleschannel($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . 'restaurant_reach');

        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    public function check_saleschannel($name, $description)
    {
        $this->db->group_start(); // Start a group for OR conditions
        $this->db->or_where_in('name', $name); // Match name from $name array
        $this->db->or_where_in('description', $description); // Match description from $description array
        $this->db->group_end(); // End the group
        $result = $this->db->get(db_prefix() . 'restaurant_reach')->result_array();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
