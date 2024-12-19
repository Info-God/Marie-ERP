<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_category($data)
    {

        if (!empty($data)) {
            $insert = [
                "category" => $data['category_name'],
                "datecreated" => date('Y-m-d H:i:s'),
            ];

            $category = $data['category_name'];

            $isExist = $this->check_category($category);
            if ($isExist) {
                $this->db->insert(db_prefix() . 'menu_categories ', $insert);
                if ($this->db->affected_rows() > 0) {
                    $data['id'] = $this->db->insert_id();
                    return $data;
                } else {
                    return false;
                }
            }else{
                return false;
            }
            return $data;
        }

    }

    public function delete_category($id)
    {
        $this->db->where('category_id', $id);
        $result = $this->db->delete(db_prefix() . 'menu_categories');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function check_category($category)
    {
        $this->db->select('category', $category);
        $this->db->where('category', $category);
        $result = $this->db->get(db_prefix() . 'menu_categories ')->result_array();

        if (count($result) > 0) {
            return false;
        }
        return true;
    }

}