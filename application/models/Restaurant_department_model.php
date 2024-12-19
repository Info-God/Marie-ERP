<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Restaurant_department_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_department($data){

        if(!empty($data)){
            $restaurant_dept = [
                "department_name" => $data['department_name'],
            ];

             $this->db->insert(db_prefix().'restaurant_departments',$restaurant_dept);
            if($this->db->affected_rows() > 0){
                $id = $this->db->insert_id();
                return $id;
            }
            else{
                return false;
            }
        }
    }

    public function update_department($data,$id){
        $this->db->update(db_prefix().'restaurant_departments',$data);
        $this->db->where(db_prefix().'department_name.dept_id',$id);

        if($this->db->affected_rows() > 0){
            $id = $this->db->insert_id();
            return $id;
        }
        else{
            return false;
        }
    }

    public function delete_department($id){
        $this->db->where('dept_id',$id);
        $result = $this->db->delete(db_prefix().'restaurant_departments');

        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}