<?php
define('basic','1');
define('standard','2');
define('pro','3');
class RegistrationApi_model extends CI_Model
{
    public function restaurant_registration($data){

        // print_r($data);die;
        $data['datecreated'] = date('Y-m-d H:i:s');

        $business_details = [
            'business_name' => $data['BasicInformation']['businessName'],
            'b_address' => $data['contactInformation']['address'],
            'b_postcode' => $data['contactInformation']['pincode'],
            'b_state' => $data['contactInformation']['state'],
            'b_country' => $data['contactInformation']['country'],
            'business_type' => $data['BasicInformation']['businessType'],
            'seating_capacity' => $data['otherInformation']['dineInCapacity'],
            'datecreated' => $data['datecreated']
        ];

        $this->db->insert('restaurant_registration', $business_details);

        if ($this->db->affected_rows() > 0) {

            $insert_id = $this->db->insert_id();

            $verify = ($data['email_verify_status'] == true) ? 1 : 0;

            $client_details = [
                'client_user' => $insert_id,
                'client_email' => $data['contactInformation']['email'],
                'client_password' => null,
                'client_phoneNumber' => $data['contactInformation']['phone'],
                'email_verify_status' => $data['email_verify_status']
            ];

            $this->db->insert('client_details', $client_details);

            log_activity('New Restaurant Added [RestaurantID: ' . $insert_id . ']');
            
            $cuisine = $data["otherInformation"]['typeOfCuisine'];
            if ($insert_id) {
                if (isset($cuisine)) {
                    foreach ($cuisine as $cuisines) {
                        $this->db->insert(db_prefix() . 'cuisine_data', [
                            'r_id' => $insert_id,
                            'cuisine_id'     => $cuisines,
                        ]);
                    }
                }
            }

            $channels = $data["otherInformation"]['channels'];
            // $month = $data["otherInformation"]['currentMonth'];
            // $year = $data["otherInformation"]['currentYear'];
            if ($insert_id) {
                if (isset($channels)) {
                    foreach ($channels as $reach) {
                        $this->db->insert(db_prefix() . 'restaurant_reach_data', [
                            'r_id' => $insert_id,
                            // 'month' => $month,
                            // 'year' => $year,
                            'reach'     => $reach,
                            'datecreated'     => $data['datecreated'],
                        ]);
                    }
                }
            }

        }

        $business_hours = $data['operatingDays'];

        $operating_days = [
            'restaurant' => $insert_id,
            'operating_hours' => serialize($business_hours)
        ];

        $this->db->insert(db_prefix().'restaurant_operating_days',$operating_days);
        

        $dateString = $data['datecreated'];
        $date = new DateTime($dateString);
        $date->add(new DateInterval('P1Y'));
        $date->sub(new DateInterval('P1D'));
        $newDate = $date->format('Y-m-d');
        $plan_details = [
            'restaurant_user' => $insert_id,
            'restaurant_plan' => $data['plan_type'],
            'datecreated' => $data['datecreated'],
            'nextduedate' => $newDate
        ];
        $this->db->insert('restaurant_user_plans', $plan_details);

        $password = $data['password'];
        $hashed_password = password_hash($password,   PASSWORD_BCRYPT  );

        $this->db->where('client_user',$insert_id);
        $update_password = [
            'client_password' => $hashed_password
        ];
        $output = $this->db->update(db_prefix().'client_details', $update_password);

        $this->db->where('plan_id', $data['plan_type']);
        $this->db->select('plan_cost');
        $plan = $this->db->get(db_prefix() . 'restaurant_plans')->row_array();

        if($output){
            $this->db->select('tblrestaurant_registration.business_name,tblclient_details.client_email');
            $this->db->from('tblclient_details');
            $this->db->join('tblrestaurant_registration','tblrestaurant_registration.restaurant_id = tblclient_details.client_user');
            $this->db->where('tblrestaurant_registration.restaurant_id',$insert_id);

            $result = $this->db->get()->result_array();
            $result['userid'] = $insert_id;
            $result['plan_type']  = $data['plan_type'];
            $result['plan_cost']  = $plan['plan_cost'];

            return $result;
        }
    
    }
    public function check_email_exist($email) {
        $this->db->where('client_email', $email);
        $query = $this->db->get(db_prefix() . 'client_details'); 
        if($query->num_rows() == 0){
            return true;
        }else{
            return false;
        }

    }

    public function commonForAll($userId)
    {
        $data = $this->db->select('restaurant.b_country as country, restaurant.datecreated,restaurant.restaurant_id as userId,
                restaurant.business_name as restaurantName,billing.restaurant_plan,
                billing.datecreated,billing.nextduedate,client.client_email,client.client_phoneNumber,client.email_verify_status')
            ->from('tblrestaurant_registration as restaurant')
            ->join('tblrestaurant_user_plans as billing', 'billing.restaurant_user = restaurant.restaurant_id')
            ->join('tblclient_details as client', 'client.client_user = restaurant.restaurant_id')
            ->where('restaurant.restaurant_id', $userId)
            ->get()->result_array();

        if ($data) {
            $data[0]['currency'] = $data[0]['country'] == 'India' ? 'INR' : 'RM';

            $this->db->where('r_id', $userId);
            $this->db->order_by('id', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get(db_prefix() . 'restaurant_reach_request')->row_array();

            if ($query) {
                if ($query['notify'] == 0 && $query['status'] != 0) {
                    if ($query['status'] == 1) {
                        $data['notification'] = "Request Accepted";
                    } elseif ($query['status'] == 2) {
                        $data['notification'] = "Request Declined";
                    }

                    $update = [
                        "notify" => 1,
                    ];
                    $this->db->where('id', $query['id']);
                    $this->db->update(db_prefix() . 'restaurant_reach_request', $update);
                }
            }

            $this->db->where_in('r_id', $userId);
            $total = $this->db->get(db_prefix() . 'labour_leave_policy')->row_array();
            $total = $total['total_labour_count'];

            $this->db->where('r_id', $userId);
            $Total = $this->db->get('tbllabour_data')->num_rows();

            if ($Total <= $total) {
                $data['labour_count'] = $total - $Total;
            }else {
                $data['labour_count'] = false;
            }

            $futureDate = new DateTime($data[0]["datecreated"]);

            $futureDate->modify('+30 days');

            $FutureDate = $futureDate->format('Y-m-d');

            $todayDate = new DateTime('today');

            $interval = $futureDate->diff($todayDate);

            $todayDate = $todayDate->format('Y-m-d');

            if ($FutureDate >= $todayDate) {
                $data['costing_remaining_days'] = $interval->days;
                $data['costingDate'] = $FutureDate;
            } else {
                $data['costing_remaining_days'] = false;
            }

            return $data;
        }

        return false;
    }
}