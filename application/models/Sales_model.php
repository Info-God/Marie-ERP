<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function sales_channel($data, $id)
    {

        if (empty($data['reach'])) {
            $this->db->where('r_id', $id);
            $this->db->delete(db_prefix() . 'restaurant_reach_data');
            $this->db->where('r_id', $id);
            $this->db->delete(db_prefix() . 'restaurant_sales_data');
            return false;
        }

        $reachs = $data['reach'];
        foreach ($reachs as $reach) {
            $this->db->where_in('reach', $reach);
            $this->db->where_in('r_id', $id);
            $getdata[] = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();
        }
        if ($getdata) {
            $this->db->where('r_id', $id);
            $this->db->delete(db_prefix() . 'restaurant_reach_data');
        }

        if (!empty($data['reach'])) {
            $reachs = $data['reach'];
            if (isset($reachs)) {
                foreach ($reachs as $reach) {
                    $this->db->insert(db_prefix() . 'restaurant_reach_data', [
                        'r_id' => $id,
                        'reach'     => $reach,
                    ]);
                }
                if ($this->db->affected_rows() > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function sales_data_by_channel($data, $id)
    {

        $this->db->where('r_id', $id);
        if (!$data['month'] == '') {
            $this->db->where('month', $data['month']);
        }
        $query = $this->db->get(db_prefix() . 'restaurant_sales_data');

        $food_value = serialize($data['food_values']);
        $beverage_value = serialize($data['beverages_values']);
        $month = $data['month'];
        unset($data['month']);

        if ($query->num_rows() > 0) {

            $this->db->where('r_id', $id);
            if (!empty($month)) {
                $this->db->where('month', $month);
            }
            $result = $this->db->update(db_prefix() . 'restaurant_sales_data', [
                'r_id' => $id,
                'month'  => $month,
                'food_data'     => $food_value,
                'beverage_data'     => $beverage_value,
            ]);

            return $result;
        }

        $result = $this->db->insert(db_prefix() . 'restaurant_sales_data', [
            'r_id' => $id,
            'month' => $month,
            'food_data'     => $food_value,
            'beverage_data'     => $beverage_value,
        ]);


        if ($result) {
            return true;
        }
        return false;
    }

    public function add_sales_menu_data($data, $id, $month,$year, $row_id, $import = '')
    {
        $data = serialize($data);
        $datecreated = date('Y-m-d H:i:s');

        if (!empty($import)) {
            $result = $this->db->insert(db_prefix() . 'restaurant_sales_menu_data', [
                'r_id' => $id,
                'row_id'     => $row_id,
                'month'     => $month,
                'year'     => $year,
                'salesdata'     => $data,
                'datecreated'     => $datecreated,
                'import'     => $import,
            ]);

            return $id;
        }

        $result = $this->db->insert(db_prefix() . 'restaurant_sales_menu_data', [
            'r_id' => $id,
            'row_id'     => $row_id,
            'month'     => $month,
            'salesdata'     => $data,
            'datecreated'     => $datecreated,
        ]);
    }

    public function update_sales_menu_data($data, $id, $month,$year, $row_id, $import = '')
    {
        $data = serialize($data);
        $datecreated = date('Y-m-d H:i:s');

        if (!empty($import)) {

            $this->db->where('r_id', $id);
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            $this->db->where('row_id', $row_id);
            return $result = $this->db->update(db_prefix() . 'restaurant_sales_menu_data', [
                'r_id' => $id,
                'row_id'     => $row_id,
                'month'     => $month,
                'salesdata'     => $data,
                'datecreated'     => $datecreated,
                'import'     => $import,
            ]);

            return $id;
        }

        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('row_id', $row_id);
        return $result = $this->db->update(db_prefix() . 'restaurant_sales_menu_data', [
            'r_id' => $id,
            'row_id'     => $row_id,
            'month'     => $month,
            'salesdata'     => $data,
            'datecreated'     => $datecreated,
        ]);
    }

    public function checks_item_name($id, $item, $month)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $result = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

        if ($result) {
            $count = count($result);
            for ($i = 0; $i < $count; $i++) {
                foreach ($result as $value) {
                    $salesdata = unserialize($value['salesdata']);
                    if ($item == $salesdata['Item_Name']) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function check_item_name($id, $item, $sales_volume, $month,$year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $result = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

        if ($result) {
            $count = count($result);
            for ($i = 0; $i < $count; $i++) {
                foreach ($result as $value) {
                    $salesdata = unserialize($value['salesdata']);
                    if ($item == $salesdata['Item_Name']) {
                        if ($sales_volume != $salesdata['Total_Unit_Sales_Volume']) {
                            return $value['row_id'];
                        } elseif ($sales_volume == $salesdata['Total_Unit_Sales_Volume']) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function exists_item_name($item)
    {
        $this->db->where_in('item', $item);
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        if ($result) {
            return true;
        }
        return false;
    }

    public function validate_sales_menu_data($data, $id, $month, $year)
    {

        $databasefields = $this->db->list_fields(db_prefix() . 'restaurant_sales_menu_data_sample');
        foreach ($databasefields as $value) {
            $databaseFields[] = $value;
        }

        $row_id = $this->get_row_count($id, $month);
        $import = 2;

        foreach ($data as $row) {
            $insert    = [];
            $duplicate = false;

            for ($i = 0; $i < count($databaseFields); $i++) {
                $j = ["id", "Category", "Item", "TableUnits"];
                if (!isset($row[$j[$i]])) {
                    continue;
                }

                $row[$j[$i]] = $this->checkNullValueAddedByUser($row[$j[$i]]);

                if ($row[$j[$i]] == '') {
                    $row[$j[$i]] = '/';
                } elseif ($databaseFields[$i] == 'Category') {
                    $category = $this->list_category();
                    if (!in_array($row["Category"], $category)) {
                        $new_category = true;
                    }
                } elseif ($databaseFields[$i] == 'Item_Name') {
                    $row[2] = ucfirst($row[2]);
                    $dup = $this->exists_item_name($row["Item"]);
                    $check = $this->check_item_name($id, $row["Item"], $row["TableUnits"], $month,$year);
                    if (is_string($check)) {
                        $update = true;
                    } elseif ($check == false) {
                        $duplicate = false;
                    } elseif ($check == true) {
                        $duplicate = true;
                    }
                }

                $insert[$databaseFields[$i]] = $row[$j[$i]];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $row_id++;

                if (!isset($update)) {
                    $result = $this->add_sales_menu_data($insert, $id, $month,$year, $row_id, $import);
                } else {
                    $result = $this->update_sales_menu_data($insert, $id, $month,$year, $row_id, $import);
                }

                if (!$dup && ($new_category || !$new_category)) {
                    $this->add_category_item_import($row['Category'], $row['Item']);
                }
                unset($dup);
            }
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public function checkNullValueAddedByUser($val)
    {
        if ($val === 'NULL' || $val === 'null') {
            $val = '';
        }

        return $val;
    }

    public function trimInsertValues($insert)
    {
        foreach ($insert as $key => $val) {
            $insert[$key] = !is_null($val) ? trim($val) : $val;
        }

        return $insert;
    }

    public function check_new_item_name($item)
    {

        $this->db->where('item', $item);
        $query = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();
        $count = count($query);

        if ($count == 0) {
            return true;
        }
        return false;
    }

    public function check_new_category($category)
    {

        $this->db->where('category', $category);
        $query = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();
        $count = count($query);

        if ($count == 0) {
            return true;
        }
        return false;
    }

    public function insert_new_category_item($category, $item)
    {

        $this->db->insert(db_prefix() . 'restaurant_menu_categories', [
            'category' => $category,
            'item' => $item,
            'validated' => 0
        ]);
    }

    public function get_sales_menu($id, $month,$year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $sales_menu = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
        if (!count($sales_menu) == 0) {
            $data['count'] = count($sales_menu);
            foreach ($sales_menu as $values) {
                $month = $values['month'];
                foreach ($values as $key => $value) {
                    if ($key == 'salesdata') {
                        $menu_data[] = unserialize($value);
                    }
                    if ($key == 'row_id') {
                        $row_id[] = $value;
                    }
                }
            }
            for ($i = 0; $i < count($menu_data); $i++) {
                $menu_data[$i]['Item_No'] = $row_id[$i];
            }
            $data['sales_month'] = $month;
            $data['menu_data'] = $menu_data;
            return $data;
        }
        return false;
    }

    public function add_category_item_import($category, $item)
    {
        $this->db->insert(db_prefix() . 'restaurant_menu_categories', [
            "category" => $category,
            "item" => $item,
            "validated" => 0
        ]);
    }

    public function get_row_count($id, $month)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->select('row_id');
        $query = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
        $count = count($query);
        return $count;
    }

    public function list_category()
    {
        $this->db->where('validated', 1);
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        $categories = [];

        foreach ($result as $values) {
            if (!in_array($values['category'], $categories)) {
                $categories[] = $values['category'];
            }
        }
        return $categories;
    }

    public function validate_row_sales_menu_data($id, $month, $data,$year)
    {

        $databasefields = $this->db->list_fields(db_prefix() . 'restaurant_sales_menu_data_sample');
        foreach ($databasefields as $value) {
            $databaseFields[] = $value;
        }

        $row_id = $this->get_row_count($id, $month);
        $rows[0]['id'] = $row_id + 1;
        $rows[0]['Category'] = $data['categories'];
        $rows[0]['Item'] = $data['items'];
        $rows[0]['TableUnits'] = $data['sales_unit'];
        $import = 2;

        foreach ($rows as $row) {
            $insert    = [];
            $duplicate = false;

            for ($i = 0; $i < count($databaseFields); $i++) {
                $j = ["id", "Category", "Item", "TableUnits"];
                if (!isset($row[$j[$i]])) {
                    continue;
                }

                $row[$j[$i]] = $this->checkNullValueAddedByUser($row[$j[$i]]);

                if ($row[$j[$i]] == '') {
                    $row[$j[$i]] = '/';
                } elseif ($databaseFields[$i] == 'Category') {
                    $category = $this->list_category();
                    if (!in_array($row["Category"], $category)) {
                        $new_category = true;
                    }
                } elseif ($databaseFields[$i] == 'Item_Name') {
                    $row[2] = ucfirst($row[2]);
                    $dup = $this->exists_item_name($row["Item"]);
                    $check = $this->check_item_name($id, $row["Item"], $row["TableUnits"], $month,$year);
                    if (is_string($check)) {
                        $update = true;
                    } elseif ($check == false) {
                        $duplicate = false;
                    } elseif ($check == true) {
                        $duplicate = true;
                    }
                }

                $insert[$databaseFields[$i]] = $row[$j[$i]];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $row_id++;

                if (!isset($update)) {
                    $result = $this->add_sales_menu_data($insert, $id, $month,$year, $row_id, $import);
                } else {
                    $result = $this->update_sales_menu_data($insert, $id, $month,$year, $row_id, $import);
                }

                if (!$dup && ($new_category || !$new_category)) {
                    $this->add_category_item_import($row['Category'], $row['Item']);
                }
                unset($dup);
            }
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public function get_id()
    {
        $this->db->select('restaurant_id');
        $query = $this->db->get(db_prefix() . 'restaurant_registration')->result_array();

        return $query;
    }

    public function delete_row($id, $month,$year, $row_id)
    {
        $this->db->where_in('r_id', $id);
        $this->db->where_in('month', $month);
        $this->db->where_in('year', $year);
        $this->db->where_in('row_id', $row_id);
        $result= $this->db->delete(db_prefix() . 'restaurant_sales_menu_data');
        return $result;
    }

    public function delete_month_data($id, $month,$year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        // $this->db->select('row_id');
        return $this->db->delete(db_prefix() . 'restaurant_sales_menu_data');

    }

    public function validate_sales_menu_row_data($id, $month, $data,$year)
    {

        $databasefields = $this->db->list_fields(db_prefix() . 'restaurant_sales_menu_data_sample');
        foreach ($databasefields as $value) {
            $databaseFields[] = $value;
        }

        foreach ($data as $row) {
            $insert    = [];
            $duplicate = false;

            for ($i = 0; $i < count($databaseFields); $i++) {
                $j = ["Item_No", "Category", "Item", "TableUnits"];
                if (!isset($row[$j[$i]])) {
                    continue;
                }

                $row[$j[$i]] = $this->checkNullValueAddedByUser($row[$j[$i]]);

                if ($row[$j[$i]] == '') {
                    $row[$j[$i]] = '/';
                } elseif ($databaseFields[$i] == 'Category') {
                    $category = $this->list_category();
                    if (!in_array($row["Category"], $category)) {
                        $new_category = true;
                    }
                } elseif ($databaseFields[$i] == 'Item') {
                    $row[2] = ucfirst($row[2]);
                    $dup = $this->exists_item_name($row["Item"]);
                    $check = $this->check_item_name($id, $row["Item"], $row["TableUnits"], $month,$year);
                    if (is_string($check)) {
                        $update = true;
                    } elseif ($check == false) {
                        $duplicate = false;
                    } elseif ($check == true) {
                        $duplicate = true;
                    }
                }

                $insert[$databaseFields[$i]] = $row[$j[$i]];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $row_id = $row["Item_No"];

                if (!isset($update)) {
                    return false;
                } else {
                    $result = $this->update_sales_menu_data($insert, $id, $month,$year, $row_id);
                }

                if (!$dup && ($new_category || !$new_category)) {
                    $this->add_category_item_import($row['Category'], $row['Item']);
                }
                unset($dup);
            }
        }
        if ($result) {
            return $row_id;
        }
        return false;
    }

    public function get_sales_menu_row($id, $month, $row_id,$year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('row_id', $row_id);
        $sales_menu = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
        if (!count($sales_menu) == 0) {
            $data['count'] = count($sales_menu);
            foreach ($sales_menu as $values) {
                $month = $values['month'];
                foreach ($values as $key => $value) {
                    if ($key == 'salesdata') {
                        $menu_data = unserialize($value);
                    }
                    if ($key == 'row_id') {
                        $row_ids = $value;
                    }
                }
            }
            $menu_data['Item_No'] = $row_ids;
            $data['sales_month'] = $month;
            $data['year'] = $year;
            $data['menu_data'] = $menu_data;
            return $data;
        }
        return false;
    }
}
