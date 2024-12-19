<?php

defined('BASEPATH') or exit('No direct script access allowed');

class plans_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_plan($data){

        if(!empty($data)){
            $restaurant_plan = [
                "restaurant_plan" => $data['plan_name'],
                "plan_cost" => $data['plan_cost']
            ];

             $this->db->insert(db_prefix().'restaurant_plans',$restaurant_plan);
            if($this->db->affected_rows() > 0){
                $id = $this->db->insert_id();
                return $id;
            }
            else{
                return false;
            }
        }
    }

    public function update_plan($data,$id){

        $update = [
            "restaurant_plan" => $data['plan_name'],
            "plan_cost" => $data['plan_cost']
        ];

        $this->db->where(db_prefix().'restaurant_plans.plan_id',$id);
        $this->db->update(db_prefix().'restaurant_plans',$update);

        if($this->db->affected_rows() > 0){
            return $id;
        }
        else{
            return false;
        }
    }

    public function delete_plan($id){
        $this->db->where('plan_id',$id);
        $result = $this->db->delete(db_prefix().'restaurant_plans');

        if($this->db->affected_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}