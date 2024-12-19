<?php
class Dashboard_model extends CI_Model
{

    public function verify_status_update($id)
    {
        $data = [
            'email_verify_status' => 1
        ];
        $this->db->where('client_user', $id);
        $result = $this->db->update('tblclient_details', $data);
        if ($result) {
            $this->db->where('client_user', $id);
            $client_data = $this->db->get('tblclient_details')->result_array();
            return $client_data;
        }
        return false;
    }

    public function findings($name, $id, $sales_channel, $year ,$month_req=""){

        if ($name == "" || empty($name) || $sales_channel == "" || empty($sales_channel)) {
            $this->db->where_in('r_id', $id);
            $this->db->order_by('id', 'DESC');
            $this->db->select(['name', 'sales_channel']);
            $this->db->limit(1);
            $Result = $this->db->get('tblcosting')->row_array();
            $name = $Result['name'];
            $sales_channel = $Result['sales_channel'];
        }

        $this->db->where_in('r_id', $id);
        $this->db->where_in('name', $name);
        $this->db->where_in('sales_channel', $sales_channel);
        if (!empty($year)) {
            $this->db->where_in('year', $year);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->limit(3);
        $this->db->select('*');
        $costing = $this->db->get('tblcosting')->result_array();

        if ($costing) {

            $month = $costing[0]['month'];
            $year = $costing[0]['year'];

            $extra = unserialize($costing[0]['extra']);
            $sales_channel = $costing[0]["sales_channel"];
            $result = [];
            $highest_ingredient_name = "";
            $consumption = 0;
            $highest_quantity = 0;
            $median = [];
            $sales_volume = 0;
            $highest_price = 0;
            $units_of_measure = 0;
            $ingredient_id = 0;
            $ingrdient_price = [];
            $ingredient_unitcost = [];
            $j = 0;

            foreach ($costing as $costing_key => $costing_details) {

                if ($sales_channel != $costing_details["sales_channel"]) {
                    continue;
                }

                $unserialized_ingredients = unserialize($costing_details['ingredients']);
                $servings = (!empty($costing_details['general_servings'])) ? $costing_details['general_servings'] : 1;

                $i = 0;
                $consumption = 0;
                foreach ($unserialized_ingredients[1]['mains'] as $unserialized_ingredients_key => $unserializedIngredients) {
                    if (count($unserialized_ingredients[1]['mains']) != 0) {
                        $ingredient_name[$i] = $unserializedIngredients["itemName"];
                        $ingrdient_price[$i] = $unserializedIngredients["unitPrice"];
                        $ingredient_unitcost[$i] = $unserializedIngredients["unitCost"];

                        if ($unserializedIngredients["unitOfMeasure"] == 1) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 2) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 3) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.001, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 7) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 8) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.001, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 4) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.005, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 5) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.015, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 6) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.002, 2);
                        }
                        $i++;
                    }
                }

                foreach ($unserialized_ingredients[0]['premix'] as $unserialized_ingredients_key => $unserializedIngredients) {
                    if (count($unserialized_ingredients[0]['premix']) != 0) {
                        $ingredient_name[$i] = $unserializedIngredients["itemName"];
                        $ingrdient_price[$i] = $unserializedIngredients["unitPrice"];
                        $ingredient_unitcost[$i] = $unserializedIngredients["unitCost"];

                        if ($unserializedIngredients["unitOfMeasure"] == 1) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 2) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 3) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.001, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 7) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 1, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 8) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.001, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 4) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.005, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 5) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.015, 2);
                        } elseif ($unserializedIngredients["unitOfMeasure"] == 6) {
                            $ingredient_quantity[$i] = round($unserializedIngredients["quantity"] * 0.002, 2);
                        }
                        $i++;
                    }
                }
                // print_r($ingrdient_price);die();

                $highest_unitcost = max($ingredient_unitcost);
                $i = array_keys($ingredient_unitcost, $highest_unitcost);
                $i = $i[0];
                // print_r($ingrdient_price);die();

                $highest_ingredient_name = $ingredient_name[$i];
                $highest_quantity = $ingredient_quantity[$i];
                $highest_quantity = round($highest_quantity / $servings, 2);
                $highest_price = $ingrdient_price[$i];

                $this->db->select('*');
                $this->db->from(db_prefix() . 'user_ingredients');
                $this->db->where('restaurant', $id);
                $stockDatas = $this->db->get()->result_array();

                if (!empty($stockDatas)) {
                    foreach ($stockDatas as $stock) {
                        $category = $stock['category'];
                        $ingredients = unserialize($stock['ingredients']);

                        foreach ($ingredients as $key => $value) {
                            if ($value['ingredient'] == $highest_ingredient_name) {
                                $ingredient_id = $value['ingredientId'];
                                $units_of_measure = $value['measurement'];
                                $category = $stock['category'];
                                break 2;
                            }
                        }
                    }
                }

                $date = $costing_details['datecreated'];

                $today = new DateTime($date);

                $today =  $today->format('Y-m-d');

                // $this->db->where_in('category', $category);
                // $this->db->where_in('item', $ingredient_id);
                // $this->db->where_in('restaurant', $id);
                // $this->db->where('datecreated <=', $today);
                // $this->db->where('consumption IS NOT NULL'); // Add this line to check that consumption is not null
                // $this->db->order_by('id', 'DESC');
                // $this->db->limit(4);

                $this->db->where_in('category', $category);
                $this->db->where_in('item', $ingredient_id);
                $this->db->where_in('restaurant', $id);
                $this->db->where('datecreated <=', $today);
                $this->db->where('consumption !=',0);
                $this->db->where('pricePerUnit !=', 0);
                $this->db->order_by('id', 'DESC');
                $this->db->limit(4);
                // $this->db->select('consumption');
                $consumption_values = $this->db->get('tblingredient_stock_cards')->result_array();
                
                for ($i = 0; $i < count($consumption_values); $i++) {
                    if ($consumption_values[$i]['consumption'] != null) {
                        $consumption += $consumption_values[$i]['consumption'];
                    }
                }
                if ($consumption == 0) {
                    $consumption = 1;
                }

                if ($extra['allChannelSameCost'] == true) {

                    $this->db->where('r_id', $id);
                    $this->db->where('month', $costing_details['month']);
                    $this->db->where('year', $costing_details['year']);
                    $this->db->like('salesdata', $costing_details['name']);
                    $this->db->select('salesdata');
                    $sales_data = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

                    foreach ($sales_data as $sales_data_key => $salesData) {
                        $unserialized_sales_data = unserialize($salesData['salesdata']);

                        if ($unserialized_sales_data['Item'] == $costing_details['name']) {
                            $sales_volume = $unserialized_sales_data['TableUnits'];

                            // $result['cylinder'][$costing_details['year']][$costing_details['month']]['outer_cylinder'] = $costing_details['sales_price'] * $sales_volume;
                            // $outer_cylinder = $costing_details['sales_price'] * $sales_volume;
                            // $inner_filled = $highest_price * $sales_volume * $highest_quantity;
                            // $result['cylinder'][$costing_details['year']][$costing_details['month']]['inner_filled'] = round($inner_filled, 2);
                            // $result['cylinder'][$costing_details['year']][$costing_details['month']]['name'] = $costing_details['name'];
                            // $result['cylinder'][$costing_details['year']][$costing_details['month']]['ingredient_name'] = $highest_ingredient_name;
                            // $result['cylinder'][$costing_details['year']][$costing_details['month']]['profit_margin_sales'] = round($inner_filled / $outer_cylinder, 2);
                            break 1;
                        }
                    }

                    $outer_cylinder = $costing_details['sales_price'] * $sales_volume;
                    $inner_filled = $highest_price * $sales_volume * $highest_quantity;

                    $result['cylinder'][$j]['year'] = $costing_details['year'];
                    $result['cylinder'][$j]['month'] = $costing_details['month'];
                    $result['cylinder'][$j]['outer_cylinder'] = $outer_cylinder;
                    $result['cylinder'][$j]['inner_filled'] = round($inner_filled, 2);
                    $result['cylinder'][$j]['name'] = $costing_details['name'];
                    $result['cylinder'][$j]['ingredient_name'] = $highest_ingredient_name;
                    $result['cylinder'][$j]['profit_margin_sales'] = round($inner_filled / $outer_cylinder, 2);
                    $result['cylinder'][$j]['percent_ingredient_consumption'] = round((($highest_quantity * $sales_volume) / $consumption) * 100, 2);
                    $result['cylinder'][$j]['unit_of_measure'] = $units_of_measure;
                    $result['cylinder'][$j]['category'] = "Checked";
                    $result['cylinder'][$j]['unit_price'] = $highest_price;
                    $result['cylinder'][$j]['consumption'] = $consumption;

                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['percent_ingredient_consumption'] = round((($highest_quantity * $sales_volume) / $consumption) * 100, 2);
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['unit_of_measure'] = $units_of_measure;
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['category'] = "Checked";

                    // break 1;
                    $j++;
                } else {

                    $this->db->where('r_id', $id);
                    $this->db->where('month', $month);
                    $this->db->where('year', $year);
                    $this->db->select('salesdata');
                    $this->db->like('salesdata', $name);
                    $salesData = $this->db->get('tblrestaurant_sales_menu_data')->result_array();

                    $salesVolume = 1;
                    foreach ($salesData as $key => $value) {
                        $value = unserialize($value['salesdata']);
                        if (strcasecmp($name, $value["Item"]) == 0) {
                            // if ($name == $value["Item"]) {
                            $salesVolume = $value["TableUnits"];
                            break 1;
                        }
                    }

                    $inner_filled = $highest_quantity * $salesVolume;

                    $result['cylinder'][$j]['year'] = $costing_details['year'];
                    $result['cylinder'][$j]['month'] = $costing_details['month'];
                    $result['cylinder'][$j]['outer_cylinder'] = $consumption;
                    $result['cylinder'][$j]['inner_filled'] = round($inner_filled, 2);
                    $result['cylinder'][$j]['name'] = $costing_details['name'];
                    $result['cylinder'][$j]['ingredient_name'] = $highest_ingredient_name;
                    $result['cylinder'][$j]['percent_ingredient_consumption'] = round((($highest_quantity * $salesVolume) / $consumption) * 100, 2);
                    $result['cylinder'][$j]['unit_of_measure'] = $units_of_measure;
                    $result['cylinder'][$j]['category'] = "UnChecked";
                    $result['cylinder'][$j]['unit_price'] = $highest_price;
                    // $result['cylinder'][$j]['outer_cylinder'] = $consumption;

                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['outer_cylinder'] = $consumption;
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['name'] = $costing_details['name'];
                    // $inner_filled = $highest_quantity * $salesVolume;
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['inner_filled'] = round($inner_filled, 2);
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['ingredient_name'] = $highest_ingredient_name;
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['percent_ingredient_consumption'] = round((($highest_quantity * $salesVolume) / $consumption) * 100, 2);
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['unit_of_measure'] = $units_of_measure;
                    // $result['cylinder'][$costing_details['year']][$costing_details['month']]['category'] = "UnChecked";

                    // break 1;
                    $j++;
                }
            }

            $this->db->where_in('r_id', $id);
            if ($month_req != "") {
                $this->db->where_in('month', $month_req);
            } else {
                $this->db->where_in('month', $month);
            }
            $this->db->where_in('year', $year);
            $this->db->select('*');
            $costing_graph = $this->db->get('tblcosting_performance')->result_array();

            $cost_margin = [];
            $result['graph'] = [];
            $i = 0;

            foreach ($costing_graph as $costing_graph_value) {

                $unserialized_data = unserialize($costing_graph_value['data']);

                $nameExists = false;

                foreach ($result['graph'] as $graph_entry) {
                    if ($graph_entry['name'] == $costing_graph_value['name'] &&
                        $graph_entry['year'] == $costing_graph_value['year'] &&
                        $graph_entry['month'] == $costing_graph_value['month']) {
                        $nameExists = true;
                        break;
                    }
                }

                if (!$nameExists) {

                    $names[] = $costing_graph_value['name'];

                    $median['profitMargin'][] += $unserialized_data['data']['cost_margin'];

                    $cost_margin[$costing_graph_value['name']] += $unserialized_data['data']['cost_margin'];

                    $this->db->where('r_id', $id);
                    $this->db->where('month', $costing_graph_value['month']);
                    $this->db->where('year', $costing_graph_value['year']);
                    $this->db->like('salesdata', $costing_graph_value['name']);
                    $this->db->select('salesdata');
                    $sales_data = $this->db->get(db_prefix() . 'restaurant_sales_menu_data')->result_array();

                    foreach ($sales_data as $sales_data_key => $salesData) {
                        $unserialized_sales_data = unserialize($salesData['salesdata']);

                        if ($unserialized_sales_data['Item'] == $costing_graph_value['name']) {
                            $sales_volume = $unserialized_sales_data['TableUnits'];

                            $median['salesVolume'][] = $sales_volume;

                            $result['graph'][$i]['year'] = $costing_graph_value['year'];
                            $result['graph'][$i]['month'] = $costing_graph_value['month'];
                            $result['graph'][$i]['name'] = $costing_graph_value['name'];
                            $result['graph'][$i]['salesVolume'] = $sales_volume;
                            // $result['graph'][$costing_graph_value['year']][$costing_graph_value['month']][$costing_graph_value['name']]['salesVolume'] = $sales_volume;

                            $i++;
                            break 1;
                        }
                    }
                }
            }

            foreach ($median as $key => $Value) {
                sort($Value);

                $count = count($Value);
                $middle = floor($count / 2);

                if ($count % 2 == 0) {
                    $result['midpoint'][$key] = ($Value[$middle - 1] + $Value[$middle]) / 2;
                    $result['midpoint'][$key] = round($result['midpoint'][$key], 2);
                } else {
                    $result['midpoint'][$key] = $Value[$middle];
                }
            }

            $this->db->where_in('r_id', $id);
            $this->db->order_by('id', 'DESC');
            $this->db->select('name');
            $this->db->select('month');
            $this->db->select('id');
            $Result = $this->db->get('tblcosting')->result_array();

            foreach ($Result as $key => $value) {
                if ($month == $value['month']) {
                    $result['search'][$value['id']] = $value['name'];
                }
            }

            $value_counts = array_count_values($names);

            foreach ($value_counts as $key => $value) {
                foreach ($result['graph'] as $KEY => $VALUE) {
                    if ($VALUE["name"] == $key) {
                        if ($value > 1) {
                            $Value = $cost_margin[$key] / $value;
                            $result['graph'][$KEY]['profitMargin'] = round($Value, 2);
                        } elseif ($value == 1) {
                            $profitMargin = $cost_margin[$key];
                            $result['graph'][$KEY]['profitMargin'] = $profitMargin;
                        }
                    }
                }
            }

            $this->db->where_in('r_id', $id);
            $this->db->where_in('year', $year);
            $this->db->select(['name', 'sales_channel']);
            $salesChannels = $this->db->get('tblcosting')->result_array();

            foreach ($salesChannels as $key => $value) {
                $result['salesChannels'][$value["sales_channel"]][] = $value['name'];
            }

            $result['selected_salesChannel'] = $value['sales_channel'];
            $result['selected_year'] = $costing_details['year'];

            $this->db->where_in('r_id', $id);
            $this->db->select('year');
            $this->db->group_by('year');
            $Result = $this->db->get('tblcosting')->result_array();

            $result['years'] = $Result;

            $i = 0;

            if (count($result['cylinder']) == 2) {
                if (($result['cylinder'][$i]['percent_ingredient_consumption'] == $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 2) {
                    $result['cylinder'][$i + 1]['updown'] = false;
                } elseif (($result['cylinder'][$i]['percent_ingredient_consumption'] > $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 2) {
                    $result['cylinder'][$i + 1]['updown'] = 'loss';
                } elseif (($result['cylinder'][$i]['percent_ingredient_consumption'] < $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 2) {
                    $result['cylinder'][$i + 1]['updown'] = 'profit';
                }
            } elseif (count($result['cylinder']) == 3) {
                if (($result['cylinder'][$i]['percent_ingredient_consumption'] == $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 1]['updown'] = false;
                } elseif (($result['cylinder'][$i]['percent_ingredient_consumption'] > $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 1]['updown'] = 'loss';
                } elseif (($result['cylinder'][$i]['percent_ingredient_consumption'] < $result['cylinder'][$i + 1]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 1]['updown'] = 'profit';
                }

                if (($result['cylinder'][$i + 1]['percent_ingredient_consumption'] == $result['cylinder'][$i + 2]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 2]['updown'] = false;
                } elseif (($result['cylinder'][$i + 1]['percent_ingredient_consumption'] > $result['cylinder'][$i + 2]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 2]['updown'] = 'loss';
                } elseif (($result['cylinder'][$i + 1]['percent_ingredient_consumption'] < $result['cylinder'][$i + 2]['percent_ingredient_consumption']) && count($result['cylinder']) == 3) {
                    $result['cylinder'][$i + 2]['updown'] = 'profit';
                }
            }

            $this->db->select(['year','month']);
            $costing_graph_year_month = $this->db->get('tblcosting_performance')->result_array();

            $unique_years = [];
            $unique_months = [];

            foreach ($costing_graph_year_month as $year_month_key => $year_month_value) {
                if (!in_array($year_month_value['year'], $unique_years)) {
                    $unique_years[] = $year_month_value['year'];
                }
            
                if (!in_array($year_month_value['month'], $unique_months)) {
                    $unique_months[] = $year_month_value['month'];
                }
            }

            $result['dropdown']['year']=$unique_years;
            $result['dropdown']['month']=$unique_months;

            return $result;
        } else {
            return false;
        }
    }
}
