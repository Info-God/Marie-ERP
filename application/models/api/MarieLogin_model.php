<?php

class MarieLogin_model extends CI_Model{

    public function getRestaurant(){
        return $this->db->get('tblrestaurant_registration')->result_array();

    }

    public function create_password($client_email, $data){
        $this->db->where('client_email',$client_email);
        return $this->db->update(db_prefix().'client_details', $data);
    }

    public function hash_password($password){
        return password_hash($password,   PASSWORD_BCRYPT  );
    }

    public function check_email_by_id($id, $email) {
        $this->db->where('client_user', $id);
        $this->db->where('client_email', $email);
        $query = $this->db->get(db_prefix() . 'client_details'); 
        
        return $query->num_rows() === 1;
    }

    public function check_email($email) {
        $this->db->where('client_email', $email);
        $query = $this->db->get(db_prefix() . 'client_details'); 
        
        return $query->num_rows() === 1;
    }

    public function get_email_id($email) {
        $this->db->select('client_password');
        $this->db->select('client_user');
        $this->db->where('client_email',$email);
        $data = $this->db->get('tblclient_details')->result_array();
        $getdata['id']=$data[0]['client_user'];
        $getdata['verify']=true;
        
        return $getdata;
    }

    public function login_check($email,$password) {
        $this->db->select('client_password');
        $this->db->select('client_user');
        $this->db->select('deactivate');
        $this->db->where('client_email',$email);
        $data = $this->db->get('tblclient_details')->result_array();

        if(empty($data)){
            return false;
        }
        if ($data[0]['deactivate'] == 1) {
            $getdata['deactivate']=true;
            return $getdata;
        }

        $getdata['deactivate']=false;
        $encrypted_password = $data[0]['client_password'];
        $getdata['id']=$data[0]['client_user'];

        $verified_password = password_verify($password,$encrypted_password);
        if($verified_password > 0 ){
            $getdata['verify']=true;
            // print_r($verified_password);
            // die('hii');
            return $getdata;
        }
        else{
            $getdata['verify']=false;
            // print_r($getdata);
            // die('hii');
            return $getdata;
        }
    }

    public function email_verify($email)
    {
        $this->db->select('client_user');
        $this->db->where('client_email',$email);
        $user = $this->db->get('client_details')->result_array();
        if(empty($user)){
            return false;
        }
        $id= $user[0]['client_user'];

        $this->db->select('business_name');
        $this->db->where('restaurant_id',$id);
        $name=$this->db->get('restaurant_registration')->result_array();
        if(empty($name)){
            return false;
        }

        $this->db->select('email_verify_status');
        $this->db->where('client_email',$email);
        $verify = $this->db->get('client_details')->result_array();

        if ($verify[0]['email_verify_status']==1) {
            $data['verification']='1';
        } else {
            $data['verification']='0';
        }

        $data['email']=$email;
        $data['name']=$name[0]['business_name'];

        $this->db->where('restaurant_user',$id);
        $type = $this->db->get('restaurant_user_plans')->result_array();

        $data['type']=$type[0]['restaurant_plan'];

            //         print_r($data);
            // die('hii');

        return $data;
    }

    public function random_code_generator()
    {
        $randomSequence = '';

        for ($i = 0; $i < 6; $i++) {
            $randomSequence .= rand(0, 9); // Appending a random digit (0-9) to the sequence
        }

        return $randomSequence;
    }

    public function get_restaurant_Name($id)
    {
        $this->db->select('business_name');
        $this->db->where('restaurant_id', $id);
        $result=$this->db->get('tblrestaurant_registration')->row_array();

        if ($result) {
            return $result['business_name'];
        } else {
            return false;
        }
    }

    public function get_restaurant_id($email)
    {
        $this->db->select('client_user');
        $this->db->where('client_email', $email);
        $result=$this->db->get('tblclient_details')->row_array();

        if ($result) {
            return $result['client_user'];
        } else {
            return false;
        }
        
    }
}