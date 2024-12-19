<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Restaurant_departments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Restaurant_department_model');
    }    
    
    public function index()
    {

        if (!has_permission('restaurant', '', 'view')) {
            access_denied('restaurant');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('department_table');
        }
        // $data['groups']    = $this->knowledge_base_model->get_kbg();
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'Departments';
        $this->load->view('admin/restaurant_departments/view_departments',$data);
    }

    public function create_department($id = ''){

        if (!is_admin() && get_option('staff_members_save_tickets_predefined_replies') == '0') {
            access_denied('Ticket Services');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $id = $this->Restaurant_department_model->add_department($data);
                if ($id) {
                    set_alert('success', 'Department Created');
                }

            } else {
                $id = $id;
                $success = $this->plans_model->update_department($data, $id);
                if ($success) {
                    set_alert('success', 'Department Updated');
                }
            }
            die;
        }
    }

    public function delete_department($id){
        if(!empty($id)){
            $result = $this->Restaurant_department_model->delete_department($id);
            if($result){
                set_alert('success', 'Department Deleted');
                redirect(admin_url('restaurant_departments'));
            }
            else{
                set_alert('danger', 'You cannot deleted');
                redirect(admin_url('restaurant_departments'));
            }
        }
    }

}