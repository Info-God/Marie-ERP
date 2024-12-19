<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SalesApi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        $this->db->select('name');
        $this->db->select('description');
        $result = $this->db->get(db_prefix() . 'restaurant_reach')->result_array();
        $i=0;

        foreach ($result as $value) {
            // $data['channels'][$value['name']]=$value['description'];
            $Data[$i]['name']=$value['name'];
            $Data[$i]['description']=$value['description'];
            $i++;
        }
        return $Data;
    }

    public function sales_channels($data, $id)
    {

        $this->db->where('r_id', $id);
        // $this->db->where('month', $data['month']);
        // $this->db->where('year', $data['year']);
        $getdata = $this->db->get(db_prefix() . 'restaurant_reach_data');

        $datecreated = date('Y-m-d H:i:s');

        if ($getdata->num_rows() > 0) {

            $this->db->where('r_id', $id);
            // $this->db->where('month', $data['month']);
            // $this->db->where('year', $data['year']);
            $this->db->delete(db_prefix() . 'restaurant_reach_data');
        }

        if (!empty($data['reach'])) {
            $reachs = $data['reach'];
            if (isset($reachs)) {
                foreach ($reachs as $reach) {
                    $result = $this->db->insert(db_prefix() . 'restaurant_reach_data', [
                        'r_id' => $id,
                        // 'month' => $data['month'],
                        // 'year' => $data['year'],
                        'reach'     => $reach,
                        'datecreated'     => $datecreated,
                    ]);
                }
                return $result;
            }
        }
    }

    public function sales_data_by_channel_api($data, $id)
    {

        $this->db->where('r_id', $id);
        $this->db->where('month', $data['month']);
        $this->db->where('year', $data['year']);
        $query = $this->db->get(db_prefix() . 'restaurant_sales_data');

        $food_value = serialize($data['food_values']);
        $beverage_value = serialize($data['beverages_values']);
        $values = serialize($data['values']);
        $datecreated = date('Y-m-d H:i:s');
        $month = $data['month'];

        if ($query->num_rows() > 0) {

            $this->db->where('r_id', $id);
            $this->db->where('month', $data['month']);
            $this->db->where('year', $data['year']);
            $this->db->update(db_prefix() . 'restaurant_sales_data', [
                'r_id' => $id,
                'month' => $month,
                'year' => $data['year'],
                'food_data'     => $food_value,
                'beverage_data'     => $beverage_value,
                'add_values'     => $values,
                'datecreated'     => $datecreated,
            ]);

            return $id;
        }

        $this->db->insert(db_prefix() . 'restaurant_sales_data', [
            'r_id' => $id,
            'month' => $month,
            'year' => $data['year'],
            'food_data'     => $food_value,
            'beverage_data'     => $beverage_value,
            'add_values'     => $values,
            'datecreated'     => $datecreated,
        ]);

        $r_id = $this->db->insert_id();

        if ($r_id) {
            return true;
        }
        return false;
    }

    public function get_sales_data($id, $Month, $year)
    {
        $this->db->where('r_id', $id);
        if (!$Month == '') {
            $this->db->where('month', $Month);
        }
        $this->db->where('year', $year);
        $result = $this->db->get(db_prefix() . 'restaurant_sales_data')->result_array();

        if ($result) {

            $data['beverages_values'] = unserialize($result[0]['beverage_data']);
            $data['food_values'] = unserialize($result[0]['food_data']);
            $data['values'] = unserialize($result[0]['add_values']);

            return $data;
        } else {
            $datas = $this->check_sales_data($id, $Month, $year);

            if ($datas) {
                $data['beverages_values'] = unserialize($datas['beverage_data']);
                $data['food_values'] = unserialize($datas['food_data']);
                $data['values'] = unserialize($datas['add_values']);

                return $data;
            }
            return false;
        }
    }

    public function get_sales_channel($id, $Month, $year)
    {
        $this->db->select('reach');
        $this->db->where('r_id', $id);
        // $this->db->where('month', $Month);
        // $this->db->where('year', $year);
        $result = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();

        return $result;
    }

    public function check_sales_data($id, $Month, $Year)
    {
        $this->db->where('r_id', $id);
        // if (!$Month == '') {
        //     $this->db->where('month', $Month);
        // }
        // $this->db->where('year', $year);
        $this->db->order_by('id', 'desc');
        $result = $this->db->get(db_prefix() . 'restaurant_sales_data')->result_array();

        if ($result) {

            $month = "";
            $year = "";
            foreach ($result as $key => $value) {
                if ($value['month'] == $Month) {
                    return $this->get_sales_data($id, $Month, $Year);
                } else {
                    if ($month == "" && $year == "") {
                        $month = $value['month'];
                        $year = $value['year'];

                        $monthlyData = $value;
                        $monthlyData["month"] = $Month;
                        $monthlyData["year"] = $Year;
                    } elseif ($month == $value['month'] && $year == $value['year']) {
                        $monthlyData = $value;
                        $monthlyData["month"] = $Month;
                        $monthlyData["year"] = $Year;
                    }
                }
            }

            $datecreated = date('Y-m-d H:i:s');
            $monthlyData["datecreated"] = $datecreated;

            $this->db->insert(db_prefix() . 'restaurant_sales_data', [
                'r_id' => $id,
                'month' => $Month,
                'year' => $Year,
                'food_data'     => $monthlyData["food_data"],
                'beverage_data'     => $monthlyData["beverage_data"],
                'add_values'     => $monthlyData["add_values"],
                'datecreated'     => $datecreated,
            ]);

            return $monthlyData;
        }
        return false;
        // else {
        //     return $this->get_sales_data($id, $Month, $year);
        // }
    }

    public function sales_menu()
    {
        // $this->db->where('validated', 1);
        $this->db->select('category');
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        $categories = [];

        $i = 0;
        foreach ($result as $values) {
            if (!in_array($values['category'], $categories)) {
                $categories[] = $values['category'];
                $i++;
            }
            if ($i == 2) {
                break;
            }
        }

        for ($i = 0; $i < count($categories); $i++) {
            $category = $categories[$i];
            $this->db->where('category', $category);
            $this->db->where('validated', 1);
            $item = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();
            $items[$category] = [];
            foreach ($item as $value) {
                $items[$category][] = $value['item'];
            }
        }
        if ($items) {
            $data['categories'] = $categories;
            $data['items'] = $items;
            return $data;
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
                    } else {
                        $new_category = false;
                    }
                } elseif ($databaseFields[$i] == 'Item') {
                    $row["Item"] = ucfirst($row["Item"]);
                    $dup = $this->exists_item_name($row["Item"]);
                    $check = $this->check_item_name($id, $row["Item"], $row["TableUnits"], $month, $year);
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
                    $result = $this->add_sales_menu_data($insert, $id, $month, $year, $row_id);
                } else {
                    $result = $this->update_sales_menu_data($insert, $id, $month, $year, $check);
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

    public function get_sales_menu($id, $month, $year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $sales_menu = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
        if (!count($sales_menu) == 0) {
            $data['count'] = count($sales_menu);
            foreach ($sales_menu as $values) {
                $month = $values['month'];
                $menuData = unserialize($values["salesdata"]);
                $menuData['id'] = $values["id"];
                $menuData['delete'] = $values["row_id"];
                $menu_data[] = $menuData;
            }
            $data['sales_month'] = $month;
            $data['menu_data'] = $menu_data;
            return $data;
        }
        $datas = $this->check_sales_menu_data($id, $month, $year);

        if ($datas) {
            $data['count'] = count($datas);
            foreach ($datas as $values) {
                $month = $values['month'];
                $menuData = unserialize($values["salesdata"]);
                $menuData['id'] = $values["id"];
                $menuData['delete'] = $values["row_id"];
                $menu_data[] = $menuData;
            }
            $data['sales_month'] = $month;
            $data['menu_data'] = $menu_data;
            return $data;
        }

        return false;
    }

    public function check_sales_menu_data($id, $Month, $Year)
    {
        $this->db->where('r_id', $id);
        // if (!$Month == '') {
        //     $this->db->where('month', $Month);
        // }
        // $this->db->where('year', $year);
        $this->db->order_by('id', 'desc');
        $result = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

        if ($result) {

            $datecreated = date('Y-m-d H:i:s');
            $month = "";
            $year = "";
            $MonthlyData = [];
            foreach ($result as $key => $value) {
                if ($value['month'] == $Month) {
                    return $this->get_sales_menu($id, $Month, $Year);
                } else {
                    if ($month == "" && $year == "") {
                        $month = $value['month'];
                        $year = $value['year'];

                        $monthlyData = $value;
                        $monthlyData["month"] = $Month;
                        $monthlyData["year"] = $Year;
                    } elseif ($month == $value['month'] && $year == $value['year']) {
                        $monthlyData = $value;
                        $monthlyData["month"] = $Month;
                        $monthlyData["year"] = $Year;
                    }


                    if ($monthlyData) {
                        $result = $this->db->insert(db_prefix() . 'restaurant_sales_menu_data', [
                            'r_id' => $monthlyData["r_id"],
                            'row_id'     => $monthlyData["row_id"],
                            'month'     => $monthlyData["month"],
                            'year'     => $monthlyData["year"],
                            'salesdata'     => $monthlyData["salesdata"],
                            'datecreated'     => $datecreated,
                        ]);

                        $monthlyData['id'] = $this->db->insert_id();
                        $MonthlyData[] = $monthlyData;
                        unset($monthlyData);
                    }
                }
            }

            return $MonthlyData;
        }
        return false;
    }

    public function get_sales_menu_row($id, $month, $row_id, $year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('id', $row_id);
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
            $menu_data['id'] = $row_ids;
            $data['sales_month'] = $month;
            $data['year'] = $year;
            $data['menu_data'] = $menu_data;
            return $data;
        }
        return false;
    }

    public function validate_sales_menu_row_data($id, $month , $year, $data)
    {

        foreach ($data as $row) {
            $insert    = [];
            $duplicate = false;
            $j = ["Item_No", "Category", "Item", "TableUnits"];
            for ($i = 0; $i < count($j); $i++) {
                if (!isset($row[$j[$i]])) {
                    continue;
                }

                $row[$j[$i]] = $this->checkNullValueAddedByUser($row[$j[$i]]);

                if ($row[$j[$i]] == '') {
                    $row[$j[$i]] = '/';
                } elseif ($j[$i] == 'Category') {
                    $category = $this->list_category();
                    if (!in_array($row["Category"], $category)) {
                        $new_category = true;
                    } else {
                        $new_category = false;
                    }
                } elseif ($j[$i] == 'Item') {
                    $row['Item'] = ucfirst($row["Item"]);
                    $dup = $this->exists_item_name($row["Item"]);
                    $check = $this->check_item_name($id, $row["Item"], $row["TableUnits"], $month, $year);
                    if (is_string($check)) {
                        $update = true;
                    } elseif ($check == false) {
                        $duplicate = false;
                    } elseif ($check == true) {
                        $duplicate = true;
                    }
                }

                $insert[$j[$i]] = $row[$j[$i]];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $row_id = $row["id"];

                if (!isset($update)) {
                    return false;
                } else {
                    $result = $this->update_sales_menu_data($insert, $id, $month, $year, $row_id);
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

    public function delete_row($id, $month, $year, $row_id)
    {
        $this->db->where_in('r_id', $id);
        $this->db->where_in('month', $month);
        $this->db->where_in('year', $year);
        $this->db->where_in('row_id', $row_id);
        $result = $this->db->delete(db_prefix() . 'restaurant_sales_menu_data');
        return $result;
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

    public function checkNullValueAddedByUser($val)
    {
        if ($val === 'NULL' || $val === 'null') {
            $val = '';
        }

        return $val;
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

    public function exists_item_name($item)
    {
        $this->db->where_in('item', $item);
        $result = $this->db->get(db_prefix() . 'restaurant_menu_categories')->result_array();

        if ($result) {
            return true;
        }
        return false;
    }

    public function check_item_name($id, $item, $sales_volume, $month, $year,$row_id='')
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->like('salesdata', $item);
        $result = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

        // print_r($item);
        // die();

        if (!empty($result)) {
            $count = count($result);
            for ($i = 0; $i < $count; $i++) {
                foreach ($result as $value) {
                    $salesdata = unserialize($value['salesdata']);
                    $salesdata['Item'] = ucfirst($salesdata["Item"]);
                    if ($item == $salesdata['Item']) {
                        if ($sales_volume != $salesdata['TableUnits']) {
                            // if ($item=="Teaa") {
                            //     print_r($value);die();
                            // }
                            return $value['row_id'];
                        } else {
                            return true;
                        }
                    } elseif(!empty($row_id) && ($row_id == $value['row_id'])){
                        // die('hiii');
                        return $value['row_id'];
                    }
                }
            }
        }else {
            // die('hiii');
            return false;
        }
    }

    public function trimInsertValues($insert)
    {
        foreach ($insert as $key => $val) {
            $insert[$key] = !is_null($val) ? trim($val) : $val;
        }

        return $insert;
    }

    public function add_sales_menu_data($data, $id, $month, $year, $row_id, $import = '')
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
            'year'     => $year,
            'salesdata'     => $data,
            'datecreated'     => $datecreated,
        ]);

        return $result;
    }

    public function update_sales_menu_data($data, $id, $month, $year, $row_id, $import = '')
    {
        $data = serialize($data);
        $datecreated = date('Y-m-d H:i:s');


        if (!empty($import)) {

            $this->db->where('r_id', $id);
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            $this->db->where('row_id', $row_id);
            $result = $this->db->update(db_prefix() . 'restaurant_sales_menu_data', [
                'r_id' => $id,
                'row_id'     => $row_id,
                'month'     => $month,
                'salesdata'     => $data,
                'datecreated'     => $datecreated,
                'import'     => $import,
            ]);

            return $result;
        }

        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('row_id', $row_id);
        $result = $this->db->update(db_prefix() . 'restaurant_sales_menu_data', [
            'r_id' => $id,
            'row_id'     => $row_id,
            'month'     => $month,
            'salesdata'     => $data,
            'datecreated'     => $datecreated,
        ]);

        return $result;
    }

    public function add_category_item_import($category, $item)
    {
        $this->db->insert(db_prefix() . 'restaurant_menu_categories', [
            "category" => $category,
            "item" => $item,
            "validated" => 0
        ]);
    }

    public function request($data, $id)
    {

        $this->db->where('r_id', $id);
        $result = $this->db->get(db_prefix() . 'restaurant_reach_request')->result_array();

        foreach ($data as  $value) {
            $channels[] = $value['name'];
        }

        $datecreated = date('Y-m-d H:i:s');

        if (!$result) {

            $this->db->where('restaurant_id', $id);
            $this->db->select("business_name");
            $result = $this->db->get(db_prefix() . 'restaurant_registration')->row_array();

            $this->db->insert(db_prefix() . 'restaurant_reach_request', [
                'r_id' => $id,
                'restaurant' => $result['business_name'],
                'channels'     => serialize($channels),
                'datecreated'     => $datecreated
            ]);

            $r_id = $this->db->insert_id();

            return $r_id;
        }else{

            $this->db->where('r_id', $id);
            $Result=$this->db->update(db_prefix() . 'restaurant_reach_request', [
                'channels'     => serialize($channels),
                'status'     => 0,
                'notify'     => 0,
            ]);

            return $Result;
        }
    }

    public function delete_row_multiple($id, $month, $year, $row_ids)
    {
        foreach ($row_ids as $key => $row_id) {
            $this->db->where_in('r_id', $id);
            $this->db->where_in('month', $month);
            $this->db->where_in('year', $year);
            $this->db->where_in('id', $row_id);
            $result = $this->db->delete(db_prefix() . 'restaurant_sales_menu_data');
        }
        return $result;
    }
}
