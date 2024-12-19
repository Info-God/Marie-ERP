<?php
class CostingApi_model extends CI_Model
{

    public function initialization($id, $month, $year)
    {
        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $sales = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

        for ($i = 0; $i < count($sales); $i++) {
            $salesdata[] = unserialize($sales[$i]['salesdata']);
        }

        $this->db->where('r_id', $id);
        // $this->db->where('month', $month);
        // $this->db->where('year', $year);
        $saleschannel = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();

        for ($i = 0; $i < count($saleschannel); $i++) {
            $salesChannel[$i] = $saleschannel[$i]['reach'];
        }

        $this->db->where('r_id', $id);
        $this->db->select(['name','id']);
        $sides = $this->db->get(db_prefix() . 'costing_sidedish')->result_array();

        $data['salesMenu'] = $salesdata;
        $data['salesChannel'] = $salesChannel;
        $data['sides'] = $sides;
        $direct_labour = $this->direct_labour($id, $month, $year);
        $data['food'] = (isset($direct_labour)) ? $direct_labour['food'] : [];
        $data['beverage'] = (isset($direct_labour)) ? $direct_labour['beverage'] : [];

        if ($data) {
            return $data;
        }
        return false;
    }

    public function save_sides($id, $data)
    {

        $side = ucwords(strtolower($data['sideName']));

        $this->db->where('r_id', $id);
        $this->db->where('name', $side);
        $result = $this->db->get(db_prefix() . 'costing_sidedish')->result_array();
        $datecreated = date('Y-m-d H:i:s');

        if (!$result) {

            $this->db->insert(db_prefix() . 'costing_sidedish', [
                'r_id' => $id,
                'name' => $side,
                'batch' => $data['batch'],
                'servings' => $data['servings'],
                'ingredients' => serialize($data['ingredients']),
                'dlh' => $data['dlh'],
                'datecreated'     => $datecreated,
            ]);
        } else {
            $this->db->where('r_id', $id);
            $this->db->where('name', $side);
            $result = $this->db->update(db_prefix() . 'costing_sidedish', [
                'batch' => $data['batch'],
                'servings' => $data['servings'],
                'ingredients' => serialize($data['ingredients']),
                'dlh' => $data['dlh'],
                'datecreated'     => $datecreated,
            ]);
        }
        $result = $this->fetch_sides($id, $side);
        return $result;
    }

    public function costing($id, $year, $month, $data)
    {

        $sums = [];
        $overheads = [];
        $others = 0;
        $count = 0;

        // $getMonthData = $this->getOverheads($id, $data['sales_channel']);

        // $labourData = $this->getLabour($id);

        // $result[0] = $getMonthData;

        // foreach ($result as $subArray) {

        // foreach ($subArray as $key => $value) {


        $getData = $this->getOverheads($id, $data['sales_channel']);

        $labourData = $this->getLabour($id, $month, $year);

        foreach ($getData as $key => $value) {
            if (array_key_exists($key, $sums)) {
                $sums[$key] += $value;
            } else {
                $sums[$key] = $value;
            }
            // }
        }

        // print_r($result);
        // die();

        // Check for sales type

        if ($data['salesType'] != 'Food') {
            foreach ($sums as $processBuilder => $value) {

                if ($processBuilder == 'C1.  PREPARATION - FOOD' || $processBuilder == 'C1. PREPARATION - FOOD') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'D1.  COOKING - FOOD' || $processBuilder == 'D1. COOKING - FOOD') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'F1.  DINE-IN PRESENTATION') {
                    unset($sums[$processBuilder]);
                }
            }
        } else {
            foreach ($sums as $processBuilder => $value) {

                if ($processBuilder == 'C2.  PREPARATION - BEVERAGES' || $processBuilder == 'C2. PREPARATION - BEVERAGES') {
                    unset($sums[$processBuilder]);
                }
                // if ($processBuilder == 'D2.  MIXING - BEVERAGES' || $processBuilder == 'D2. MIXING - BEVERAGES') {
                //     unset($sums[$processBuilder]);
                // }
            }
        }
        // print_r($sums);die();

        $salesData = $this->db->select('*')
            ->from('tblrestaurant_sales_data')
            ->where('r_id', $id)
            ->where('month', $month)
            ->get()->result_array();
        $a = $salesData[0]['add_values'];
        $b = $salesData[0]['food_data'];
        // $c = $salesData[0]['beverage_data'];
        $food = unserialize($a);
        $Food_Data = unserialize($b);
        // $beverage_data = unserialize($c);
        $total_sales = $food[0]['food_Total'] + $food[0]['beverages_Total'];
        // $food_sales = $food[0]['food_Total'];
        $Multiplier = array('1' => '0.8', '2' => '0.8', '3' => '0.9', '4' => '0.9', '5' => '1.0', '6' => '1.0', '7' => '1.1', '8' => '1.1');
        $multiplier = ($data['ing_count'] < 9) ? $Multiplier[$data['ing_count']] : 1.2;

        $reachData = $this->db->select('reach')
            ->from('tblrestaurant_reach_data')
            ->where('r_id', $id)
            // ->where('month', $month)
            ->get()->result_array();

        for ($i = 0; $i < count($reachData); $i++) {
            $reach[$i] = $reachData[$i]['reach'];
            $R = $reachData[$i]['reach'];
            $FOOD[$R] = $Food_Data[$i];
        }

        unset($food[0]['food_Total']);
        unset($food[0]['beverages_Total']);
        $count = count($food[0]);

        if (in_array('Dine-in', $reach)) {
            $count--;
        }

        // Formulas

        foreach ($sums as $processBuilder => $value) {
            // print_r($sums);
            // die();
            if ($processBuilder == 'A. PROCUREMENT' || $processBuilder == 'A.  PROCUREMENT') {

                $totalPROCUREMENT = $value;

                $overheads[$processBuilder] = round(($totalPROCUREMENT / $total_sales) * $multiplier, 2);

                // die();
            } elseif ($processBuilder == 'B. STORAGE' || $processBuilder == 'B.  STORAGE') {
                $totalSTORAGE = $value;

                $overheads[$processBuilder] = round(($totalSTORAGE / $total_sales) * $multiplier, 2);
                // print_r($overheads);
                // die();
            } elseif ($processBuilder == 'C1.  PREPARATION - FOOD' || $processBuilder == 'C1. PREPARATION - FOOD') {


                $overheadKey = isset($labourData['C1.  PREPARATION - FOOD']) ? 'C1.  PREPARATION - FOOD' : 'C1. PREPARATION - FOOD';
                for ($i = 0; $i <= count($data['preparation']); $i++) {
                    $result = ($data['preparation'][$i] / $labourData[$overheadKey]) * $value;
                    if ($result == 0) {
                        $overheads[$processBuilder] += 0;
                        // print_r($overheads);
                        // die();
                    } else {
                        $overheads[$processBuilder] += round($result / $data['batch'][$i], 2);
                    }
                }
            } elseif ($processBuilder == 'C2.  PREPARATION - BEVERAGES' || $processBuilder == 'C2. PREPARATION - BEVERAGES') {

                $overheadKey = isset($labourData['C2.  PREPARATION - BEVERAGES']) ? 'C2.  PREPARATION - BEVERAGES' : 'C2. PREPARATION - BEVERAGES';
                for ($i = 0; $i <= count($data['preparation']); $i++) {
                    $result = ($data['preparation'][$i] / $labourData[$overheadKey]) * $value;
                    if ($result == 0) {
                        $overheads[$processBuilder] += 0;
                    } else {
                        $overheads[$processBuilder] += round($result / $data['batch'][$i], 2);
                    }
                }
                // print_r($labourData[$overheadKey]);
                // die();
            } elseif ($processBuilder == 'D1.  COOKING - FOOD' || $processBuilder == 'D1. COOKING - FOOD') {

                $overheadKey = isset($labourData['D1.  COOKING - FOOD']) ? 'D1.  COOKING - FOOD' : 'D1. COOKING - FOOD';
                $overheads[$processBuilder] = round(($data['cooking'] / $labourData[$overheadKey]) * $value, 2);
                // print_r($overheads);
                // die();
            } 
            // elseif ($processBuilder == 'D2.  MIXING - BEVERAGES' || $processBuilder == 'D2. MIXING - BEVERAGES') {

            //     $overheadKey = isset($labourData['D2.  MIXING - BEVERAGES']) ? 'D2.  MIXING - BEVERAGES' : 'D2. MIXING - BEVERAGES';
            //     $overheads[$processBuilder] = round(($data['cooking'] / $labourData[$overheadKey]) * $value, 2);
            //     // print_r($overheads);
            //     // die();
            // } 
            elseif (($processBuilder == 'E1. DINE-IN ORDERING') || ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING') || ($processBuilder == 'E3. OWN PLATFORM ORDERING') || ($processBuilder == 'E4. TAKEAWAY ORDERING') || ($processBuilder == 'G1. DINE-IN SERVING') || ($processBuilder == 'G2. DELIVERY PLATFORM SERVING') || ($processBuilder == 'G3. OWN PLATFORM SERVING') || ($processBuilder == 'G4. TAKEAWAY SERVING') || ($processBuilder == 'H1. DINE-IN PAYING') || ($processBuilder == 'H2. DELIVERY PLATFORM PAYING') || ($processBuilder == 'H4. TAKEAWAY PAYING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    $overheads[$processBuilder] = round($value / $food[0]['Dine-in'], 2);
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    $overheads[$processBuilder] = round($value / $food[0]['Delivery Platform Service'], 2);
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    $overheads[$processBuilder] = round($value / $food[0]['Own Platform'], 2);
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    $overheads[$processBuilder] = round($value / $food[0]['Takeaway'], 2);
                }
                // print_r($overheads);
                // die();
            } elseif (($processBuilder == 'F1.  DINE-IN PRESENTATION') || ($processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING') || ($processBuilder == 'F3.  OWN PLATFORM PACKAGING') || ($processBuilder == 'F4.  TAKEAWAY PACKAGING') || ($processBuilder == 'F1. DINE-IN PRESENTATION') || ($processBuilder == 'F2. DELIVERY PLATFORM PACKAGING') || ($processBuilder == 'F3. OWN PLATFORM PACKAGING') || ($processBuilder == 'F4. TAKEAWAY PACKAGING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    $overheads[$processBuilder] = round($value / $FOOD['Dine-in'], 2);
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    $overheads[$processBuilder] = round($value / $FOOD['Delivery Platform Service'], 2);
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    $overheads[$processBuilder] = round($value / $FOOD['Own Platform'], 2);
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    $overheads[$processBuilder] = round($value / $FOOD['Takeaway'], 2);
                }
                // print_r($overheads);
                // die();
            } elseif (($processBuilder == 'E1.  DINE-IN ORDERING') || ($processBuilder == 'E2.  DELIVERY PLATFORM ORDERING') || ($processBuilder == 'E3.  OWN PLATFORM ORDERING') || ($processBuilder == 'E4.  TAKEAWAY ORDERING') || ($processBuilder == 'G1.  DINE-IN SERVING') || ($processBuilder == 'G2.  DELIVERY PLATFORM SERVING') || ($processBuilder == 'G3.  OWN PLATFORM SERVING') || ($processBuilder == 'G4.  TAKEAWAY SERVING') || ($processBuilder == 'H1.  DINE-IN PAYING') || ($processBuilder == 'H2.  DELIVERY PLATFORM PAYING') || ($processBuilder == 'H4.  TAKEAWAY PAYING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    $overheads[$processBuilder] = round($value / $food[0]['Dine-in'], 2);
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    $overheads[$processBuilder] = round($value / $food[0]['Delivery Platform Service'], 2);
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    $overheads[$processBuilder] = round($value / $food[0]['Own Platform'], 2);
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    $overheads[$processBuilder] = round($value / $food[0]['Takeaway'], 2);
                }
                // print_r($overheads);
                // die();
            } elseif ($processBuilder == 'I. CLEANING' || $processBuilder == 'I.  CLEANING') {
                $totalCLEANING = $value;

                if (in_array('Dine-in', $reach)) {
                    if (count($reachData) != 1) {
                        if ($data['sales_channel'] != 'Dine-in') {
                            $traceable = $totalCLEANING * (0.05 / $count);
                            $overheads[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                        } else {
                            $traceable = $totalCLEANING * 0.95;
                            $overheads[$processBuilder] = round($traceable / $food[0]['Dine-in'], 2);
                        }
                    } else {
                        $overheads[$processBuilder] = round($totalCLEANING / $total_sales, 2);
                    }
                } else {
                    $traceable = $totalCLEANING * (1/ $count);
                    $overheads[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                }

                // print_r($overheads);
                // die();
            } elseif ($processBuilder == 'J. FACILITY MANAGEMENT' || $processBuilder == 'J.  FACILITY MANAGEMENT') {
                $totalFACILITY_MANAGEMENT = $value;

                if (in_array('Dine-in', $reach)) {
                    if (count($reachData) != 1) {
                        if ($data['sales_channel'] != 'Dine-in') {
                            $traceable = $totalFACILITY_MANAGEMENT * (0.05 / $count);
                            $overheads[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                        } else {
                            $traceable = $totalFACILITY_MANAGEMENT * 0.95;
                            $overheads[$processBuilder] = round($traceable / $food[0]['Dine-in'], 2);
                        }
                    } else {
                        $overheads[$processBuilder] = round($totalFACILITY_MANAGEMENT / $total_sales, 2);
                    }
                } else {
                    $traceable = $totalFACILITY_MANAGEMENT * (1/ $count);
                    $overheads[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                }

                // print_r($overheads);
                // die();
            } elseif ($processBuilder == 'K. EMPLOYEE WELFARE' || $processBuilder == 'K.  EMPLOYEE WELFARE') {
                $totalSTORAGE = $value;

                $overheads[$processBuilder] = round($totalSTORAGE / $total_sales, 2);
                // print_r($overheads);
                // die();
            } elseif ($processBuilder == 'L. ADMINISTRATIVE' || $processBuilder == 'L.  ADMINISTRATIVE') {
                $totalSTORAGE = $value;

                $overheads[$processBuilder] = round($totalSTORAGE / $total_sales, 2);
                // print_r($overheads);
                // die();
            }
        }
        foreach ($overheads as $key => $value) {
            $overheads[$key] = round($value, 2);
        }
        ksort($overheads);
        // print_r($sums2);die();
        return $overheads;
    }

    public function getOverheads($userId, $sales_channel)
    {
        $i = 0;
        $j = 0;
        $sums = [];
        $this->db->select('*');
        $this->db->from('tbloverheads_user_data');
        $this->db->where('restaurant', $userId);
        $result = $this->db->get()->result_array();

        if (!empty($result)) {
            foreach ($result as $eachData) {

                $data = unserialize($eachData['datas']);
                $cycle = $eachData['cycle'];

                foreach ($data as $singleData) {
                    foreach ($singleData['processBuilderRelationship'] as $processBuilder => $percentage) {
                        if ($sales_channel == 'Dine-in') {

                            if (!$percentage) {
                                continue;
                            }

                            if ($processBuilder == 'E1. DINE-IN ORDERING' || $processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'G1. DINE-IN SERVING' || $processBuilder == 'H1. DINE-IN PAYING' || $processBuilder == 'E1.  DINE-IN ORDERING' || $processBuilder == 'F1.  DINE-IN PRESENTATION' || $processBuilder == 'G1.  DINE-IN SERVING' || $processBuilder == 'H1.  DINE-IN PAYING') {

                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            } else {
                                if ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2. DELIVERY PLATFORM SERVING' || $processBuilder == 'H2. DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3. OWN PLATFORM ORDERING' || $processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'G3. OWN PLATFORM SERVING' || $processBuilder == 'H3. OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4. TAKEAWAY ORDERING' || $processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'G4. TAKEAWAY SERVING' || $processBuilder == 'H4. TAKEAWAY PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E2.  DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2.  DELIVERY PLATFORM SERVING' || $processBuilder == 'H2.  DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3.  OWN PLATFORM ORDERING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING' || $processBuilder == 'G3.  OWN PLATFORM SERVING' || $processBuilder == 'H3.  OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4.  TAKEAWAY ORDERING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING' || $processBuilder == 'G4.  TAKEAWAY SERVING' || $processBuilder == 'H4.  TAKEAWAY PAYING') {
                                    continue;
                                }


                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            }
                            // print_r($result1);
                            // die();
                        } elseif ($sales_channel == 'Delivery Platform Service') {
                            if ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2. DELIVERY PLATFORM SERVING' || $processBuilder == 'H2. DELIVERY PLATFORM PAYING' || $processBuilder == 'E2.  DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2.  DELIVERY PLATFORM SERVING' || $processBuilder == 'H2.  DELIVERY PLATFORM PAYING') {
                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            } else {
                                if ($processBuilder == 'E1. DINE-IN ORDERING' || $processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'G1. DINE-IN SERVING' || $processBuilder == 'H1. DINE-IN PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3. OWN PLATFORM ORDERING' || $processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'G3. OWN PLATFORM SERVING' || $processBuilder == 'H3. OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4. TAKEAWAY ORDERING' || $processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'G4. TAKEAWAY SERVING' || $processBuilder == 'H4. TAKEAWAY PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E1.  DINE-IN ORDERING' || $processBuilder == 'F1.  DINE-IN PRESENTATION' || $processBuilder == 'G1.  DINE-IN SERVING' || $processBuilder == 'H1.  DINE-IN PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3.  OWN PLATFORM ORDERING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING' || $processBuilder == 'G3.  OWN PLATFORM SERVING' || $processBuilder == 'H3.  OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4.  TAKEAWAY ORDERING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING' || $processBuilder == 'G4.  TAKEAWAY SERVING' || $processBuilder == 'H4.  TAKEAWAY PAYING') {
                                    continue;
                                }

                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            }
                        } elseif ($sales_channel == 'Own Platform') {
                            if ($processBuilder == 'E3. OWN PLATFORM ORDERING' || $processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'G3. OWN PLATFORM SERVING' || $processBuilder == 'H3. OWN PLATFORM PAYING' || $processBuilder == 'E3.  OWN PLATFORM ORDERING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING' || $processBuilder == 'G3.  OWN PLATFORM SERVING' || $processBuilder == 'H3.  OWN PLATFORM PAYING') {
                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            } else {
                                if ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2. DELIVERY PLATFORM SERVING' || $processBuilder == 'H2. DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E1. DINE-IN ORDERING' || $processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'G1. DINE-IN SERVING' || $processBuilder == 'H1. DINE-IN PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4. TAKEAWAY ORDERING' || $processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'G4. TAKEAWAY SERVING' || $processBuilder == 'H4. TAKEAWAY PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E2.  DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2.  DELIVERY PLATFORM SERVING' || $processBuilder == 'H2.  DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E1.  DINE-IN ORDERING' || $processBuilder == 'F1.  DINE-IN PRESENTATION' || $processBuilder == 'G1.  DINE-IN SERVING' || $processBuilder == 'H1.  DINE-IN PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E4.  TAKEAWAY ORDERING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING' || $processBuilder == 'G4.  TAKEAWAY SERVING' || $processBuilder == 'H4.  TAKEAWAY PAYING') {
                                    continue;
                                }


                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            }
                        } elseif ($sales_channel == 'Takeaway') {
                            if ($processBuilder == 'E4. TAKEAWAY ORDERING' || $processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'G4. TAKEAWAY SERVING' || $processBuilder == 'H4. TAKEAWAY PAYING' || $processBuilder == 'E4.  TAKEAWAY ORDERING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING' || $processBuilder == 'G4.  TAKEAWAY SERVING' || $processBuilder == 'H4.  TAKEAWAY PAYING') {
                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            } else {
                                if ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2. DELIVERY PLATFORM SERVING' || $processBuilder == 'H2. DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3. OWN PLATFORM ORDERING' || $processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'G3. OWN PLATFORM SERVING' || $processBuilder == 'H3. OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E1. DINE-IN ORDERING' || $processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'G1. DINE-IN SERVING' || $processBuilder == 'H1. DINE-IN PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E2.  DELIVERY PLATFORM ORDERING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING' || $processBuilder == 'G2.  DELIVERY PLATFORM SERVING' || $processBuilder == 'H2.  DELIVERY PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E3.  OWN PLATFORM ORDERING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING' || $processBuilder == 'G3.  OWN PLATFORM SERVING' || $processBuilder == 'H3.  OWN PLATFORM PAYING') {
                                    continue;
                                }
                                if ($processBuilder == 'E1.  DINE-IN ORDERING' || $processBuilder == 'F1.  DINE-IN PRESENTATION' || $processBuilder == 'G1.  DINE-IN SERVING' || $processBuilder == 'H1.  DINE-IN PAYING') {
                                    continue;
                                }

                                if ($cycle == 1) {
                                    $amount = $singleData['extrapolation'];
                                } else {
                                    $amount = $singleData['spent'];
                                }

                                $value = ($percentage * $amount) / 100;
                                $result1[$i][$processBuilder] =  $value;
                            }
                        }
                    };
                    $i++;
                }
            }


            $processBuilderData = $result1;

            foreach ($processBuilderData as $subArray) {

                foreach ($subArray as $key => $value) {
                    if (array_key_exists($key, $sums)) {
                        $sums[$key] += $value;
                    } else {
                        $sums[$key] = $value;
                    }
                }
            }
        }
        // print_r($sums);
        // die();
        return $sums;
    }

    public function getLabour($userId, $month, $year)
    {
        $sums = [];

        $this->db->select('*');
        $this->db->from('tbllabour_data');
        $this->db->where('r_id', $userId);
        $result = $this->db->get()->result_array();

        if (!empty($result)) {

            $this->db->select('days');
            $this->db->from('tblmonth');
            $this->db->where('name', $month);
            $Month = $this->db->get()->result_array();

            $isLeapYear = date('L', strtotime("$year-01-01"));
            if ($isLeapYear && $month=="February") {
                $Month[0]['days'] += 1;
            }

            $result1 = [];

            foreach ($result as $eachData) {

                $this->db->where_in('r_id', $userId);
                $this->db->where_in('year', $year);
                $this->db->where_in('month', $month);
                $this->db->where_in('e_id', $eachData['e_id']);
                $this->db->select('days');
                $restdays = $this->db->get('tbllabour_restdays')->result_array();

                if (!$restdays) {
                    $restdays = 0;
                } else {
                    $restdays = unserialize($restdays[0]['days']);
                    $restdays = count($restdays);
                }
                $days = $Month[0]['days'] - $restdays;

                $data = unserialize($eachData['activity']);
                $productivity = $eachData['productivity'];
                $monthHours = $productivity * $days;

                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['activity'] == 'C1.  PREPARATION - FOOD' || $data[$i]['activity'] == 'C2.  PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1.  COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    } elseif ($data[$i]['activity'] == 'C1. PREPARATION - FOOD' || $data[$i]['activity'] == 'C2. PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1. COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    }
                }
            }
            // print_r($result1);
            // die();

            $processBuilderData = $result1;

            foreach ($processBuilderData as $subArray) {

                foreach ($subArray as $key => $value) {
                    if (array_key_exists($key, $sums)) {
                        $sums[$key] += $value;
                    } else {
                        $sums[$key] = $value;
                    }
                }
            }
        }
        // print_r($sums);
        // die();
        return $sums;
    }

    public function direct_labour($id, $month, $year)
    {
        $sums = [];
        $sums1 = [];
        $this->db->select('*');
        $this->db->from('tbllabour_data');
        $this->db->where('r_id', $id);
        $result = $this->db->get()->result_array();

        if (!empty($result)) {

            $this->db->select('days');
            $this->db->from('tblmonth');
            $this->db->where('name', $month);
            $Month = $this->db->get()->result_array();

            $isLeapYear = date('L', strtotime("$year-01-01"));
            if ($isLeapYear && $month=="February") {
                $Month[0]['days'] += 1;
            }

            $result1 = [];
            $result2 = [];

            foreach ($result as $eachData) {

                $this->db->where_in('r_id', $id);
                $this->db->where_in('year', $year);
                $this->db->where_in('month', $month);
                $this->db->where_in('e_id', $eachData['e_id']);
                $this->db->select('days');
                $restdays = $this->db->get('tbllabour_restdays')->result_array();

                if (!$restdays) {
                    $restdays = 0;
                } else {
                    $restdays = unserialize($restdays[0]['days']);
                    $restdays = count($restdays);
                }

                $days = $Month[0]['days'] - $restdays;

                $data = unserialize($eachData['activity']);
                $productivity = $eachData['productivity'];
                $monthHours = $productivity * $days;

                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['activity'] == 'C1.  PREPARATION - FOOD' || $data[$i]['activity'] == 'C2.  PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1.  COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    } elseif ($data[$i]['activity'] == 'C1. PREPARATION - FOOD' || $data[$i]['activity'] == 'C2. PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1. COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    }
                }
            }

            $processBuilderData = $result1;
            foreach ($processBuilderData as $subArray) {
                foreach ($subArray as $key => $value) {

                    if ($key == 'C1. PREPARATION - FOOD') {
                        $key = 'C1.  PREPARATION - FOOD';
                    } elseif ($key == 'C2. PREPARATION - BEVERAGES') {
                        $key = 'C2.  PREPARATION - BEVERAGES';
                    } elseif ($key == 'D1. COOKING - FOOD') {
                        $key = 'D1.  COOKING - FOOD';
                    }

                    if (array_key_exists($key, $sums)) {
                        $sums[$key] += $value;
                    } else {
                        $sums[$key] = $value;
                    }
                }
            }
        }
        // print_r($sums);
        // die();
        if (!empty($result)) {
            foreach ($result as $eachdata) {

                $data = unserialize($eachdata['activity']);
                $traceable = $eachdata['traceable'];

                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]['activity'] == 'C1.  PREPARATION - FOOD' || $data[$i]['activity'] == 'C2.  PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1.  COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($traceable * $percent) / 100;
                        $result2[$i][$data[$i]['activity']] +=  $value;
                    } elseif ($data[$i]['activity'] == 'C1. PREPARATION - FOOD' || $data[$i]['activity'] == 'C2. PREPARATION - BEVERAGES' || $data[$i]['activity'] == 'D1. COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($traceable * $percent) / 100;
                        $result2[$i][$data[$i]['activity']] +=  $value;
                    }
                }
            }
            // print_r($result2);die;
            $processBuilderData = $result2;

            foreach ($processBuilderData as $subArray) {

                foreach ($subArray as $key => $value) {
                    
                    if ($key == 'C1. PREPARATION - FOOD') {
                        $key = 'C1.  PREPARATION - FOOD';
                    } elseif ($key == 'C2. PREPARATION - BEVERAGES') {
                        $key = 'C2.  PREPARATION - BEVERAGES';
                    } elseif ($key == 'D1. COOKING - FOOD') {
                        $key = 'D1.  COOKING - FOOD';
                    }

                    if (array_key_exists($key, $sums1)) {
                        $sums1[$key] += $value;
                    } else {
                        $sums1[$key] = $value;
                    }
                }
            }
        }

        // print_r($sums1);die();


        foreach ($sums as $processBuilder => $value) {

            if (stripos($processBuilder, "BEVERAGES") !== false) {
                $beverage = round(($sums1[$processBuilder] / $value), 2);
                $overheads['beverage'][$processBuilder] = number_format($beverage,2);
            } else {
                $food = round(($sums1[$processBuilder] / $value), 2);
                $overheads['food'][$processBuilder] = number_format($food,2);
            }
            

        }

        return $overheads;
    }

    public function save_costing($id, $year, $month, $data)
    {

        $datecreated = date('Y-m-d H:i:s');

        $extra['allChannelSameCost'] = $data['allChannelSameCost'];
        $extra['name'] = ucwords(strtolower($data['name']));
        $data['name'] = ucwords(strtolower($data['name']));

        $costing = [
            'r_id' => $id,
            'month' => $month,
            'year' => $year,
            'name' => $data['name'],
            'type' => $data['type'],
            'general_batch' => $data['general_batch'],
            'general_servings' => $data['general_servings'],
            'product_sales' => $data['product_sales'],
            'sales_channel' => $data['sales_channel'],
            'sales_price' => $data['sales_price'],
            'ingredients' => serialize($data['ingredients']),
            'direct_labour' => serialize($data['costingLabour']),
            'indirect_labour' => serialize($data['indirectLabour']),
            'overheads' => serialize($data['overheads']),
            'extra' => serialize($extra),
            'datecreated' => $datecreated
        ];

        $this->db->where('r_id', $id);
        $this->db->where('month', $month);
        $this->db->where('year', $year);
        $this->db->where('name', $data['name']);
        $this->db->where('sales_channel', $data['sales_channel']);
        $Result = $this->db->get(db_prefix() . 'costing')->result_array();

        if (!$Result) {

            $this->db->insert(db_prefix() . 'costing', $costing);

            $result = $this->db->insert_id();

        } else {

            $this->db->where('id', $Result[0]['id']);
            $result = $this->db->update(db_prefix() . 'costing', $costing);

            if ($result) {
                $result = $Result[0]['id'];
            }
        }

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function fetch_costing($id)
    {

        $this->db->where_in('id', $id);
        $Result1 = $this->db->get('tblcosting')->row_array();

        $this->db->where_in('c_id', $id);
        $Result2 = $this->db->get('tblcosting_performance')->row_array();

        if ($Result1 && $Result2) {

            $result['id'] = $Result1['r_id'];
            $result['month'] = $Result1['month'];
            $result['year'] = $Result1['year'];
            $result['name'] = $Result1['name'];
            $result['general_batch'] = $Result1['general_batch'];
            $result['general_servings'] = $Result1['general_servings'];
            $result['product_sales'] = $Result1['product_sales'];
            $result['sales_channel'] = $Result1['sales_channel'];
            $result['sales_price'] = $Result1['sales_price'];
            $result['ingredients'] = unserialize($Result1['ingredients']);
            $result['direct_labour'] = unserialize($Result1['direct_labour']);
            $result['indirect_labour'] = unserialize($Result1['indirect_labour']);
            $result['overheads'] = unserialize($Result1['overheads']);
            $result['extra'] = unserialize($Result1['extra']);
            $result['performance'] = unserialize($Result2['data']);

            return $result;
        } else {
            return false;
        }
    }

    public function delete_costing($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete(db_prefix() . 'costing');

        $this->db->where('c_id', $id);
        $result = $this->db->delete(db_prefix() . 'costing_performance');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function fetch_sides($id, $sideName)
    {

        $this->db->where('r_id', $id);
        $this->db->where('name', $sideName);
        $Result = $this->db->get(db_prefix() . 'costing_sidedish')->result_array();

        if ($Result) {
            $i = 0;
            foreach ($Result as  $value) {
                $result[$i]['name'] = $value['name'];
                $result[$i]['batch'] = $value['batch'];
                $result[$i]['servings'] = $value['servings'];
                $result[$i]['ingredients'] = unserialize($value['ingredients']);
                $result[$i]['dlh'] = $value['dlh'];
                $servings = ($result[0]["batch"] == "Batch") ? $result[0]["servings"] : 1;
                $i++;
            }
            $this->load->model('api/Ingredient_model');
            $data = $this->Ingredient_model->selectingIngredients_costing($id);
            $true = [];

            foreach ($result[0]['ingredients'] as $ingredientCATEGORIES) {
                foreach ($ingredientCATEGORIES['ingredients'] as $value) {
                    if ($value['isChecked'] == true) {
                        $true[] = $value['ingredient'];
                        $Values[$value['ingredient']] = $value;
                    }
                }
            }

            foreach ($data as $KEY => $ingredientCATEGORIES) {
                foreach ($ingredientCATEGORIES['ingredients'] as $key => $value) {
                    if (in_array($value['ingredient'], $true)) {
                        $data[$KEY]['ingredients'][$key]['isChecked'] = true;
                        $data[$KEY]['ingredients'][$key]['category'] = $Values[$value['ingredient']]['category'];
                        $data[$KEY]['ingredients'][$key]['unitOfMeasure'] = $Values[$value['ingredient']]['unitOfMeasure'];
                        $measure = $Values[$value['ingredient']]['unitOfMeasure'];
                        $data[$KEY]['ingredients'][$key]['quantity'] = $Values[$value['ingredient']]['quantity'];
                        $UnitOfMeasure = array('1' => '1', '2' => '1', '3' => '0.001', '4' => '0.005', '5' => '0.015', '6' => '0.002', '7' => '1', '8' => '0.001');
                        $batchCost = $Values[$value['ingredient']]['quantity'] * $UnitOfMeasure[$measure] * $data[$KEY]['ingredients'][$key]['unit_price'];
                        $data[$KEY]['ingredients'][$key]['batchCost'] = round($batchCost,2);
                        $unitCost = round($data[$KEY]['ingredients'][$key]['batchCost'] / $servings, 2);
                        $data[$KEY]['ingredients'][$key]['unitCost'] = round($unitCost, 2);
                    } else {
                        $data[$KEY]['ingredients'][$key]['isChecked'] = false;
                    }
                }
            }

            $result[0]['ingredients'] = $data;

            return $result;
        } else {
            return false;
        }
    }

    public function indirect_labour($id, $year, $month, $data)
    {

        $sums = [];
        $labour = [];
        $others = 0;
        $count = 0;

        $sums = $this->get_indirect_labour($id, $month, $year);

        // foreach ($getData as $key => $value) {

        //     if (array_key_exists($key, $sums)) {
        //         $sums[$key] += $value;
        //     } else {
        //         $sums[$key] = $value;
        //     }
        // }
        // print_r($sums);
        // die();

        // Check for sales type

        if ($data['salesType'] != 'Food') {
            foreach ($sums as $processBuilder => $value) {

                if ($processBuilder == 'C1.  PREPARATION - FOOD' || $processBuilder == 'C1. PREPARATION - FOOD') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'D1.  COOKING - FOOD' || $processBuilder == 'D1. COOKING - FOOD') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F4. TAKEAWAY PACKAGING' || $processBuilder == 'F4.  TAKEAWAY PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F3. OWN PLATFORM PACKAGING' || $processBuilder == 'F3.  OWN PLATFORM PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                if ($processBuilder == 'F2. DELIVERY PLATFORM PACKAGING' || $processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING') {
                    unset($sums[$processBuilder]);
                }
                // if ($processBuilder == 'F1. DINE-IN PRESENTATION' || $processBuilder == 'F1.  DINE-IN PRESENTATION') {
                //     unset($sums[$processBuilder]);
                // }
            }
        } else {
            foreach ($sums as $processBuilder => $value) {

                if ($processBuilder == 'C2.  PREPARATION - BEVERAGES' || $processBuilder == 'C2. PREPARATION - BEVERAGES') {
                    unset($sums[$processBuilder]);
                }
                // if ($processBuilder == 'D2.  MIXING - BEVERAGES' || $processBuilder == 'D2. MIXING - BEVERAGES') {
                //     unset($sums[$processBuilder]);
                // }
            }
        }

        $salesData = $this->db->select('*')
            ->from('tblrestaurant_sales_data')
            ->where('r_id', $id)
            ->where('month', $month)
            ->get()->result_array();
        $a = $salesData[0]['add_values'];
        $b = $salesData[0]['food_data'];
        // $c = $salesData[0]['beverage_data'];
        $food = unserialize($a);
        $Food_Data = unserialize($b);
        // $beverage_data = unserialize($c);
        $total_sales = $food[0]['food_Total'] + $food[0]['beverages_Total'];
        // $food_sales = $food[0]['food_Total'];
        $Multiplier = array('1' => '0.8', '2' => '0.8', '3' => '0.9', '4' => '0.9', '5' => '1.0', '6' => '1.0', '7' => '1.1', '8' => '1.1');
        $multiplier = ($data['ing_count'] < 9) ? $Multiplier[$data['ing_count']] : 1.2;

        $reachData = $this->db->select('reach')
            ->from('tblrestaurant_reach_data')
            ->where('r_id', $id)
            // ->where('month', $month)
            ->get()->result_array();

        for ($i = 0; $i < count($reachData); $i++) {
            $reach[$i] = $reachData[$i]['reach'];
            $R = $reachData[$i]['reach'];
            $FOOD[$R] = $Food_Data[$i];
        }

        unset($food[0]['food_Total']);
        unset($food[0]['beverages_Total']);
        $count = count($food[0]);

        if (in_array('Dine-in', $reach)) {
            $count--;
        }

        // print_r($data['sales_type']);die();
        // Formulas

        // Procurement

        foreach ($sums as $processBuilder => $value) {
            // print_r($sums);
            // die();
            if ($processBuilder == 'A. PROCUREMENT' || $processBuilder == 'A.  PROCUREMENT') {
                $totalPROCUREMENT = $value;

                $labour[$processBuilder] = round(($totalPROCUREMENT / $total_sales) * $multiplier, 2);

                // die();
            } elseif ($processBuilder == 'B. STORAGE' || $processBuilder == 'B.  STORAGE') {
                $totalSTORAGE = $value;

                $labour[$processBuilder] = round(($totalSTORAGE / $total_sales) * $multiplier, 2);
                // print_r($labour);
                // die();
            }
            // elseif ($processBuilder == 'C1.  PREPARATION - FOOD') {

            //     $labour[$processBuilder] = round(($data['preparation'] / $labourData['C1.  PREPARATION - FOOD']) * $value, 2);
            //     // print_r($labour);
            //     // die();
            // } elseif ($processBuilder == 'C2.  PREPARATION - BEVERAGES') {

            //     $labour[$processBuilder] = round(($data['preparation'] / $labourData['C2.  PREPARATION - BEVERAGES']) * $value, 2);
            //     // print_r($labour);
            //     // die();
            // } elseif ($processBuilder == 'D1.  COOKING - FOOD') {

            //     $labour[$processBuilder] = round(($data['cooking'] / $labourData['D1.  COOKING - FOOD']) * $value, 2);
            //     // print_r($value);
            //     // die();
            // } elseif ($processBuilder == 'D2.  MIXING - BEVERAGES') {

            //     $labour[$processBuilder] = round(($data['cooking'] / $labourData['D2.  MIXING - BEVERAGES']) * $value, 2);
            //     // print_r($labour);
            //     // die();
            // } 
            elseif (($processBuilder == 'E1.  DINE-IN ORDERING') || ($processBuilder == 'E2.  DELIVERY PLATFORM ORDERING') || ($processBuilder == 'E3.  OWN PLATFORM ORDERING') || ($processBuilder == 'E4.  TAKEAWAY ORDERING') || ($processBuilder == 'G1.  DINE-IN SERVING') || ($processBuilder == 'G2.  DELIVERY PLATFORM SERVING') || ($processBuilder == 'G3.  OWN PLATFORM SERVING') || ($processBuilder == 'G4.  TAKEAWAY SERVING') || ($processBuilder == 'H1.  DINE-IN PAYING') || ($processBuilder == 'H2.  DELIVERY PLATFORM PAYING') || ($processBuilder == 'H3.  OWN PLATFORM PAYING') || ($processBuilder == 'H4.  TAKEAWAY PAYING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    if (strpos($processBuilder, '1') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Dine-in'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    if (strpos($processBuilder, '2') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Delivery Platform Service'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    if (strpos($processBuilder, '3') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Own Platform'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    if (strpos($processBuilder, '4') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Takeaway'], 2);
                    }
                }
                // print_r($labour);
                // die();
            } elseif (($processBuilder == 'E1. DINE-IN ORDERING') || ($processBuilder == 'E2. DELIVERY PLATFORM ORDERING') || ($processBuilder == 'E3. OWN PLATFORM ORDERING') || ($processBuilder == 'E4. TAKEAWAY ORDERING') || ($processBuilder == 'G1. DINE-IN SERVING') || ($processBuilder == 'G2. DELIVERY PLATFORM SERVING') || ($processBuilder == 'G3. OWN PLATFORM SERVING') || ($processBuilder == 'G4. TAKEAWAY SERVING') || ($processBuilder == 'H1. DINE-IN PAYING') || ($processBuilder == 'H2. DELIVERY PLATFORM PAYING') || ($processBuilder == 'H3. OWN PLATFORM PAYING') || ($processBuilder == 'H4. TAKEAWAY PAYING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    if (strpos($processBuilder, '1') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Dine-in'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    if (strpos($processBuilder, '2') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Delivery Platform Service'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    if (strpos($processBuilder, '3') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Own Platform'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    if (strpos($processBuilder, '4') === 1) {
                        $labour[$processBuilder] = round($value / $food[0]['Takeaway'], 2);
                    }
                }
                // print_r($labour);
                // die();
            } elseif (($processBuilder == 'F1.  DINE-IN PRESENTATION') || ($processBuilder == 'F2.  DELIVERY PLATFORM PACKAGING') || ($processBuilder == 'F3.  OWN PLATFORM PACKAGING') || ($processBuilder == 'F4.  TAKEAWAY PACKAGING') || ($processBuilder == 'F1. DINE-IN PRESENTATION') || ($processBuilder == 'F2. DELIVERY PLATFORM PACKAGING') || ($processBuilder == 'F3. OWN PLATFORM PACKAGING') || ($processBuilder == 'F4. TAKEAWAY PACKAGING')) {

                if ($data['sales_channel'] == 'Dine-in') {
                    if (strpos($processBuilder, '1') === 1) {
                        $labour[$processBuilder] = round($value / $FOOD['Dine-in'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Delivery Platform Service') {
                    if (strpos($processBuilder, '2') === 1) {
                        $labour[$processBuilder] = round($value / $FOOD['Delivery Platform Service'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Own Platform') {
                    if (strpos($processBuilder, '3') === 1) {
                        $labour[$processBuilder] = round($value / $FOOD['Own Platform'], 2);
                    }
                } elseif ($data['sales_channel'] == 'Takeaway') {
                    if (strpos($processBuilder, '4') === 1) {
                        $labour[$processBuilder] = round($value / $FOOD['Takeaway'], 2);
                    }
                }
                // print_r($labour);
                // die();
            } elseif ($processBuilder == 'I. CLEANING' || $processBuilder == 'I.  CLEANING') {
                $totalCLEANING = $value;

                if (in_array('Dine-in', $reach)) {
                    if (count($reachData) != 1) {
                        if ($data['sales_channel'] != 'Dine-in') {
                            $traceable = $totalCLEANING * (0.05 / $count);
                            $labour[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                        } else {
                            $traceable = $totalCLEANING * 0.95;
                            $labour[$processBuilder] = round($traceable / $food[0]['Dine-in'], 2);
                        }
                    } else {
                        $labour[$processBuilder] = round($totalCLEANING / $total_sales, 2);
                    }
                } else {
                    $traceable = $totalCLEANING * (1/ $count);
                    $labour[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                }

                // print_r($labour);
                // die();
            } elseif ($processBuilder == 'J. FACILITY MANAGEMENT' || $processBuilder == 'J.  FACILITY MANAGEMENT') {
                $totalFACILITY_MANAGEMENT = $value;

                if (in_array('Dine-in', $reach)) {
                    if (count($reachData) != 1) {
                        if ($data['sales_channel'] != 'Dine-in') {
                            $traceable = $totalFACILITY_MANAGEMENT * (0.05 / $count);
                            $labour[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                        } else {
                            $traceable = $totalFACILITY_MANAGEMENT * 0.95;
                            $labour[$processBuilder] = round($traceable / $food[0]['Dine-in'], 2);
                        }
                    } else {
                        $labour[$processBuilder] = round($totalFACILITY_MANAGEMENT / $total_sales, 2);
                    }
                } else {
                    $traceable = $totalFACILITY_MANAGEMENT * (1/ $count);
                    $labour[$processBuilder] = round($traceable / $food[0][$data['sales_channel']], 2);
                }

                // print_r($labour);
                // die();
            } elseif ($processBuilder == 'K. EMPLOYEE WELFARE' || $processBuilder == 'K.  EMPLOYEE WELFARE') {
                $totalSTORAGE = $value;

                $labour[$processBuilder] = round($totalSTORAGE / $total_sales, 2);
                // print_r($labour);
                // die();
            } elseif ($processBuilder == 'L. ADMINISTRATIVE' || $processBuilder == 'L.  ADMINISTRATIVE') {
                $totalSTORAGE = $value;

                $labour[$processBuilder] = round($totalSTORAGE / $total_sales, 2);
                // print_r($labour);
                // die();
            }
        }

        foreach ($labour as $key => $value) {
            $labour[$key] = round($value, 2);
        }

        ksort($labour);
        // print_r($labour);die();
        return $labour;
    }

    public function get_indirect_labour($id, $month, $year)
    {
        $sums = [];
        $sums1 = [];
        $this->db->select('*');
        $this->db->from('tbllabour_data');
        $this->db->where('r_id', $id);
        $result = $this->db->get()->result_array();

        if (!empty($result)) {

            $this->db->select('days');
            $this->db->from('tblmonth');
            $this->db->where('name', $month);
            $Month = $this->db->get()->result_array();

            $isLeapYear = date('L', strtotime("$year-01-01"));
            if ($isLeapYear && $month == "February") {
                $Month[0]['days'] += 1;
            }

            $result1 = [];
            $result2 = [];

            foreach ($result as $eachData) {

                $this->db->where_in('r_id', $id);
                $this->db->where_in('year', $year);
                $this->db->where_in('month', $month);
                $this->db->where_in('e_id', $eachData['e_id']);
                $this->db->select('days');
                $restdays = $this->db->get('tbllabour_restdays')->result_array();

                // $this->db->where_in('year', $year);
                // $this->db->where_in('month', $month);
                // $this->db->select('day');
                // $result3 = $this->db->get(db_prefix() . 'labour_user_leave')->num_rows();

                if (!$restdays) {
                    $restdays = 0;
                } else {
                    $restdays = unserialize($restdays[0]['days']);
                    $restdays = count($restdays);
                }

                // if (!$result3) {
                //     $result3 = 0;
                // }

                // $restdays = $restdays + $result3;
                $days = $Month[0]['days'] - $restdays;

                $data = unserialize($eachData['activity']);
                $productivity = $eachData['productivity'];
                $monthHours = $productivity * $days;

                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['activity'] != 'C1.  PREPARATION - FOOD' && $data[$i]['activity'] != 'C2.  PREPARATION - BEVERAGES' && $data[$i]['activity'] != 'D1.  COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    } elseif ($data[$i]['activity'] != 'C1. PREPARATION - FOOD' && $data[$i]['activity'] != 'C2. PREPARATION - BEVERAGES' && $data[$i]['activity'] != 'D1. COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($monthHours * $percent) / 100;
                        $result1[$i][$data[$i]['activity']] +=  $value;
                    }
                }
            }

            $processBuilderData = $result1;

            foreach ($processBuilderData as $subArray) {

                foreach ($subArray as $key => $value) {
                    if (array_key_exists($key, $sums)) {
                        $sums[$key] += $value;
                    } else {
                        $sums[$key] = $value;
                    }
                }
            }
        }

        if (!empty($result)) {
            foreach ($result as $eachdata) {

                $data = unserialize($eachdata['activity']);
                $traceable = $eachdata['traceable'];

                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]['activity'] != 'C1.  PREPARATION - FOOD' && $data[$i]['activity'] != 'C2.  PREPARATION - BEVERAGES' && $data[$i]['activity'] != 'D1.  COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($traceable * $percent) / 100;
                        $result2[$i][$data[$i]['activity']] +=  $value;
                    } elseif ($data[$i]['activity'] != 'C1. PREPARATION - FOOD' && $data[$i]['activity'] != 'C2. PREPARATION - BEVERAGES' && $data[$i]['activity'] != 'D1. COOKING - FOOD') {

                        $percent = $data[$i]['percentage'];
                        $value = ($traceable * $percent) / 100;
                        $result2[$i][$data[$i]['activity']] +=  $value;
                    }
                }
            }

            $processBuilderData = $result2;

            foreach ($processBuilderData as $subArray) {

                foreach ($subArray as $key => $value) {
                    if (array_key_exists($key, $sums1)) {
                        $sums1[$key] += $value;
                    } else {
                        $sums1[$key] = $value;
                    }
                }
            }
        }

        // print_r($sums1);
        // die();
        return $sums1;
    }

    public function performance($id, $r_id, $month, $year)
    {

        $this->db->where_in('id', $id);
        $Result = $this->db->get('tblcosting')->result_array();

        if ($Result) {

            $Name = $Result[0]['name'];
            $sales_channel = $Result[0]['sales_channel'];
            $type = $Result[0]['type'];
            $sales_price = $Result[0]['sales_price'];
            $results['ingredients'] = unserialize($Result[0]['ingredients']);
            $results['direct_labour'] = unserialize($Result[0]['direct_labour']);
            $results['indirect_labour'][] = unserialize($Result[0]['indirect_labour']);
            $results['overheads'][] = unserialize($Result[0]['overheads']);

            $unitcost_labour = 0;
            $unitcost_ingredients = 0;
            $unitcost_business = 0;
            foreach ($results as $name => $result) {
                foreach ($result as $key => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    if ($name == "direct_labour") {
                        if (isset($value["foodData"]) && !empty($value["foodData"])) {
                            for ($i = 0; $i < count($value['foodData']); $i++) {
                                if (isset($value["foodData"][$i]["unitCost"]) && !empty($value["foodData"][$i]["unitCost"])) {
                                    $unitcost_labour += $value["foodData"][$i]["unitCost"];
                                }
                            }
                        }

                        if (isset($value["SideData"]) && !empty($value["SideData"])) {
                            for ($i = 0; $i < count($value["SideData"]); $i++) {
                                if (isset($value["SideData"][$i]["unitCost"]) && !empty($value["SideData"][$i]["unitCost"])) {
                                    $unitcost_labour += $value["SideData"][$i]["unitCost"];
                                }
                            }
                        }

                        if (isset($value["beverageData"]) && !empty($value["beverageData"])) {
                            for ($i = 0; $i < count($value["beverageData"]); $i++) {
                                if (isset($value["beverageData"][$i]["unitCost"]) && !empty($value["beverageData"][$i]["unitCost"])) {
                                    $unitcost_labour += $value["beverageData"][$i]["unitCost"];
                                }
                            }
                        }
                    }

                    if ($name == "ingredients") {
                        if (isset($value["premix"]) && !empty($value["premix"])) {
                            for ($i = 0; $i < count($value["premix"]); $i++) {
                                $unitcost_ingredients += $value["premix"][$i]["unitCost"];
                            }
                        } elseif (isset($value["mains"]) && !empty($value["mains"])) {
                            for ($i = 0; $i < count($value["mains"]); $i++) {
                                $unitcost_ingredients += $value["mains"][$i]["unitCost"];
                            }
                        } elseif (isset($value["sides"]) && !empty($value["sides"])) {
                            foreach ($value["sides"] as $category => $SidesValue) {
                                for ($i = 0; $i < count($SidesValue); $i++) {
                                    $unitcost_ingredients += $SidesValue[$i]["unitCost"];
                                }
                            }
                        }
                    }

                    if ($name == "indirect_labour") {
                        foreach ($value as $key => $Value) {
                            $unitcost_business += $Value;
                        }
                    }

                    if ($name == "overheads") {
                        foreach ($value as $key => $Value) {
                            $unitcost_business += $Value;
                        }
                    }
                }
            }

            $data['data']['total_cost'] = round($unitcost_labour + $unitcost_ingredients + $unitcost_business, 2);
            $data['data']['total_cost'] = round($data['data']['total_cost'], 2);
            $data['data']['profit'] = round($sales_price - $data['data']['total_cost'],2);

            if ($sales_price != 0) {
                $data['data']['cost_margin'] = round(($data['data']['profit'] / $sales_price) * 100, 2);
                $data['data']['cost_margin'] = round($data['data']['cost_margin'], 2);
            } else {
                $data['data']['cost_margin'] = 0;
            }

            $data['data']['cost_labour'] = round($unitcost_labour, 2);
            $data['data']['cost_business'] = round($unitcost_business, 2);
            $data['data']['cost_ingredients'] = round($unitcost_ingredients, 2);
            $data['data']['sales_price'] = $sales_price;

            $datecreated = date('Y-m-d H:i:s');

            $this->db->where_in('c_id', $id);
            $Result = $this->db->get('tblcosting_performance')->result_array();

            if (!$Result) {

                $saving = [
                    'r_id' => $r_id,
                    'c_id' => $id,
                    'month' => $month,
                    'year' => $year,
                    'name' => $Name,
                    'sales_channel' => $sales_channel,
                    'type' => $type,
                    'data' => serialize($data),
                    'datecreated' => $datecreated
                ];

                $this->db->insert(db_prefix() . 'costing_performance', $saving);
            } else {

                $saving = [
                    'sales_channel' => $sales_channel,
                    'data' => serialize($data),
                    'datecreated' => $datecreated
                ];

                $this->db->where('c_id', $id);
                $this->db->where('r_id', $r_id);                
                $this->db->update(db_prefix() . 'costing_performance', $saving);
            }

            $query = $this->db->last_query();
            $this->log_query($query, $Name, $r_id, $id);

            $this->db->where_in('sales_channel', $sales_channel);
            $this->db->where_in('month', $month);
            $this->db->where_in('year', $year);
            $this->db->where_in('r_id', $r_id);
            $Result = $this->db->get('tblcosting_performance')->result_array();

            if ($Result) {
                foreach ($Result as $key => $value) {
                    // if ($value['c_id'] == $id) {
                    //     continue;
                    // }
                    $datas[$key] = $value;
                    $datas[$key]['data'] = unserialize($datas[$key]['data']);
                    $rank[$datas[$key]["c_id"]] = $datas[$key]["data"]["data"]["cost_margin"];
                }

                $datas = isset($datas) ? $datas : null;

                if ($rank != null) {
                    arsort($rank);
                    $j = 0;

                    foreach ($rank as $key => $value) {
                        for ($i = 0; $i < count($datas); $i++) {
                            if ($datas[$i]["c_id"] == $key) {
                                $table = $datas[$i];

                                $data['table'][$j]['name'] = $table['name'];
                                $data['table'][$j]['type'] = $table['type'];
                                $data['table'][$j]['profit'] = $table['data']['data']['profit'];
                                $data['table'][$j]['percent'] = $table['data']['data']['cost_margin'];
                                $j++;
                            }
                        }
                    }
                }
            }

            return $data;
        } else {
            return false;
        }
    }

    public function homescreen($id, $month, $year, $sales_channel, $search = "")
    {

        if ($month == 'January') {
            $previousMonth = 'December';
        } elseif ($month == 'February') {
            $previousMonth = 'January';
        } elseif ($month == 'March') {
            $previousMonth = 'February';
        } elseif ($month == 'April') {
            $previousMonth = 'March';
        } elseif ($month == 'May') {
            $previousMonth = 'April';
        } elseif ($month == 'June') {
            $previousMonth = 'May';
        } elseif ($month == 'July') {
            $previousMonth = 'June';
        } elseif ($month == 'August') {
            $previousMonth = 'July';
        } elseif ($month == 'September') {
            $previousMonth = 'August';
        } elseif ($month == 'October') {
            $previousMonth = 'September';
        } elseif ($month == 'November') {
            $previousMonth = 'October';
        } elseif ($month == 'December') {
            $previousMonth = 'November';
        }

        if ($previousMonth == "December") {
            $previousyear = $year - 1;
        } else {
            $previousyear = $year;
        }

        $this->db->where_in('r_id', $id);
        if ($search != null && $search != false && $search != "") {
            $this->db->where_in('c_id', $search);
        }
        $this->db->where_in('month', $month);
        $this->db->where_in('year', $year);
        $this->db->where_in('sales_channel', $sales_channel);
        $this->db->order_by('id', 'DESC');
        $Result = $this->db->get('tblcosting_performance')->result_array();

        $this->db->where_in('r_id', $id);
        if ($search != null && $search != false && $search != "") {
            $this->db->where_in('id', $search);
        }
        $this->db->where_in('sales_channel', $sales_channel);
        $this->db->where_in('month', $month);
        $this->db->where_in('year', $year);
        $this->db->order_by('id', 'DESC');
        $this->db->select('id');
        $this->db->select('month');
        $this->db->select('name');
        // $this->db->limit(3);
        $Result1 = $this->db->get('tblcosting')->result_array();

        $datas = [];
        $data = [];
        if ($Result && $Result1) {
            foreach ($Result as $Key => $Value) {
                $unserializedData = unserialize($Value['data']);

                $datas['id'] = $Value["c_id"];
                $datas['name'] = $Value["name"];

                if ($unserializedData['data']["sales_price"]) {
                    $datas['price'] = $unserializedData['data']["sales_price"];
                } else {
                    $datas['price'] = $unserializedData['data']["total_cost"];
                }

                $total_cost = $unserializedData['data']["total_cost"];

                $datas['profit'] = round($unserializedData['data']["profit"], 2);
                $datas['margin'] = $unserializedData['data']["cost_margin"];

                $datas['profit_loss'] = ($datas['price'] >= $total_cost) ? true : false;

                $this->db->where_in('r_id', $id);
                $this->db->where_in('month', $previousMonth);
                $this->db->where_in('year', $previousyear);
                $this->db->where_in('sales_channel', $sales_channel);
                $this->db->where_in('name', $Value["name"]);
                $this->db->order_by('id', 'DESC');
                $this->db->select('data');
                $Result2 = $this->db->get('tblcosting_performance')->row_array();

                if ($Result2) {
                    $unserializeddata = unserialize($Result2['data']);
                    $previousmonth = $unserializeddata['data']["cost_margin"];
                    $datas['upDown'] = round($datas['margin'] - $previousmonth, 2);

                    if ($datas['upDown'] > 0) {
                        $datas['stat'] = "Higher";
                    } elseif ($datas['upDown'] <  0) {
                        $datas['stat'] = "Lower";
                        $datas['upDown'] = round($datas['upDown'] * -1, 2);
                    }
                } else {
                    $datas['upDown'] = false;
                }

                foreach ($datas as $KEY => $VALUE) {
                    $data['product'][$Key][$KEY] = $VALUE;
                }

                $datas = [];

                foreach ($Result1 as $key => $value) {
                    if ($value['name'] == $Value["name"]) {
                        $this->db->where_in('name', $Value["name"]);
                        $this->db->where_in('r_id', $id);
                        $this->db->where_in('sales_channel', $sales_channel);
                        // $this->db->order_by('id', 'DESC');
                        $Performances = $this->db->get('tblcosting_performance')->result_array();

                        foreach ($Performances as $Performance) {
                            $Performance_month = $Performance["month"];
                            $Performance = unserialize($Performance['data']);
                            $cost_margin = $Performance['data']['cost_margin'];

                            $data['product'][$key]['graphData']['month'][] = $Performance_month;
                            $data['product'][$key]['graphData']['data'][] = $cost_margin;
                        }
                    }
                }
            }

            foreach ($data['product'] as $key => $value) {
                if (!isset($value["graphData"]) || count($value) <= 5) {
                    unset($data['product'][$key]);
                }
            }

            $this->db->where_in('r_id', $id);
            $this->db->where_in('sales_channel', $sales_channel);
            $this->db->where_in('month', $month);
            $this->db->where_in('year', $year);
            $this->db->order_by('id', 'DESC');
            $this->db->select('name');
            $this->db->select('id');
            $data['search'] = $this->db->get('tblcosting')->result_array();

            // $i = 0;
            // foreach ($Result as $key => $value) {
            //     $data['search'][$i]['name'] = $value['name'];
            //     $data['search'][$i]['id'] = $value['id'];
            //     $i++;
            // }
            $present_data = array_column($data['search'],"name");

            sort($data['search']);

            $this->db->where_in('r_id', $id);
            $this->db->where_in('month', $previousMonth);
            $this->db->where_in('year', $previousyear);
            $this->db->where_in('sales_channel', $sales_channel);
            $this->db->order_by('id', 'DESC');
            $this->db->select('name');
            $this->db->select('id');
            $previous_costing = $this->db->get('tblcosting')->result_array();

            $previous_data = array_column($previous_costing,"name");

            $diff = array_diff($previous_data,$present_data);

            $i=0;
            foreach ($previous_costing as $key => $value) {
                if(array_search($value['name'],$diff)){
                    $data['shortcut'][$i]['name'] = $value['name'];
                    $data['shortcut'][$i]['id'] = $value['id'];
                    $i++;
                }
            }

            $this->db->select('reach');
            $this->db->where('r_id', $id);
            $data['salesChannel'] = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();

            return $data;
        } else {

            $this->db->where_in('r_id', $id);
            $this->db->where_in('sales_channel', $sales_channel);
            $this->db->where_in('month', $previousMonth);
            $this->db->where_in('year', $previousyear);
            $this->db->order_by('id', 'DESC');
            $this->db->select('name');
            $this->db->select('sales_channel');
            $this->db->select('id');
            $Result = $this->db->get('tblcosting')->result_array();

            $i = 0;
            foreach ($Result as $key => $value) {
                $data['search'][$i]['name'] = $value['name'];
                $data['search'][$i]['sales_channel'] = $value['sales_channel'];
                $data['search'][$i]['id'] = $value['id'];
                $i++;
            }

            $this->db->select('reach');
            $this->db->where('r_id', $id);
            $data['salesChannel'] = $this->db->get(db_prefix() . 'restaurant_reach_data')->result_array();

            return $data;
        }
    }

    // public function copy_costing($name, $id, $sales_channel)
    // {

    //     $this->db->where_in('name', $name);
    //     $this->db->where_in('sales_channel', $sales_channel);
    //     $this->db->where_in('r_id', $id);
    //     $this->db->order_by('id', 'desc');
    //     $this->db->limit('1');
    //     $Result1 = $this->db->get('tblcosting')->row_array();

    //     $this->db->where_in('name', $name);
    //     $this->db->where_in('sales_channel', $sales_channel);
    //     $this->db->where_in('r_id', $id);
    //     $this->db->order_by('id', 'desc');
    //     $this->db->limit('1');
    //     $Result2 = $this->db->get('tblcosting_performance')->row_array();

    //     if ($Result1 && $Result2) {

    //         $result['id'] = $Result1['r_id'];
    //         $result['month'] = $Result1['month'];
    //         $result['year'] = $Result1['year'];
    //         $result['name'] = $Result1['name'];
    //         $result['type'] = $Result1['type'];
    //         $result['general_batch'] = $Result1['general_batch'];
    //         $result['general_servings'] = $Result1['general_servings'];
    //         $result['product_sales'] = $Result1['product_sales'];
    //         $result['sales_channel'] = $data['sales_channel'] = $Result1['sales_channel'];
    //         $data['sales_type'] = $Result1["type"];
    //         $result['sales_price'] = $Result1['sales_price'];
    //         $result['ingredients'] = unserialize($Result1['ingredients']);
    //         $result['direct_labour'] = unserialize($Result1['direct_labour']);
    //         $result['indirect_labour'] = unserialize($Result1['indirect_labour']);
    //         $result['overheads'] = unserialize($Result1['overheads']);
    //         $result['extra'] = unserialize($Result1['extra']);
    //         $result['performance'] = unserialize($Result2['data']);

    //         // $currentDate = new DateTime();
    //         // $month = $currentDate->format('F');
    //         // $year = $currentDate->format('Y');

    //         $currentDate = new DateTime();
    //         // $month = $currentDate->format('F');
    //         $year = $currentDate->format('Y');
    //         $currentDate->modify('-1 month');
    //         $month = $currentDate->format('F');
    //         $this->db->where('month', $month);
    //         $this->db->where('year', $year);
    //         $this->db->where('r_id', $id);
    //         $this->db->like('salesdata', $result['name']);
    //         $this->db->select('salesdata');
    //         $sales_data = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
    //         // print_r($sales_data);die();

    //         if ($sales_data) {
    //             foreach ($sales_data as $value) {
    //                 $value = unserialize($value['salesdata']);
    //                 if ($name == $value["Item"]) {
    //                     $result['product_sales'] = $value["TableUnits"];
    //                     break;
    //                 }
    //             }
    //         } else {
    //             $result['product_sales'] = false;
    //         }

    //             $data['ing_count'] = 0;

    //             foreach ($result['ingredients'] as $KEY => $VALUE) {
    //                 foreach ($VALUE as $Key => $Value) {
    //                     foreach ($Value as $key => $value) {
    //                         if ($Key != "sides") {
    //                             $data['ing_count'] += 1;
    //                         }
    //                         $category = $value["category"];
    //                         $item = $value["itemName"];
    //                         $batchCost = $value["batchCost"];
    //                         $quantity = $value["quantity"];
    //                         $this->db->select('tblingredient_categories.category_id');
    //                         $this->db->from('tblingredient_categories');
    //                         $this->db->where('category', $category);
    //                         $category_id = $this->db->get()->result();
    //                         $category = $category_id[0]->category_id;

    //                         $this->db->from(db_prefix() . 'user_ingredients');
    //                         $this->db->where('restaurant', $id);
    //                         $this->db->where('category', $category);
    //                         $stockDatas = $this->db->get()->result_array();

    //                         $stockDatas = unserialize($stockDatas[0]["ingredients"]);

    //                         foreach ($stockDatas as $stock) {
    //                             $item=strtolower($item);
    //                             if ($item == strtolower($stock["ingredient"])) {
    //                                 $ingredient_id = $stock["ingredientId"];
    //                             }
    //                         }
    //                         // print_r($value);die;

    //                         $Today = new DateTime('today');
    //                         $Today = $Today->format('Y-m-d');

    //                         $query = $this->db->select('*')
    //                             ->from('tblingredient_stock_cards')
    //                             ->where('restaurant', $id)
    //                             ->where('category', $category)
    //                             ->where('item', $ingredient_id)
    //                             // ->where('datecreated <=', $Today)
    //                             ->order_by('id', 'DESC')
    //                             ->limit(4)
    //                             ->get()
    //                             ->result_array();

    //                         foreach ($query as $singleData) {
    //                             if (isset($singleData['pricePerUnit']) && $singleData['pricePerUnit'] !== "" && $singleData['pricePerUnit'] !== 0) {
    //                                 $UNIT[] = $singleData['pricePerUnit'];
    //                             }
    //                         }

    //                         $count = count($UNIT);
    //                         $sum = array_sum($UNIT);
    //                         unset($UNIT);

    //                         // settype($sum, 'Integer');
    //                         if ($sum != 0 && $count != 0) {
    //                             $unit_price = $sum / $count;
    //                             $unit_price = number_format((float)$unit_price, 2, '.', '');
    //                             // $unit_price = round($unit_price, 2);
    //                         } else {
    //                             $unit_price = 0; // Set a default value or handle it according to your logic
    //                             // break 1;
    //                         }
    //                         $result['ingredients'][$KEY][$Key][$key]["unitPrice"] = $unit_price;
    //                         if($batchCost != $unit_price){
    //                             $result['ingredients'][$KEY][$Key][$key]["batchCost"] = round($quantity * $unit_price,2);
    //                         }
    //                     }
    //                 }
    //             }

    //             $direct = $this->direct_labour($id, $month, $year);

    //             $Direct = [];

    //             if ($data['sales_type'] == "Food") {
    //                 $Direct['C1'] = $direct['food']["C1.  PREPARATION - FOOD"];
    //                 $Direct['D1'] = $direct['food']["D1.  COOKING - FOOD"];
    //             } else {
    //                 $Direct['C2'] = $direct['beverage']["C2.  PREPARATION - BEVERAGES"];
    //             }

    //             foreach ($result['direct_labour'] as $KEY => $VALUE) {
    //                 foreach ($VALUE as $Key => $Value) {
    //                     foreach ($Value as $key => $value) {
    //                         if (strpos($value['name'], "C1") !== false) {
    //                             $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['C1'];
    //                         } elseif (strpos($value['name'], "D1") !== false) {
    //                             $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['D1'];
    //                         } elseif (strpos($value['name'], "D2") !== false) {
    //                             $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['D2'];
    //                         }
    //                     }
    //                 }
    //             }

    //             $indirect = $this->indirect_labour($id, $year, $month, $data);

    //             $result['indirect_labour'] = $indirect;

    //             $data['preparation'] = [0 => 1];
    //             $data['batch'] = [0 => 1];

    //             $overheads = $this->costing($id, $year, $month, $data);

    //             $result['overheads'] = $overheads;


    //         return $result;
    //     } else {
    //         return false;
    //     }
    // }

    public function copy_costing($name, $id, $sales_channel)
    {

        $this->db->where_in('name', $name);
        $this->db->where_in('sales_channel', $sales_channel);
        $this->db->where_in('r_id', $id);
        $this->db->order_by('id', 'desc');
        $this->db->limit('1');
        $Result1 = $this->db->get('tblcosting')->row_array();

        $this->db->where_in('name', $name);
        $this->db->where_in('sales_channel', $sales_channel);
        $this->db->where_in('r_id', $id);
        $this->db->order_by('id', 'desc');
        $this->db->limit('1');
        $Result2 = $this->db->get('tblcosting_performance')->row_array();

        if ($Result1 && $Result2) {

            $result['id'] = $Result1['r_id'];
            $result['month'] = $Result1['month'];
            $result['year'] = $Result1['year'];
            $result['name'] = $Result1['name'];
            $result['type'] = $Result1['type'];
            $result['general_batch'] = $Result1['general_batch'];
            $result['general_servings'] = $Result1['general_servings'];
            $result['product_sales'] = $Result1['product_sales'];
            $result['sales_channel'] = $data['sales_channel'] = $Result1['sales_channel'];
            $data['sales_type'] = $Result1["type"];
            $result['sales_price'] = $Result1['sales_price'];
            $result['ingredients'] = unserialize($Result1['ingredients']);
            $result['direct_labour'] = unserialize($Result1['direct_labour']);
            $result['indirect_labour'] = unserialize($Result1['indirect_labour']);
            $result['overheads'] = unserialize($Result1['overheads']);
            $result['extra'] = unserialize($Result1['extra']);
            $result['performance'] = unserialize($Result2['data']);

            // $currentDate = new DateTime();
            // $month = $currentDate->format('F');
            // $year = $currentDate->format('Y');

            $currentDate = new DateTime();
            // $month = $currentDate->format('F');
            $year = $currentDate->format('Y');
            $currentDate->modify('-1 month');
            $month = $currentDate->format('F');
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            $this->db->where('r_id', $id);
            $this->db->like('salesdata', $result['name']);
            $this->db->select('salesdata');
            $sales_data = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();
            // print_r($sales_data);die();

            if ($sales_data) {
                foreach ($sales_data as $value) {
                    $value = unserialize($value['salesdata']);
                    if ($name == $value["Item"]) {
                        $result['product_sales'] = $value["TableUnits"];
                        break;
                    }
                }
            } else {
                $result['product_sales'] = false;
            }

                $data['ing_count'] = 0;

                foreach ($result['ingredients'] as $KEY => $VALUE) {
                    foreach ($VALUE as $Key => $Value) {
                        foreach ($Value as $key => $value) {
                            if ($Key != "sides") {
                                $data['ing_count'] += 1;
                            }
                            $category = $value["category"];
                            $item = $value["itemName"];
                            $batchCost = $value["batchCost"];
                            $quantity = $value["quantity"];
                            $unitOfMeasure = $value["unitOfMeasure"];
                            $this->db->select('tblingredient_categories.category_id');
                            $this->db->from('tblingredient_categories');
                            $this->db->where('category', $category);
                            $category_id = $this->db->get()->result();
                            $category = $category_id[0]->category_id;

                            $this->db->from(db_prefix() . 'user_ingredients');
                            $this->db->where('restaurant', $id);
                            $this->db->where('category', $category);
                            $stockDatas = $this->db->get()->result_array();

                            $stockDatas = unserialize($stockDatas[0]["ingredients"]);

                            foreach ($stockDatas as $stock) {
                                $item=strtolower($item);
                                if ($item == strtolower($stock["ingredient"])) {
                                    $ingredient_id = $stock["ingredientId"];
                                }
                            }
                            // print_r($value);die;

                            $Today = new DateTime('today');
                            $Today = $Today->format('Y-m-d');

                            $query = $this->db->select('*')
                                ->from('tblingredient_stock_cards')
                                ->where('restaurant', $id)
                                ->where('category', $category)
                                ->where('item', $ingredient_id)
                                // ->where('datecreated <=', $Today)
                                ->order_by('id', 'DESC')
                                ->limit(4)
                                ->get()
                                ->result_array();

                            foreach ($query as $singleData) {
                                if (isset($singleData['pricePerUnit']) && $singleData['pricePerUnit'] !== "" && $singleData['pricePerUnit'] !== 0) {
                                    $UNIT[] = $singleData['pricePerUnit'];
                                }
                            }

                            $count = count($UNIT);
                            $sum = array_sum($UNIT);
                            unset($UNIT);

                            // settype($sum, 'Integer');
                            if ($sum != 0 && $count != 0) {
                                $unit_price = $sum / $count;
                                $unit_price = number_format((float)$unit_price, 2, '.', '');
                                // $unit_price = round($unit_price, 2);
                            } else {
                                $unit_price = 0; // Set a default value or handle it according to your logic
                                // break 1;
                            }
                            $result['ingredients'][$KEY][$Key][$key]["unitPrice"] = $unit_price;
                            if($batchCost != $unit_price){

                                if ($unitOfMeasure == 1) {
                                    $quantity = round($quantity * 1, 2);
                                } elseif ($unitOfMeasure == 2) {
                                    $quantity = round($quantity * 1, 2);
                                } elseif ($unitOfMeasure == 3) {
                                    $quantity = round($quantity * 0.001, 2);
                                } elseif ($unitOfMeasure == 7) {
                                    $quantity = round($quantity * 1, 2);
                                } elseif ($unitOfMeasure == 8) {
                                    $quantity = round($quantity * 0.001, 2);
                                } elseif ($unitOfMeasure == 4) {
                                    $quantity = round($quantity * 0.005, 2);
                                } elseif ($unitOfMeasure == 5) {
                                    $quantity = round($quantity * 0.015, 2);
                                } elseif ($unitOfMeasure == 6) {
                                    $quantity = round($quantity * 0.002, 2);
                                }

                                $result['ingredients'][$KEY][$Key][$key]["batchCost"] = round($quantity * $unit_price,2);
                            }
                        }
                    }
                }

                $direct = $this->direct_labour($id, $month, $year);

                $Direct = [];

                if ($data['sales_type'] == "Food") {
                    $Direct['C1'] = $direct['food']["C1.  PREPARATION - FOOD"];
                    $Direct['D1'] = $direct['food']["D1.  COOKING - FOOD"];
                } else {
                    $Direct['C2'] = $direct['beverage']["C2.  PREPARATION - BEVERAGES"];
                }

                foreach ($result['direct_labour'] as $KEY => $VALUE) {
                    foreach ($VALUE as $Key => $Value) {
                        foreach ($Value as $key => $value) {
                            if (strpos($value['name'], "C1") !== false) {
                                $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['C1'];
                            } elseif (strpos($value['name'], "D1") !== false) {
                                $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['D1'];
                            } elseif (strpos($value['name'], "D2") !== false) {
                                $result['direct_labour'][$KEY][$Key][$key]["rmPerDLH"] = $Direct['D2'];
                            }
                        }
                    }
                }

                $indirect = $this->indirect_labour($id, $year, $month, $data);

                $result['indirect_labour'] = $indirect;

                $data['preparation'] = [0 => 1];
                $data['batch'] = [0 => 1];

                $overheads = $this->costing($id, $year, $month, $data);

                $result['overheads'] = $overheads;


            return $result;
        } else {
            return false;
        }
    }

    public function sides_delete($id, $data)
    {

        if (!is_array($data)) {
            $data = [$data];
        }

        $this->db->where_in('id', $data);
        $this->db->where('r_id', $id);
        $this->db->delete(db_prefix() . 'costing_sidedish');

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function log_query($query, $name, $r_id, $id)
    {
        $log_file = 'log.txt';
        $log_message = date('Y-m-d H:i:s') . " - Performance API - " . "Name of the product = " . $name . " - Resturant ID = " . $r_id . " - Costing ID = " . $id ." - Query = ". $query . PHP_EOL;

        file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
    }
}