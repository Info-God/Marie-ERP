<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Plans extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('plans_model');
    }

    public function index()
    {
        if (!has_permission('plans', '', 'view')) {
            access_denied('plans');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('restaurant_plans_table');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'Restaurant Plans';
        $this->load->view('admin/restaurant_plans/view_plans',$data);
    }

    public function create_plans($id = ''){
        if (!has_permission('plans', '', 'view')) {
            access_denied('plans');
        }
        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $id = $this->plans_model->add_plan($data);
                if ($id) {
                    set_alert('success', 'Plan Created');
                }

            } else {
                $success = $this->plans_model->update_plan($data, $data['id']);
                if ($success) {
                    set_alert('success', 'Plan Updated');
                }
            }
            die;
        }
    }

    public function delete_plans($id){
        if (!has_permission('plans', '', 'view')) {
            access_denied('plans');
        }
        if(!empty($id)){
            $result = $this->plans_model->delete_plan($id);
            if($result){
                set_alert('success', 'Plan Deleted');
                redirect(admin_url('plans'));
            }
            else{
                set_alert('danger', 'You cannot deleted');
                redirect(admin_url('plans'));
            }
        }
    }
}

