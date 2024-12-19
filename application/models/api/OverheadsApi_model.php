<?php

use Twilio\Deserialize;

defined('BASEPATH') or exit('No direct script access allowed');

class OverheadsApi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function initialization($id)
    {
        $result = $this->db->get(db_prefix() . 'overheads_cycle')->result_array();
        foreach ($result as $singleData) {
            $cycle[] = $singleData['cycle'];
        }

        $result = $this->db->get(db_prefix() . 'overheads_report')->result_array();
        foreach ($result as $singleData) {
            $report[] = $singleData['report'];
            $description[$singleData['report']] = $singleData['description'];
        }

        $this->db->where_in('r_id', $id);
        $result1 = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();

        $Data = unserialize($result1[0]['data']);
        $data['data'] = $Data['data'];
        $data['cycle']=$cycle;
        $data['report']=$report;
        $data['description']=$description;
        return $data;
    }

    public function storeData($userId, $category, $report, $cycle, $data)
    {

        $this->db->select('overheads_id');
        $this->db->from('tbloverheads');
        $this->db->where('category', $category);
        $categoryData = $this->db->get()->result_array();
        $category_id = $categoryData[0]['overheads_id'];

        if (!$category_id) {
            $this->db->where_in('r_id', $userId);
            $result = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();
            $process_data = unserialize($result[0]['data']);

            foreach ($process_data["data"]["data"]["new_categories"] as $key => $value) {
                if ($category == $key) {
                    $category_id = $value;
                }
            }
        }
        // print_r($category_id);die;

        $this->db->where_in('restaurant', $userId);
        $this->db->where_in('category', $category_id);
        $res = $this->db->get(db_prefix() . 'overheads_user_data')->result_array();
        // print_r($res);die('hii');
        if ($res) {
            return $this->editData($userId, $category_id, $report, $cycle, $data);
        } else {
            $data = serialize($data);

            // report id
            $this->db->select('id');
            $this->db->from('tbloverheads_report');
            $this->db->where('report', $report);
            $reportData = $this->db->get()->result_array();
            $report_id = $reportData[0]['id'];

            // cycle id
            $this->db->select('id');
            $this->db->from('tbloverheads_cycle');
            $this->db->where('cycle', $cycle);
            $cycleData = $this->db->get()->result_array();
            $cycle_id = $cycleData[0]['id'];

            $datecreated = date('Y-m-d H:i:s');

            $storeData = [
                'restaurant' => $userId,
                'category' => $category_id,
                'report' => $report_id,
                'cycle' => $cycle_id,
                'datas' => $data,
                'datecreated' => $datecreated
            ];

            $this->db->insert(db_prefix() . 'overheads_user_data', $storeData);

            $report = $this->db->insert_id();

            return $report;
        }
    }

    public function fetchData($r_id, $category)
    {

        $this->db->select('overheads_id');
        $this->db->where('category', $category);
        $category_details = $this->db->get('tbloverheads')->result_array();
        $category_id = $category_details[0]['overheads_id'];

        if (!$category_id) {
            $this->db->where_in('r_id', $r_id);
            $result = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();
            $process_data = unserialize($result[0]['data']);

            foreach ($process_data["data"]["data"]["new_categories"] as $key => $value) {
                if ($category == $key) {
                    $category_id = $value;
                }
            }
        }        

        $this->db->where('category', $category_id);
        $this->db->where('restaurant', $r_id);
        $categoryData = $this->db->get('overheads_user_data')->row();

        // report id
        $this->db->select('report');
        $this->db->from('tbloverheads_report');
        $this->db->where('id', $categoryData->report);
        $reportData = $this->db->get()->result_array();
        $report = $reportData[0]['report'];

        // cycle id
        $this->db->select('cycle');
        $this->db->from('tbloverheads_cycle');
        $this->db->where('id', $categoryData->cycle);
        $cycleData = $this->db->get()->result_array();
        $cycle = $cycleData[0]['cycle'];

        $data['r_id'] = $categoryData->restaurant;
        // $data['category']=$category;
        $data['report'] = $report;
        $data['cycle'] = $cycle;
        $data['datas'] = unserialize($categoryData->datas);
        $data['date'] = $categoryData->datecreated;
        // print_r($data['datas']); die();

        return $data;
    }

    public function editData($r_id, $category_id, $report, $cycle,  $data)
    {

        $data = serialize($data);

        // report id
        $this->db->select('id');
        $this->db->from('tbloverheads_report');
        $this->db->where('report', $report);

        $reportData = $this->db->get()->result_array();
        $report_id = $reportData[0]['id'];

        // cycle id
        $this->db->select('id');
        $this->db->from('tbloverheads_cycle');
        $this->db->where('cycle', $cycle);

        $cycleData = $this->db->get()->result_array();
        $cycle_id = $cycleData[0]['id'];

        $datecreated = date('Y-m-d H:i:s');

        $storeData = [
            'category' => $category_id,
            'report' => $report_id,
            'cycle' => $cycle_id,
            'datas' => $data,
            'datecreated' => $datecreated
        ];

        $this->db->where('category', $category_id);
        $this->db->where('restaurant', $r_id);
        $result = $this->db->update(db_prefix() . 'overheads_user_data', $storeData);

        if ($result) {
            return true;
        }
        return false;
    }
}
