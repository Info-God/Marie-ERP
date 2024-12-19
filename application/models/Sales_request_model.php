<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_request_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/ProcessBuilderApi_model');
    }

    public function accept($id)
    {

        if (!empty($id)) {
            $update = [
                "status" => 1,
            ];
            $this->db->where('id', $id);
            $result = $this->db->update(db_prefix() . 'restaurant_reach_request', $update);
            if ($result) {

                $this->db->where('id', $id);
                $getdata = $this->db->get(db_prefix() . 'restaurant_reach_request')->row_array();

                $datecreated = date('Y-m-d H:i:s');

                if ($getdata) {

                    $this->db->where('r_id', $getdata['r_id']);
                    $this->db->delete(db_prefix() . 'restaurant_reach_data');
                }

                $reachs = unserialize($getdata['channels']);
                if (isset($reachs)) {
                    foreach ($reachs as $reach) {
                        $result = $this->db->insert(db_prefix() . 'restaurant_reach_data', [
                            'r_id' => $getdata['r_id'],
                            'reach'     => $reach,
                            'datecreated'     => $datecreated,
                        ]);
                    }
                }

                $this->db->where_in('r_id', $getdata['r_id']);
                $result = $this->db->get(db_prefix() . 'processbuilder_data')->row_array();

                $data = unserialize($result['data']);

                $processbuilder = $this->ProcessBuilderApi_model->getProcessBuilder($getdata['r_id']);

                $old = $data['data']['data']['activity'];
                $data['data']['data']['activity'] = $processbuilder;

                $new1 = array_diff($old, $processbuilder);
                $new2 = array_diff($processbuilder, $old);
                
                $new = array_merge($new1, $new2);

                if (in_array("D2. MIXING - BEVERAGES",$new)) {
                    $position = array_search("D2. MIXING - BEVERAGES", $new);

                    unset($new[$position]);
                }

                $New = [];
                
                foreach ($new as $value) {
                    if ($value) {
                        $explodedValue = explode(".", $value);
                        $New[] = $explodedValue[0];
                    }
                }
                
                $data['data']['data']['new'] = $New;                
                $data['data']['data']['change'] = true;

                $Data = [
                    'data' => serialize($data),
                ];
    
                $this->db->where_in('r_id', $getdata['r_id']);
                $result = $this->db->update(db_prefix() . 'processbuilder_data', $Data);

                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

    public function decline($id)
    {

        if (!empty($id)) {
            $update = [
                "status" => 2,
            ];
            $this->db->where('id', $id);
            $result = $this->db->update(db_prefix() . 'restaurant_reach_request', $update);

            if ($result) {
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }
}
