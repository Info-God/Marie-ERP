<?php

class RegistrationApi_model extends CI_Model
{
    public function restaurant_registration($data){
                
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

                    $client_details = [
                        'client_user' => $insert_id,
                        'client_email' => $data['contactInformation']['email'],
                        'client_password' => null,
                        'client_phoneNumber' => $data['contactInformation']['phone']
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

                }
                $business_hours = $data['operatingDays'];

                $operating_days = [
                    'restaurant' => $insert_id,
                    'operating_hours' => serialize($business_hours)
                ];

                $this->db->insert(db_prefix().'restaurant_operating_days',$operating_days);
                
                return $insert_id;
    
        }



}