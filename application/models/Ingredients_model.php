<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ingredients_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_ingredients($data)
    {

        // if (!empty($_FILES['image'])) {
        //     $filename = $_FILES['image']['name'];
        //     $tmp_name = $_FILES['image']['tmp_name'];
        //     $folder = 'uploads/ingredient_image/';
        //     $file = base_url($folder . $filename);
        //     move_uploaded_file($tmp_name, $folder . $filename);

        if (!empty($data)) {
            $category_insert = [
                "category" => $data['category_name'],
            ];

            $this->db->insert(db_prefix() . 'ingredient_categories', $category_insert);

            if ($this->db->affected_rows() > 0) {
                $id = $this->db->insert_id();

                $ingredient_insert = [
                    "category" => $id,
                    "ingredient_name" => $data['ingredient_name'],
                    "units_of_measure" => $data['unit'],
                ];

                $this->db->insert(db_prefix() . 'ingredients_listing', $ingredient_insert);

                $data['id'] = $this->db->insert_id();

                return $data;
            } else {
                return false;
            }
        }
        // }
        return false;
    }

    public function update_ingredients($data, $id)
    {

        if (!empty($data)) {
            $update = [
                "category" => $data['category_name'],
            ];
            $this->db->where('category_id', $id);
            $this->db->update(db_prefix() . 'ingredient_categories', $update);

            if ($this->db->affected_rows() > 0) {
                $data['alert'] = 'Category updated';
                $data['result'] = true;
            } else {
                $data['alert'] = 'Not able to update';
                $data['result'] = false;
            }
            return $data;
        }
    }

    public function delete_ingredient($id)
    {

        $this->db->where('category_id', $id);
        $this->db->delete(db_prefix() . 'ingredient_categories');

        $this->db->where('category', $id);
        $this->db->delete(db_prefix() . 'ingredients_listing');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
