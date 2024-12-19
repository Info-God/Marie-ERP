<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Restaurant_cuisine_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_cuisine($data){

        if(!empty($data)){
            $restaurant_cuisine = [
                "cuisine_types" => $data['cuisine_type'],
            ];

            $this->db->insert(db_prefix().'restaurant_cuisine',$restaurant_cuisine);
            if($this->db->affected_rows() > 0){
                $id = $this->db->insert_id();
                return $id;
            }
            else{
                return false;
            }
        }
    }

    public function update_cuisine($data,$id){

        $update = [
            "cuisine_types" => $data['cuisine_type'],
        ];

        $this->db->where(db_prefix().'restaurant_cuisine.r_id',$id);
        $this->db->update(db_prefix().'restaurant_cuisine',$update);

        if($this->db->affected_rows() > 0){
            return $id;
        }
        else{
            return false;
        }
    }

    public function delete_cuisine($id){
        $this->db->where('r_id',$id);
        $this->db->delete(db_prefix().'restaurant_cuisine');

        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}