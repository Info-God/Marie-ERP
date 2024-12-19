<?php

use Twilio\Deserialize;

defined('BASEPATH') or exit('No direct script access allowed');

class ProcessBuilderApi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCategory()
    {
        $i = 0;
        $result = $this->db->get(db_prefix() . 'overheads')->result_array();
        foreach ($result as $singleData) {
            $category = $singleData['category'];
            $items = explode(',', $singleData['predefined_items']);

            // $categories[$i][$category] = $items;
            $categories[$category] = $items;
            $i++;
        }

        return $categories;
    }

    public function getProcessBuilder($id)
    {
        $result = $this->db->get(db_prefix() . 'labour_activity_list')->result_array();

        $i = 0;
        $j = 0;

        foreach ($result as $Data) {
            if ($j < 5) {
                for ($i; $i < 5; $i++) {
                    $Activity1[] = $Data['activity'];
                    break;
                }
            }

            if ($j <= 20 && $j > 4) {
                for ($q = 0; $q < 4; $q++) {
                    $Activity2[] = $Data['activity'];
                    break;
                }
            }

            if ($j > 20) {
                for ($q = 0; $q < 4; $q++) {
                    $Activity3[] = $Data['activity'];
                    break;
                }
            }
            $j++;
        }


        $this->db->where_in('r_id', $id);
        // $this->db->where_in('year', $year);
        // $this->db->where_in('month', $month);
        $result1 = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();

        foreach ($result1 as $Data1) {
            $saleschannel[] = $Data1['reach'];
        }

        foreach ($saleschannel as  $salesChannel) {
            $i = 0;
            foreach ($Activity2 as  $value) {
                if ($salesChannel == "Dine-in") {
                    if (($value == "E1. DINE-IN ORDERING" || $value == "F1. DINE-IN PRESENTATION" || $value == "G1. DINE-IN SERVING" || $value == "H1. DINE-IN PAYING")) {
                        $dine[] = $value;
                    }
                } elseif ($salesChannel == "Delivery Platform Service") {
                    if (($value == "E2. DELIVERY PLATFORM ORDERING" || $value == "F2. DELIVERY PLATFORM PACKAGING" || $value == "G2. DELIVERY PLATFORM SERVING" || $value == "H2. DELIVERY PLATFORM PAYING")) {
                        $delivery[] = $value;
                    }
                } elseif ($salesChannel == "Own Platform") {
                    if (($value == "E3. OWN PLATFORM ORDERING" || $value == "F3. OWN PLATFORM PACKAGING" || $value == "G3. OWN PLATFORM SERVING" || $value == "H3. OWN PLATFORM PAYING")) {
                        $platform[] = $value;
                    }
                } elseif ($salesChannel == "Takeaway") {
                    if (($value == "E4. TAKEAWAY ORDERING" || $value == "F4. TAKEAWAY PACKAGING" || $value == "G4. TAKEAWAY SERVING" || $value == "H4. TAKEAWAY PAYING")) {
                        $takeaway[] = $value;
                    }
                }

                $i++;
            }
        }

        if (isset($dine)) {
            for ($i = 0; $i < 4; $i++) {
                $Activity1[] = $dine[$i];
            }
        }

        if (isset($delivery)) {
            for ($i = 0; $i < 4; $i++) {
                $Activity1[] = $delivery[$i];
            }
        }

        if (isset($platform)) {
            for ($i = 0; $i < 4; $i++) {
                $Activity1[] = $platform[$i];
            }
        }

        if (isset($takeaway)) {
            for ($i = 0; $i < 4; $i++) {
                $Activity1[] = $takeaway[$i];
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $Activity1[] = $Activity3[$i];
        }

        return $Activity1;
    }

    public function getDefault()
    {

        $default = array(
            'A' => [0 => 'Ingredients', 1 => 'Labour'],
            'B' => ['Labour'],
            'C1' => ['Labour'],
            'C2' => ['Labour'],
            'D1' => ['Labour'],
            'E1' => ['Labour'],
            'E2' => ['Labour'],
            'E3' => ['Labour'],
            'E4' => ['Labour'],
            'F1' => ['Labour'],
            'F2' => ['Labour'],
            'F3' => ['Labour'],
            'F4' => ['Labour'],
            'G1' => ['Labour'],
            'G2' => ['Labour'],
            'G3' => ['Labour'],
            'G4' => ['Labour'],
            'H1' => ['Labour'],
            'H2' => ['Labour'],
            'H3' => ['Labour'],
            'H4' => ['Labour'],
            'I' => ['Labour'],
            'J' => ['Labour'],
            // 'K' => [''],
            'L' => ['Labour']
        );

        return $default;
    }

    public function getDescription()
    {

        $description = array(
            'A' => 'Purchasing materials that goes into food and beverage products.',
            'B' => 'Safekeeping and monitoring of materials in inventory.',
            'C1' => 'Preparing bases, marination, curries etc. for food orders.',
            'C2' => 'Preparing premixes, fruits etc. for beverage orders.',
            'D1' => 'Cooking food upon customer order.',
            'E1' => 'Ordering from table and recording on app.',
            'E2' => 'Order received from delivery platform.',
            'E3' => 'Order received through own platform.',
            'E4' => 'Order received over the counter.',
            'F1' => 'Plating for customers.',
            'F2' => 'Packaging food and beverages for customers.',
            'F3' => 'Packaging food and beverages for customers.',
            'F4' => 'Packaging food and beverages for customers.',
            'G1' => 'Serving food and beverage to customers at table.',
            'G2' => 'Checking packed order before handing over to driver.',
            'G3' => 'Checking packed order before handing over to customer.',
            'G4' => 'Checking packed order before handing over to customer.',
            'H1' => 'Paying for food and beverage consumption before exiting.',
            'H2' => 'Receipt payment from delivery provider.',
            'H3' => 'Receipt payment from customer banking.',
            'H4' => 'Paying for food and beverage consumption before exiting.',
            'I' => 'Clearing and cleaning tables; cleaning glasses, utensils and plates.',
            'J' => 'Upkeep of premises to an exceptional standard.',
            'K' => 'Providing shelter, food and conveniences to employees.',
            'L' => 'Bookkeeping, management and other administrative duties for an operational business.'
        );

        return $description;
    }

    public function saving($id, $data)
    {

        foreach ($data["data"]["data"]["categories"] as $key => $value) {
            $categories[]=$key;
        }

        $this->db->select('category');
        $this->db->from('tbloverheads');
        $categoryData = $this->db->get()->result_array();
        $dbCategories = array_column($categoryData, 'category');

        $notFoundCategories = array_diff($categories, $dbCategories);  

        $this->db->from('tbloverheads');
        $categoryData = $this->db->count_all_results();

        foreach ($data["data"]["data"]["new_categories"] as $key => $value) {
            if (array_search($key,$notFoundCategories)) {
                $count[]=$value;
                $id_assigned[]=$key;
            }
        }

        $max_id = max($id_assigned);

        if(!$max_id){
            $max_id = count($dbCategories) + 1;
        }

        foreach ($notFoundCategories as $key => $value) {
            if (!array_search($key,$id_assigned)) {
                $data["data"]["data"]["new_categories"][$value]=$max_id;
                $max_id++;
            }
        }

        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();

        if (!$result) {
            $datecreated = date('Y-m-d H:i:s');

            $Data = [
                'r_id' => $id,
                'data' => serialize($data),
                'datecreated' => $datecreated
            ];

            $this->db->insert(db_prefix() . 'processbuilder_data', $Data);

            $result = $this->db->insert_id();

            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {

            $Data = [
                'data' => serialize($data),
            ];

            $this->db->where_in('r_id', $id);
            $result = $this->db->update(db_prefix() . 'processbuilder_data', $Data);


            if ($result) {
                return $result;
            } else {
                return false;
            }
        }
    }

    // public function saving($id, $data)
    // {

    //     $this->db->where_in('r_id', $id);
    //     $result = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();

    //     if (!$result) {
    //         $datecreated = date('Y-m-d H:i:s');

    //         $Data = [
    //             'r_id' => $id,
    //             'data' => serialize($data),
    //             'datecreated' => $datecreated
    //         ];

    //         $this->db->insert(db_prefix() . 'processbuilder_data', $Data);

    //         $result = $this->db->insert_id();

    //         if ($result) {
    //             return $result;
    //         } else {
    //             return false;
    //         }
    //     } else {

    //         $Data = [
    //             'data' => serialize($data),
    //         ];

    //         $this->db->where_in('r_id', $id);
    //         $result = $this->db->update(db_prefix() . 'processbuilder_data', $Data);


    //         if ($result) {
    //             return $result;
    //         } else {
    //             return false;
    //         }
    //     }
    // }

    public function fetch($id)
    {
        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'processbuilder_data')->result_array();

        $data = unserialize($result[0]['data']);

        $Activity=$data["data"]["data"]["activity"];
        $data["data"]["data"]["activity"] =[];

        foreach ($Activity as $key => $value) {
            $this->db->select('activity');
            $this->db->from('tbllabour_activity_list'); // This is the master table
            $this->db->where_in('activity', $value); // Filter by assigned activities
            $query = $this->db->get()->result_array();

            if($query){
                $data["data"]["data"]["activity"][] = $query[0]["activity"];
            }
        }

        return $data;
    }

    public function expenses($id)
    {
        //labour data collection
        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'labour_data')->result_array();
        if (!empty($result)) {

            foreach ($result as $key => $Value) {

                $unserialized[$Value["e_id"]] = unserialize($Value['activity']);

                if (!empty($Value["traceable"])) {
                    $traceables[$Value["e_id"]] = $Value["traceable"];
                } else {
                    $traceables[$Value["e_id"]] = $Value["salary"];
                }
            }

            $i = 0;
            $j = 0;
            foreach ($unserialized as $Key => $VALUE) {
                foreach ($VALUE as $key => $value) {
                    $values = round(($value["percentage"] * $traceables[$Key]) / 100, 2);
                    $Data[$j][$value['activity']] = $values;
                    $j++;
                }
                $i++;
            }

            //overheads data collection
            $this->db->select('*');
            $this->db->from('tbloverheads_user_data');
            $this->db->where('restaurant', $id);
            $annualResult = $this->db->get()->result_array();

            foreach ($annualResult as $eachData) {
                $EachData[] = unserialize($eachData['datas']);
            }

            $extrapolation = [];
            $spent = [];
            $processBuilder = [];
            $total = [];
            $KEY = [];
            foreach ($EachData as $s => $EACHDATA) {
                foreach ($EACHDATA as $key => $VALUE) {
                    foreach ($VALUE['processBuilderRelationship'] as $KEY => $value) {
                        if(!$value){
                            continue;
                        }
                        $processBuilder[$KEY] = $value;
                    }

                    if ($annualResult[$s]["cycle"] == 2) {
                        $total = $VALUE['spent'];
                    } else {
                        $total = $VALUE['extrapolation'];
                    }

                    foreach ($processBuilder as $key => $value) {

                        $values = round(($value * $total) / 100,2);
                        $Data[][$key] = $values;
                        unset($processBuilder);
                    }
                }
            }

            // $i = 0;
            // foreach ($processBuilder as $key => $VALUE) {
            //     foreach ($VALUE as $key => $value) {
            //         $values = ($value * $total[$i]) / 100;
            //         $Data[$j][$key] = $values;
            //         $j++;
            //     }
            //     $i++;
            // }

            $sums = [];
            foreach ($Data as $subArray) {
                foreach ($subArray as $key => $value) {

                    if ($key == 'A.  PROCUREMENT') {
                        $key = 'A. PROCUREMENT';
                    } elseif ($key == 'B.  STORAGE') {
                        $key = 'B. STORAGE';
                    } elseif ($key == 'C1.  PREPARATION - FOOD') {
                        $key = 'C1. PREPARATION - FOOD';
                    } elseif ($key == 'C2.  PREPARATION - BEVERAGES') {
                        $key = 'C2. PREPARATION - BEVERAGES';
                    } elseif ($key == 'D1.  COOKING - FOOD') {
                        $key = 'D1. COOKING - FOOD';
                    } elseif ($key == 'E1.  DINE-IN ORDERING') {
                        $key = 'E1. DINE-IN ORDERING';
                    } elseif ($key == 'E2.  DELIVERY PLATFORM ORDERING') {
                        $key = 'E2. DELIVERY PLATFORM ORDERING';
                    } elseif ($key == 'E3.  OWN PLATFORM ORDERING') {
                        $key = 'E3. OWN PLATFORM ORDERING';
                    } elseif ($key == 'E4.  TAKEAWAY ORDERING') {
                        $key = 'E4. TAKEAWAY ORDERING';
                    } elseif ($key == 'F1.  DINE-IN PRESENTATION') {
                        $key = 'F1. DINE-IN PRESENTATION';
                    } elseif ($key == 'F2.  DELIVERY PLATFORM PACKAGING') {
                        $key = 'F2. DELIVERY PLATFORM PACKAGING';
                    } elseif ($key == 'F3.  OWN PLATFORM PACKAGING') {
                        $key = 'F3. OWN PLATFORM PACKAGING';
                    } elseif ($key == 'F4.  TAKEAWAY PACKAGING') {
                        $key = 'F4. TAKEAWAY PACKAGING';
                    } elseif ($key == 'G1.  DINE-IN SERVING') {
                        $key = 'G1. DINE-IN SERVING';
                    } elseif ($key == 'G2.  DELIVERY PLATFORM SERVING') {
                        $key = 'G2. DELIVERY PLATFORM SERVING';
                    } elseif ($key == 'G3.  OWN PLATFORM SERVING') {
                        $key = 'G3. OWN PLATFORM SERVING';
                    } elseif ($key == 'G4.  TAKEAWAY SERVING') {
                        $key = 'G4. TAKEAWAY SERVING';
                    } elseif ($key == 'H1.  DINE-IN PAYING') {
                        $key = 'H1. DINE-IN PAYING';
                    } elseif ($key == 'H2.  DELIVERY PLATFORM PAYING') {
                        $key = 'H2. DELIVERY PLATFORM PAYING';
                    } elseif ($key == 'H3.  OWN PLATFORM PAYING') {
                        $key = 'H3. OWN PLATFORM PAYING';
                    } elseif ($key == 'H4.  TAKEAWAY PAYING') {
                        $key = 'H4. TAKEAWAY PAYING';
                    } elseif ($key == 'I.  CLEANING') {
                        $key = 'I. CLEANING';
                    } elseif ($key == 'J.  FACILITY MANAGEMENT') {
                        $key = 'J. FACILITY MANAGEMENT';
                    } elseif ($key == 'K.  EMPLOYEE WELFARE') {
                        $key = 'K. EMPLOYEE WELFARE';
                    } elseif ($key == 'L.  ADMINISTRATIVE') {
                        $key = 'L. ADMINISTRATIVE';
                    }

                    if (array_key_exists($key, $sums)) {
                        $sums[$key] += $value;
                    } else {
                        $sums[$key] = $value;
                    }
                }
            }

            $sums2=$sums;

            foreach ($sums2 as $key => $value) {
                $sums3[$key] = round($value, 2);
            }

            return $sums3;
        }
        return false;
    }

    public function change($id)
    {
        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'processbuilder_data')->row_array();

        $data = unserialize($result['data']);

        $processbuilder = $this->getProcessBuilder($id);

        $old = $data['data']['data']['activity'];
        $data['data']['data']['activity'] = $processbuilder;

        foreach ($old as $key => $value) {
            $parts = explode(".", $value);
            if (in_array($value, $processbuilder)) {
                $status[$parts[0]] = false;
            } else {
                $status[$parts[0]] = true;
            }
        }

        $data['data']['data']['status'] = $status;

        return $data;
    }
}
