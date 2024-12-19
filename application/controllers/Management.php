<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Management extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('restaurant_model');
    }

    public function password_creation($id)
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            $email = $data['email'];
            $isexist = $this->restaurant_model->check_email_by_id($id,$email);
            if (!$isexist) {
                set_alert('danger', _l('inactive_account'));
                redirect(base_url('management/password_creation/'.$id));
            }
            $password = $this->input->post('password');
            $result = $this->restaurant_model->update_password($id, $password);
            if($result){
                set_alert('success', _l('User_created'));
                redirect(site_url('authentication/login_client' )); 
            }     
        }

        $data['title'] = "password creation";
        $this->data($data);
        $this->view('password_creation1');
        $this->layout();
    }
}