<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Items_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_item($data)
    {

        if (!empty($data)) {
            $insert = [
                "category" => $data['category_name'],
                "item" => $data['item_name'],
            ];

            $category[] = $data['category_name'];
            $item[] = $data['item_name'];

            $repeated = $this->check_item($item);

            if ($repeated == false) {
                $new = $this->check_category($category);

                $this->db->insert(db_prefix() . 'restaurant_menu_categories', $insert);
                if ($this->db->affected_rows() > 0) {

                    $data['id'] = $this->db->insert_id();
                    $data['alert'] = $new == false ? 'New Item Created' : 'New Category Created';
                    return $data;
                } else {
                    return false;
                }
            }
            $data['alert'] = 'Item Already Exists';
            return $data;
        }
        return false;
    }

    public function update_item($data, $id)
    {

        if (!empty($data)) {
            $update = [
                "category" => $data['category_name'],
                "item" => $data['item_name'],
            ];
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'restaurant_menu_categories', $update);

            if ($this->db->affected_rows() > 0) {
                $data['id'] = $id;
                $duplicate = $this->check_category($update['category']);
                $data['alert'] = 'Item updated';
                if ($duplicate) {
                    $data['alert'] = 'Category updated';
                }
                return $data;
            } else {
                return false;
            }
        }
    }

    public function delete_item($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . 'restaurant_menu_categories');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function check_category($category)
    {
        $this->db->select('category', $category);
        $this->db->where_in('category', $category);
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        if (count($result) > 0) {
            return false;
        }
        return true;
    }

    public function check_item($item)
    {
        $this->db->select('item', $item);
        $this->db->where_in('item', $item);
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
