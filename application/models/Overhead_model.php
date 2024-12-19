<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Overhead_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_overheads($data)
    {

        if (!empty($data)) {
            $insert = [
                "category" => $data['category'],
                "predefined_items" => $data['predefined_items'],
            ];

            $category[] = $data['category'];
            $predefined_items[] = $data['predefined_items'];

            $repeated = $this->check_category($category);

            if ($repeated == false) {
                $this->db->insert(db_prefix() . 'overheads', $insert);
                if ($this->db->affected_rows() > 0) {

                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = 'New Overhead Created' ;
                    return $data;
                } else {
                    return false;
                }
            }
            $data['alert'] = 'Overhead Category Already Exists';
            return $data;
        }
        return false;
    }

    public function check_category($category)
    {
        $this->db->select('category', $category);
        $this->db->where_in('category', $category);
        $result = $this->db->get(db_prefix() . 'overheads')->result_array();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_overhead($id) {
        $this->db->where('overheads_id', $id);
        $result = $this->db->delete(db_prefix() . 'overheads');
    
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function update_overheads($data, $id)
    {
        if (!empty($data) ) {
            $update_data = [
                "category" => $data['category'],
                "predefined_items" => $data['predefined_items'],
        
            ];
            $id = $this->input->post('id');

            $this->db->where('overheads_id', $id);
            
            $this->db->update(db_prefix() . 'overheads', $update_data);

            if ($this->db->affected_rows() > 0) {
                $data['alert'] = 'Overhead Updated Successfully';
                return $data;
            } else {
                $data['alert'] = 'Failed to Update Overhead';
                return $data;
            }
        }

        return false;
    }

    public function add_cycle($data)
    {
        if (!empty($data)) {
            $insert = [
                "cycle" => $data['cycle'],
                "datecreated" => date('Y-m-d H:i:s'),
            ];

            $cycle[] = $data['cycle'];

            $repeated = $this->check_cycle($cycle);

            if ($repeated == false) {
                $this->db->insert(db_prefix() . 'overheads_cycle', $insert);
                if ($this->db->affected_rows() > 0) {

                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = 'New Overhead Cycle  Created' ;
                    return $data;
                } else { 
                    return false;
                }
            }
            $data['alert'] = 'Overhead Cycle Already Exists';
            return $data;
        }
        return false;
    }

    public function check_cycle($cycle)
    {
        $this->db->select('cycle', $cycle);
        $this->db->where_in('cycle', $cycle);
        $result = $this->db->get(db_prefix() . 'overheads_cycle')->result_array();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function update_cycle($data, $id)
    {

        if (!empty($data)) {
            $update = array(
                "cycle" => $data['cycle'],
            );
            
            $id = $this->input->post('id');

            $repeated = $this->check_cycle($data['cycle']);

            if ($repeated) {
                $data['alert'] = 'Overhead Cycle already exists';
                return $data;
            }

            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'overheads_cycle', $update);
            if ($this->db->affected_rows() > 0) {
                $data['id'] = $this->db->insert_id();
                $data['alert'] = 'Overhead Cycle updated';
                return $data;
            } else {
                return false;
            }
        
        return array('success' => false, 'alert' => 'Failed to update Overhead Cycle.');
        }
    }

    public function delete_cycle($id) {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . 'overheads_cycle');
    
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function add_report($data)
    {
        if (!empty($data)) {
            $insert = [
                "report" => $data['report'],
                "description" => $data['description'],
                "datecreated" => date('Y-m-d H:i:s'),
            ];
            $report[] = $data['report'];
            $repeated = $this->check_report($report);
            if ($repeated == false) {
                $this->db->insert(db_prefix() . 'overheads_report', $insert);
                if ($this->db->affected_rows() > 0) {
                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = 'New Overhead Report Created' ;
                    return $data;
                } else {
                    return false;
                }
            }
            $data['alert'] = 'Overhead Report Already Exists';
            return $data;
        }
        return false;
    }

    public function check_report($report)
    {
        $this->db->select('report', $report);
        $this->db->where_in('report', $report);
        $result = $this->db->get(db_prefix() . 'overheads_report')->result_array();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function update_report($data, $id)
    {

        if (!empty($data)) {
            $update = array(
                "report" => $data['report'],
                "description" => $data['description'],
            );
            
            $id = $this->input->post('id');

            $repeated = $this->check_report($data['report']);
            if ($repeated == true) {
                $data['alert'] = 'Overhead Report already exists';
                return $data;
            }
            
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'overheads_report', $update);
            if ($this->db->affected_rows() > 0) {
                $data['id'] = $this->db->insert_id();
                $data['alert'] = 'Overhead Report updated';
                return $data;
            } else {
                return false;
            }
        
        return array('success' => false, 'alert' => 'Failed to update Overhead Report.');
        }
    }

    public function delete_report($id) {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . 'overheads_report');
    
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}