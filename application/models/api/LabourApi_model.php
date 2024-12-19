<?php
class LabourApi_model extends CI_Model
{

    public function save_traceable($id, $data)
    {
        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'labour_traceable')->result_array();
        $traceable = serialize($data['traceable']);
        $a = $data['data'];

        if (!$result) {
            foreach ($a as $key => $value) {
                if ($key == 1) {
                    $foreigner = "Yes";
                    $fulltime = "Yes";
                    $Value['traceable'] = $value;
                } elseif ($key == 2) {
                    $foreigner = "Yes";
                    $fulltime = "No";
                    $Value['traceable'] = $value;
                } elseif ($key == 3) {
                    $foreigner = "No";
                    $fulltime = "Yes";
                    $Value['traceable'] = $value;
                } elseif ($key == 4) {
                    $foreigner = "No";
                    $fulltime = "No";
                    $Value['traceable'] = $value;
                }
                $datecreated = date('Y-m-d');

                $data['datecreated'] = date('Y-m-d H:i:s');

                $labour_details = [
                    'r_id' => $id,
                    'foreigner' => $foreigner,
                    'fulltime' => $fulltime,
                    'traceable' => $traceable,
                    'data' => serialize($Value),
                    'datecreated' => $datecreated
                ];

                $this->db->insert('tbllabour_traceable', $labour_details);
            }

            if ($this->db->affected_rows() > 0) {

                $insert_id = $this->db->insert_id();
                return $insert_id;
            } else {
                return false;
            }
        } else {
            foreach ($a as $key => $value) {
                if ($key == 1) {
                    $foreigner = "Yes";
                    $fulltime = "Yes";
                    $Value['traceable'] = $value;
                    $Value['values'] = $data['data'][1];
                } elseif ($key == 2) {
                    $foreigner = "Yes";
                    $fulltime = "No";
                    $Value['traceable'] = $value;
                    $Value['values'] = $data['data'][2];
                } elseif ($key == 3) {
                    $foreigner = "No";
                    $fulltime = "Yes";
                    $Value['traceable'] = $value;
                    $Value['values'] = $data['data'][3];
                } elseif ($key == 4) {
                    $foreigner = "No";
                    $fulltime = "No";
                    $Value['traceable'] = $value;
                    $Value['values'] = $data['data'][4];
                }

                $labour_details = [
                    'traceable' => $traceable,
                    'data' => serialize($Value),
                ];

                $this->db->where_in('r_id', $id);
                $this->db->where_in('foreigner', $foreigner);
                $this->db->where_in('fulltime', $fulltime);
                $report = $this->db->update(db_prefix() . 'labour_traceable', $labour_details);

                $this->db->where_in('r_id', $id);
                $total = $this->db->get(db_prefix() . 'labour_leave_policy')->row_array();
                $total = $total['total_labour_count'];

                $this->db->where('r_id', $id);
                $Total = $this->db->get('tbllabour_data')->num_rows();

                if ($Total >= $total) {
                    $result = $this->generate_traceable($id);

                    if (!$result) {
                        $this->generate_traceable($id);
                    }
                }
            }

            if ($report) {
                return $report;
            } else {
                return false;
            }
        }
    }

    public function save_leave_policy($id, $data)
    {
        // Extract data from input array
        $leave = $data['leave'];
        $carry_forward = $data['carry_forward'];
        $total_labour_count = $data['total_labour_count'];
        $datecreated = date('Y-m-d');

        // Store data for insertion or update
        $storeData = [
            'r_id' => $id,
            'foreigners_ftime' => $leave['1'],
            'foreigners_ptime' => $leave['2'],
            'local_ftime' => $leave['3'],
            'local_ptime' => $leave['4'],
            'carry_forward' => $carry_forward,
            'total_labour_count' => $total_labour_count,
            'datecreated' => $datecreated
        ];

        // Check if record already exists
        $result = $this->db->get_where(db_prefix() . 'labour_leave_policy', ['r_id' => $id])->row();

        if ($result) {
            // Update existing record
            $this->db->where('r_id', $id);
            $report = $this->db->update(db_prefix() . 'labour_leave_policy', $storeData);
        } else {
            // Insert new record
            $report = $this->db->insert(db_prefix() . 'labour_leave_policy', $storeData);
        }

        // Return the report
        return $report ? true : false;
    }

    public function activity($id, $fulltime, $foreigner)
    {
        $this->db->where('r_id', $id);
        $result = $this->db->get('tblprocessbuilder_data')->result_array();

        if ($result) {
            $Data = unserialize($result[0]['data']);

            $data['activity'] = $Data['data']['data']['activity'];

            foreach ($data['activity'] as $key => $value) {
                if ($value == "K. EMPLOYEE WELFARE") {
                    unset($data['activity'][$key]);
                }
            }

            $data['description'] = array(
                'A' => 'Purchasing materials that goes into food and beverage products.',
                'B' => 'Safekeeping and monitoring of materials in inventory.',
                'C1' => 'Preparing bases, marination, curries etc. for food orders.',
                'C2' => 'Preparing premixes, fruits etc. for beverage orders.',
                'D1' => 'Cooking food upon customer order.',
                'D2' => 'Mixing beverages upon customer order.',
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

            if ($fulltime == 'Yes' && $foreigner == 'Yes') {
                $scheduleType = 'foreigners_ftime';
            } elseif ($fulltime == 'No' && $foreigner == 'Yes') {
                $scheduleType = 'foreigners_ptime';
            } elseif ($fulltime == 'Yes' && $foreigner == 'No') {
                $scheduleType = 'local_ftime';
            } elseif ($fulltime == 'No' && $foreigner == 'No') {
                $scheduleType = 'local_ptime';
            }

            $this->db->where_in('r_id', $id);
            $result = $this->db->get(db_prefix() . 'labour_leave_policy')->row_array();

            $result = $result[$scheduleType];

            $data['annual'] = ($result == 0) ? true : false;

            return $data;
        }
        return false;
    }

    public function insert_labour($id, $data)
    {

        $data['datecreated'] = date('Y-m-d');
        $data['activity'] = serialize($data['activity']);
        $data['name'] = ucwords(strtolower($data['name']));

        $this->db->where_in('r_id', $id);
        $this->db->where_in('name', $data['name']);
        $result = $this->db->get('tbllabour_data')->result_array();
        if (!$result) {

            $labour_details = [
                'r_id' => $id,
                'name' => $data['name'],
                'fulltime' => $data['fulltime'],
                'foreigner' => $data['foreigner'],
                'salary' => $data['salary'],
                'productivity' => $data['productivity'],
                'traceable' => $data['traceable'],
                'traceables' => null,
                'activity' => $data['activity'],
                'datecreated' => $data['datecreated']
            ];

            $this->db->insert('tbllabour_data', $labour_details);

            if ($this->db->affected_rows() > 0) {

                $insert_id = $this->db->insert_id();

                $restDay_details = [
                    'r_id' => $id,
                    'e_id' => $insert_id,
                    'month' => $data['restDays']['month'],
                    'year' => $data['restDays']['year'],
                    'days' => serialize($data['restDays']['days']),
                    'data' => serialize($data['restDays']),
                    'datecreated' => $data['datecreated']
                ];

                $this->db->insert('tbllabour_restdays', $restDay_details);

                $this->db->where_in('r_id', $id);
                $total = $this->db->get(db_prefix() . 'labour_leave_policy')->row_array();
                $total = $total['total_labour_count'];

                $this->db->where('r_id', $id);
                $Total = $this->db->get('tbllabour_data')->num_rows();

                if ($Total >= $total) {
                    $this->generate_traceable($id);

                    $update = [
                        'total_labour_count' => $Total,
                    ];

                    $this->db->where('r_id', $id);
                    $total = $this->db->update(db_prefix() . 'labour_leave_policy', $update);
                    $count_details['notification'] = false;
                    $count_details['count'] = false;
                    $count_details['insert_id'] = $insert_id;
                }else {
                    $count_details['notification'] = true;
                    $count_details['count'] = $total - $Total;
                    $count_details['insert_id'] = $insert_id;
                }

                return $count_details;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function update_labour($id, $data)
    {

        $e_id = $data['employeeId'];
        $data['name'] = ucwords(strtolower($data['name']));
        $data['activity'] = serialize($data['activity']);
        $data['datecreated'] = date('Y-m-d');

        $labour_details = [
            'name' => $data['name'],
            'fulltime' => $data['fulltime'],
            'foreigner' => $data['foreigner'],
            'salary' => $data['salary'],
            'productivity' => $data['productivity'],
            'activity' => $data['activity'],
            'datecreated' => $data['datecreated']
        ];

        $this->db->where_in('r_id', $id);
        $this->db->where_in('e_id', $e_id);
        $labour_update = $this->db->update('tbllabour_data', $labour_details);

        if ($labour_update) {
            $restDay_details = [
                'r_id' => $id,
                'e_id' => $e_id,
                'month' => $data['restDays']['month'],
                'year' => $data['restDays']['year'],
                'days' => serialize($data['restDays']['days']),
                'data' => serialize($data['restDays']),
                'datecreated' => $data['datecreated']
            ];

            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->where_in('month', $data['restDays']['month']);
            $this->db->where_in('year', $data['restDays']['year']);
            $result = $this->db->get('tbllabour_restdays')->row_array();

            if ($result) {
                // Update existing record
                $this->db->where_in('r_id', $id);
                $this->db->where_in('e_id', $e_id);
                $this->db->where_in('month', $data['restDays']['month']);
                $this->db->where_in('year', $data['restDays']['year']);
                $this->db->update(db_prefix() . 'labour_restdays', $restDay_details);
            } else {
                // Insert new record
                $this->db->insert(db_prefix() . 'labour_restdays', $restDay_details);
            }

            if ($data['change'] == true) {
                $this->generate_traceable($id);
            }

            return $result;
        } else {
            return false;
        }
    }

    public function fetch_labour($id, $employeeId)
    {
        if ($id && $employeeId) {
            $this->db->where('r_id', $id);
            $this->db->where('e_id', $employeeId);
            $result = $this->db->get('tbllabour_data')->result_array();
            $activities = unserialize($result[0]['activity']);
            $result[0]['activity'] = [];

            $Activity = [];
            foreach ($activities as $key => $value) {
                $parts = explode('.', $value["activity"]);
                $Activity[] = $parts[0];
            }

            foreach ($Activity as $key => $value) {
                $this->db->select('activity');
                $this->db->from('tbllabour_activity_list'); // This is the master table
                $this->db->like('activity', $value); // Filter by assigned activities
                $query = $this->db->get()->result_array();

                if (!$query) {
                    unset($activities[$key]);
                }
            }
            $result[0]['activity']=$activities;

            $result[0]['traceables'] = unserialize($result[0]['traceables']);

            // Get the current month in name
            $month = date('F');

            // Get the current year in number
            $year = date('Y');

            $this->db->where('r_id', $id);
            $this->db->where('e_id', $employeeId);
            $this->db->where('month', $month);
            $this->db->where('year', $year);
            $this->db->order_by('id', 'DESC');
            $Result = $this->db->get('tbllabour_restdays')->row_array();
            $result[0]['data'] = unserialize($Result['data']);
            // $result[0]['data']['days'] = unserialize($result[0]['data']['days']);
            // unset($Result[0]['data']);

            // $result[0]['annual'] = $Result[0]['annual'];

            return $result;
        } else {
            return false;
        }
    }

    public function fetch_labour_list($id)
    {
        $this->db->select('name');
        $this->db->select('traceable');
        $this->db->select('e_id');
        $this->db->where('r_id', $id);
        return $this->db->get('tbllabour_data')->result_array();
    }

    public function fetch_traceable($id)
    {
        $this->db->where('r_id', $id);
        $Result = $this->db->get('tbllabour_traceable')->result_array();

        for ($i = 0; $i < count($Result); $i++) {
            $result[$i]['traceable'] = unserialize($Result[$i]['traceable']);
            $result[$i]['data'] = unserialize($Result[$i]['data']);
        }

        return $result;
    }

    public function fetch_leave($id)
    {
        $this->db->where_in('r_id', $id);
        $result = $this->db->get(db_prefix() . 'labour_leave_policy')->result_array();
        return $result;
    }

    public function delete_labour($id, $e_id)
    {

        if ($id && $e_id) {

            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->delete(db_prefix() . 'labour_data');

            $this->db->where_in('r_id', $id);
            $result = $this->db->get(db_prefix() . 'labour_data')->num_rows();

            if ($result == 0) {

                $this->db->where_in('r_id', $id);
                $this->db->delete(db_prefix() . 'labour_traceable');

                $this->db->where_in('r_id', $id);
                $this->db->delete(db_prefix() . 'labour_leave_policy');
            } elseif ($result > 0) {

                $this->db->set('total_labour_count', 'total_labour_count - 1', false);
                $this->db->where_in('r_id', $id);
                $this->db->update('tbllabour_leave_policy');

                $this->generate_traceable($id);
            }



            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->delete(db_prefix() . 'labour_restdays');

            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->delete(db_prefix() . 'labour_overtime');

            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->delete(db_prefix() . 'labour_user_leave');

            return true;
        } else {
            return false;
        }
    }

    public function generate_traceable($id)
    {

        $this->db->select('foreigner');
        $this->db->select('fulltime');
        $this->db->select('salary');
        $this->db->select('e_id');
        $this->db->where('r_id', $id);
        $Employee = $this->db->get('tbllabour_data')->result_array();

        $total = [];
        // $sum3 = 0;
        foreach ($Employee as $key => $value) {
            if ($value["foreigner"] == "Yes" && $value["fulltime"] == "Yes") {
                $e_id[1][] = $value["e_id"];
                $salary[1][$value["e_id"]] = $value["salary"];
                $total[1] += $value["salary"];
            } elseif ($value["foreigner"] == "Yes" && $value["fulltime"] == "No") {
                $e_id[2][] = $value["e_id"];
                $salary[2][$value["e_id"]] = $value["salary"];
                $total[2] += $value["salary"];
            } elseif ($value["foreigner"] == "No" && $value["fulltime"] == "Yes") {
                $e_id[3][] = $value["e_id"];
                $salary[3][$value["e_id"]] = $value["salary"];
                $total[3] += $value["salary"];
            } elseif ($value["foreigner"] == "No" && $value["fulltime"] == "No") {
                $e_id[4][] = $value["e_id"];
                $salary[4][$value["e_id"]] = $value["salary"];
                $total[4] += $value["salary"];
            }
        }

        $this->db->where('r_id', $id);
        $Traceable = $this->db->get('tbllabour_traceable')->result_array();

        foreach ($Traceable as $key => $value) {
            if ($value["foreigner"] == "Yes" && $value["fulltime"] == "Yes") {
                $Categories[1] = unserialize($value["data"]);
                $categories[1] = $Categories[1]['traceable'];
            } elseif ($value["foreigner"] == "Yes" && $value["fulltime"] == "No") {
                $Categories[2] = unserialize($value["data"]);
                $categories[2] = $Categories[2]['traceable'];
            } elseif ($value["foreigner"] == "No" && $value["fulltime"] == "Yes") {
                $Categories[3] = unserialize($value["data"]);
                $categories[3] = $Categories[3]['traceable'];
            } elseif ($value["foreigner"] == "No" && $value["fulltime"] == "No") {
                $Categories[4] = unserialize($value["data"]);
                $categories[4] = $Categories[4]['traceable'];
            }
        }

        $traceable = unserialize($value['traceable']);
        $total_salary = [];

        foreach ($categories as $Key => $Value) {
            foreach ($Value as $key => $value) {
                if ($value == "Yes") {
                    $total_salary[$key] += $total[$Key];
                }
            }
        }

        $FINALstep = [];
        $NO = 0;
        foreach ($categories as $key => $value) {
            foreach ($value as $Key => $Value) {
                if ($Value == "Yes") {
                    $Salary = $salary[$key];
                    foreach ($Salary as $KEY => $VALUE) {
                        $STEPone = $VALUE / $total_salary[$Key];
                        $STEPtwo = round($STEPone * $traceable[$Key], 0);
                        $FINALstep[$KEY] += $STEPtwo;
                        $unserializedData[$KEY][$Key] = $STEPtwo;
                    }
                } else {
                    if (!in_array("Yes", $value)) {
                        foreach ($e_id[$key] as $E_ID) {
                            $labour_details = [
                                'traceable' => Null,
                                'traceables' => Null
                            ];

                            $this->db->where_in('r_id', $id);
                            $this->db->where_in('e_id', $E_ID);
                            $result = $this->db->update('tbllabour_data', $labour_details);
                        }
                    }
                }
            }
        }

        $SALARY = 0;
        foreach ($FINALstep as $key => $value) {

            for ($i = 0; $i < 5; $i++) {
                if (!$SALARY) {
                    $SALARY = $salary[$i][$key];
                } else {
                    $i = 6;
                }
            }

            $value = $value + $SALARY;
            // $sum3+=$value;

            unset($SALARY);
            $serializedData = serialize($unserializedData[$key]);

            $labour_details = [
                'traceable' => $value,
                'traceables' => $serializedData,
            ];

            $this->db->where('r_id', $id);
            $this->db->where('e_id', $key);
            $result = $this->db->update('tbllabour_data', $labour_details);
        }

        return $result;
    }

    public function overtime($id, $e_id, $year, $month, $day, $hrs){

        $this->db->where_in('r_id', $id);
        $this->db->where_in('e_id', $e_id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->where_in('day', $day);
        $total = $this->db->get(db_prefix() . 'labour_overtime')->result_array();

        if (!$total) {

            $labour_details = [
                'r_id' => $id,
                'e_id' => $e_id,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'hrs' => $hrs,
            ];

            $this->db->insert(db_prefix() . 'labour_overtime', $labour_details);

            if ($this->db->affected_rows() > 0) {

                $insert_id = $this->db->insert_id();
                return $insert_id;
            }
        } else {
            $update = [
                'hrs' => $hrs,
            ];

            $this->db->where_in('r_id', $id);
            $this->db->where_in('e_id', $e_id);
            $this->db->where_in('year', $year);
            $this->db->where_in('month', $month);
            $this->db->where_in('day', $day);
            $result = $this->db->update(db_prefix() . 'labour_overtime', $update);

            return $result;
        }
    }

    public function leave($id, $e_id, $year, $month, $day, $name)
    {
        $existing_days = [];
        $nonexisting_days = [];

        foreach ($day as $d) {
            $this->db->where('r_id', $id)
                ->where('e_id', $e_id)
                ->where('year', $year)
                ->where('month', $month)
                ->where('day', $d)
                ->where('leave_name', $name);
            $existing_records = $this->db->get('tbllabour_user_leave')->result_array();

            if (!empty($existing_records)) {
                $existing_days[] = $d;
            } else {
                $nonexisting_days[] = $d;
            }
        }

        if (!empty($existing_days) && empty($nonexisting_days)) {
            return true;
        }

        foreach ($nonexisting_days as $d) {
            $labour_details = [
                'r_id' => $id,
                'e_id' => $e_id,
                'year' => $year,
                'month' => $month,
                'leave_name' => $name,
                'day' => $d,
            ];

            if ($name == "Others") {
                $this->db->insert('tbllabour_user_leave', $labour_details);
            } else {
                $this->db->select('e_id, fulltime, foreigner')
                    ->where('r_id', $id)
                    ->where('e_id', $e_id);
                $row = $this->db->get(db_prefix() . 'labour_data')->row_array();

                // $processedData = [];
                $scheduleType = '';
                if ($row['fulltime'] == 'Yes' && $row['foreigner'] == 'Yes') {
                    $scheduleType = 'foreigners_ftime';
                } elseif ($row['fulltime'] == 'No' && $row['foreigner'] == 'Yes') {
                    $scheduleType = 'foreigners_ptime';
                } elseif ($row['fulltime'] == 'Yes' && $row['foreigner'] == 'No') {
                    $scheduleType = 'local_ftime';
                } elseif ($row['fulltime'] == 'No' && $row['foreigner'] == 'No') {
                    $scheduleType = 'local_ptime';
                }
                $total = ['e_id' => $row['e_id'], 'schedule_type' => $scheduleType];

                $this->db->select($total['schedule_type'] . ' AS total')
                    ->where('r_id', $id);
                $leaveTotal = $this->db->get(db_prefix() . 'labour_leave_policy')->result_array();

                $this->db->select('r_id, e_id, COUNT(day) AS count')
                    ->where('r_id', $id)
                    ->where('e_id', $e_id);
                $total_leaves = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

                if (!empty($leaveTotal) && $leaveTotal[0]['total'] != 0) {
                    if (($leaveTotal[0]['total'] - $total_leaves[0]['count'] != 0)) {
                        $this->db->insert(db_prefix() . 'labour_user_leave', $labour_details);
                        if ($this->db->affected_rows() > 0) {
                            $insert_id = $this->db->insert_id();
                        }
                    } else {
                        return 'Cannot apply leave. leave balance is 0.';
                    }
                } else {
                    return 'Cannot apply leave. Insufficient leave balance.';
                }
            }
        }
        return true;
    }

    public function attendance($id, $year, $month, $day)
    {
        // Query to fetch employee names
        $this->db->select('name');
        $this->db->select('e_id');
        $this->db->where('r_id', $id);
        $result1 = $this->db->get('tbllabour_data')->result_array();

        // Query to fetch overtime hours
        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->where_in('day', $day);
        $this->db->select('hrs');
        $this->db->select('e_id');
        $Result2 = $this->db->get(db_prefix() . 'labour_overtime')->result_array();

        $result2 = [];
        foreach ($Result2 as $value) {
            $result2[$value['e_id']] = $value['hrs'];
        }

        // Query to fetch employee data
        $this->db->select('e_id');
        $this->db->select('fulltime');
        $this->db->select('foreigner');
        $this->db->where_in('r_id', $id);
        $re = $this->db->get(db_prefix() . 'labour_data')->result_array();

        $processedData = array();
        foreach ($re as $row) {
            // Determine the schedule type based on the conditions
            if ($row['fulltime'] == 'Yes' && $row['foreigner'] == 'Yes') {
                $scheduleType = 'foreigners_ftime';
            } elseif ($row['fulltime'] == 'No' && $row['foreigner'] == 'Yes') {
                $scheduleType = 'foreigners_ptime';
            } elseif ($row['fulltime'] == 'Yes' && $row['foreigner'] == 'No') {
                $scheduleType = 'local_ftime';
            } elseif ($row['fulltime'] == 'No' && $row['foreigner'] == 'No') {
                $scheduleType = 'local_ptime';
            }

            $processedData[] = array(
                'e_id' => $row['e_id'],
                'schedule_type' => $scheduleType
            );
        }

        // Query to fetch leave totals
        $leavecount = array();
        foreach ($processedData as $total) {
            $this->db->select($total['schedule_type'] . ' AS total');
            $this->db->where('r_id', $id);
            $leaveTotal = $this->db->get(db_prefix() . 'labour_leave_policy')->row_array();
            $leavecount[] = array(
                'e_id' => $total['e_id'],
                'leaveTotal' => $leaveTotal['total'] ?? 0
            );
        }

        // Query to fetch leave counts
        $this->db->select('r_id, e_id, COUNT(day) AS count');
        $this->db->where('r_id', $id);
        $this->db->where('day <=', $day);
        $this->db->where_in('leave_name', 'Annual');
        $this->db->group_by('r_id, e_id');
        $result4 = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

        // Query to fetch today's leave status
        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->where_in('day', $day);
        $this->db->where_in('leave_name', ['Annual', 'Others']);
        $this->db->select(['leave_name', 'e_id']);
        $result5 = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

        $this->db->select('e_id, days');
        $this->db->where('r_id', $id);
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $result = $this->db->get(db_prefix() . 'labour_restdays')->result_array();
        $result6 = [];

        $RD = [];
        // Process the result
        foreach ($result as $row) {
            // Unserialize the 'days' column data
            $days = unserialize($row['days']);

            // Initialize a count variable
            $count = 0;

            // Count the days greater than or equal to $day
            foreach ($days as $d) {
                if ($d > $day) {
                    $count++;
                }
            }

            // Push e_id and count of days to the results array
            $result6[$row['e_id']] = $count;

            if (in_array($day, $days)) {
                $RD[] = $row['e_id'];
            }
        }

        // Combine all results into data array
        $data = array(
            'name' => $result1,
            'overtime' => $result2,
            'restday_balance' => $result6,
            'leaveTotal' => $leavecount,
            'today_work_status' => $result5,

        );

        // Calculate annual leave balances

        foreach ($leavecount as $row) {
            $e_id = $row['e_id'];
            $leaveTotal = 0;
            $leaveTotal = $row['leaveTotal'];

            if (!isset($data['annual_leave_balance'][$e_id])) {
                $data['annual_leave_balance'][$e_id] = intval($leaveTotal);
            }
        }

        foreach ($result4 as $row) {
            $e_id = $row['e_id'];
            $count = $row['count'];

            if (isset($data['annual_leave_balance'][$e_id])) {
                $data['annual_leave_balance'][$e_id] -= $count;
            }
        }
        $finalData = array();

        foreach ($data['name'] as $emp) {
            $empId = $emp['e_id'];

            $finalData[$empId] = array(
                'e_id' => $empId,
                'name' => $emp['name'],
                'restDays_balance' => isset($result6[$empId]) ? $result6[$empId] : 0,
                'overtime' => isset($data['overtime'][$empId]) ? $data['overtime'][$empId] : 0,
                'annual_leave_balance' => isset($data['annual_leave_balance'][$empId]) ? $data['annual_leave_balance'][$empId] : 0,
                'today_work_status' => 'Working'
            );

            if (in_array($empId, $RD)) {
                $finalData[$empId]['today_work_status'] = 'RD';
            } else {
                // If not a rest day, check if there's a leave status for the current day
                foreach ($data['today_work_status'] as $leaveStatus) {
                    if ($leaveStatus['e_id'] == $empId) {
                        $finalData[$empId]['today_work_status'] = $leaveStatus['leave_name']; // Set to leave name
                        break;
                    }
                }
            }
        }

        $response = array_values($finalData);

        return $response;
    }

    public function calender($id, $year, $month)
    {

        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->select('day');
        return $this->db->get(db_prefix() . 'labour_overtime')->result_array();
    }

    public function summary($id, $year, $month, $day)
    {

        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->select_sum('hrs', 'total_overtime');
        $result1 = $this->db->get(db_prefix() . 'labour_overtime')->row_array();

        $data['overtime'] = isset($result1['total_overtime']) ? $result1['total_overtime'] : 0;

        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->where_in('day', $day);
        $this->db->where_in('leave_name', "Annual");
        $this->db->select('e_id');
        $result2 = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->where_in('day', $day);
        $this->db->where_in('leave_name', "Others");
        $this->db->select('e_id');
        $result3 = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

        $Data['annual'] = $result2;
        $Data['others'] = $result3;

        $employee_id = [];
        $i = 0;
        foreach ($Data as $category => $values) {
            foreach ($values as $item) {
                $this->db->where('r_id', $id);
                $this->db->where('e_id', $item['e_id']);
                $this->db->select('name');
                $result = $this->db->get('tbllabour_data')->result_array();

                if (!empty($result)) {
                    $data[$category][] = $result[0]['name'];
                }

                if (!in_array($item['e_id'], $employee_id)) {
                    $employee_id[$i] = $item['e_id'];
                    $i++;
                }
            }
        }

        if (!isset($data['annual'])) {
            $data['annual'] = [];
        }
        if (!isset($data['others'])) {
            $data['others'] = [];
        }

        // Query for rest days
        $this->db->where_in('r_id', $id);
        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->select('e_id');
        $this->db->select('days');
        $result4 = $this->db->get('tbllabour_restdays')->result_array();

        $restDays = [];

        foreach ($result4 as $item) {
            $days = unserialize($item['days']);
            if (in_array($day, $days)) {
                $restDays[] = $item['e_id'];
            }
        }

        // Query for additional information based on 'e_id'
        $data['restDays'] = [];

        foreach ($restDays as $e_id) {
            $this->db->where('r_id', $id);
            $this->db->where('e_id', $e_id);
            $this->db->select('name');
            $result = $this->db->get('tbllabour_data')->row_array();

            if (!empty($result)) {
                $data['restDays'][] = $result['name'];
            }

            if (!in_array($e_id, $employee_id)) {
                $employee_id[$i] = $e_id;
                $i++;
            }
        }

        $this->db->select('e_id');
        $this->db->where('r_id', $id);
        $total = $this->db->get('tbllabour_data')->num_rows();

        $data['working_count'] = $total - count($employee_id);

        if ($data['working_count'] == 0) {
            $data['percent'] = null;
        } else {
            $data['percent'] = ($data['working_count'] / $total) * 100;
        }

        return $data;
    }

    public function check_setup($id)
    {

        $this->db->where_in('r_id', $id);
        $result1 = $this->db->get(db_prefix() . 'labour_traceable')->num_rows();

        $this->db->where_in('r_id', $id);
        $result2 = $this->db->get(db_prefix() . 'labour_leave_policy')->num_rows();

        if ($result1 == 0 && $result2 == 0) {
            return true;
        }
        return false;
    }

    public function restdays($id, $e_id, $year, $month, $data)
    {

        $this->db->where('r_id', $id);
        $this->db->where('e_id', $e_id);
        $this->db->where('year', $year);
        $this->db->where('month', $month);
        $result = $this->db->get('tbllabour_restdays')->result_array();

        $currentMonth = date('n'); // 'n' format returns the numeric representation of the month (1 for January, 2 for February, etc.)

        if ($currentMonth == 3 && $month == "January" && !$result) {

            // $this->db->where('r_id', $id);
            $this->db->where('e_id', $e_id);
            $this->db->select(['foreigner', 'fulltime']);
            $result1 = $this->db->get('tbllabour_data')->row_array();

            $fulltime = $result1["fulltime"];
            $foreigner = $result1["foreigner"];

            if ($fulltime == 'Yes' && $foreigner == 'Yes') {
                $scheduleType = 'foreigners_ftime';
            } elseif ($fulltime == 'No' && $foreigner == 'Yes') {
                $scheduleType = 'foreigners_ptime';
            } elseif ($fulltime == 'Yes' && $foreigner == 'No') {
                $scheduleType = 'local_ftime';
            } elseif ($fulltime == 'No' && $foreigner == 'No') {
                $scheduleType = 'local_ptime';
            }

            $this->db->where('r_id', $id);
            $this->db->select($scheduleType . ' AS total');
            $this->db->select(['carry_forward']);
            $result2 = $this->db->get('tbllabour_leave_policy')->row_array();

            $carryForward = $result2["carry_forward"];

            if ($carryForward == "yes") {
                $this->db->where('r_id', $id);
                $this->db->where('e_id', $e_id);
                $this->db->select('count');
                $this->db->limit(1);
                $this->db->order_by('id', 'desc');
                $result3 = $this->db->get('tbllabour_user_leave')->row_array();

                // $data['count'] = $result3['count'] + $annualLeave;
            }
        }

        // $currentDate = new DateTime();

        // foreach ($data['days'] as $date) {
        //     // Create a DateTime object for the current date in the loop
        //     $dateObject = DateTime::createFromFormat('Y-m-d', $date);

        //     // Compare the date object with the current date
        //     if ($dateObject <= $currentDate) {
        //         $data['count']--;
        //     }
        // }

        $datecreated = date('Y-m-d');

        if (!$result) {
            $restDay_details = [
                'r_id' => $id,
                'e_id' => $e_id,
                'month' => $month,
                'year' => $year,
                'days' => serialize($data['days']),
                'data' => serialize($data),
                'datecreated' => $datecreated,
            ];

            $this->db->insert('tbllabour_restdays', $restDay_details);

            $report = $this->db->insert_id();

            if ($report) {
                return $report;
            } else {
                return false;
            }
        } else {
            $restDay_details = [
                'days' => serialize($data['days']),
                'data' => serialize($data),
                'datecreated' => $datecreated,
            ];

            $this->db->where('r_id', $id);
            $this->db->where('e_id', $e_id);
            $this->db->where('year', $year);
            $this->db->where('month', $month);
            $report = $this->db->update(db_prefix() . 'labour_restdays', $restDay_details);

            if ($report) {
                return $report;
            } else {
                return false;
            }
        }
    }

    public function leave_check($year, $month, $day)
    {

        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        $this->db->select('e_id');
        $this->db->select('count');
        $this->db->select('days');
        $result1 = $this->db->get('tbllabour_restdays')->result_array();

        // $unserialized_value = [];
        $RD = [];
        foreach ($result1 as $key => $value) {
            // $unserialized_value = unserialize($value['days']);

            // if (in_array($day, $unserialized_value)) {
            //     $RD[] = $value['e_id'];

            $this->db->set('count', 'count - 1', false);
            $this->db->where_in('e_id', $value['e_id']);
            $this->db->where_in('year', $year);
            $this->db->where_in('month', $month);
            $this->db->update('tbllabour_restdays');
            // }
        }

        $this->db->where_in('year', $year);
        $this->db->where_in('month', $month);
        // $this->db->where_in('day', $day);
        // $this->db->where_in('leave_name', 'Annual');
        $this->db->select(['count', 'e_id']);
        $result2 = $this->db->get(db_prefix() . 'labour_user_leave')->result_array();

        // Decrement count column by 1
        foreach ($result2 as $row) {

            $this->db->set('count', 'count - 1', false);
            $this->db->where_in('e_id', $row['e_id']);
            $this->db->where_in('year', $year);
            $this->db->where_in('month', $month);
            $data = $this->db->update('tbllabour_restdays');
        }

        return $data;
    }
}
