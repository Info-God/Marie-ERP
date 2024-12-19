<?php

class MarieLogin_model extends CI_Model{

    public function getRestaurant(){
        return $this->db->get('tblrestaurant_registration')->result_array();

    }
    public function hash_password($password){
        return password_hash($password,   PASSWORD_BCRYPT  );
    }

    public function create_password($id, $data){
        $this->db->where('client_user',$id);
        return $this->db->update(db_prefix().'client_details', $data);
    }

    public function check_email_by_id($id, $email) {
        $this->db->where('client_user', $id);
        $this->db->where('client_email', $email);
        $query = $this->db->get(db_prefix() . 'client_details'); 
        
        return $query->num_rows() === 1;
    }

    public function login_check($email,$password) {
        $this->db->select('client_password');
        $this->db->where('client_email',$email);
        $data = $this->db->get('tblclient_details')->result_array();
        if(empty($data)){
            return false;
        }
        $encrypted_password = $data[0]['client_password'];

        $verified_password = password_verify($password,$encrypted_password);
        if($verified_password > 0 ){
            return true;
        }
        else{
            return false;
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
            $data['verification']='Email Verified';
        } else {
            $data['verification']='Email Not Verified';
        }

        $data['email']=$email;
        $data['name']=$name[0]['business_name'];

        return $data;
    }
}