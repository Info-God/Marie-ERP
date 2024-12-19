<?php

defined('BASEPATH') or exit('No direct script access allowed');

class restaurant_cuisine extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Restaurant_cuisine_model');

    }
    public function index()
    {

        if (!has_permission('restaurant', '', 'view')) {
            access_denied('restaurant');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('restaurant_cuisine');
        }
        // $data['groups']    = $this->knowledge_base_model->get_kbg();
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('restaurant_title');
        $this->load->view('admin/restaurant_cuisine/view_cuisine',$data);
    }

    public function create_cuisine($id = ''){

        if ($this->input->post()) {
            $data = $this->input->post();

            // print_r($data);
            // die('hii');

            if (!$this->input->post('id')) {
            
                $id = $this->Restaurant_cuisine_model->add_cuisine($data);
                if ($id) {
                    set_alert('success', 'Cuisine Created');

                }

            } else {
                $success = $this->Restaurant_cuisine_model->update_cuisine($data, $data['id']);
                if ($success) {
                    set_alert('success', 'Cuisine Updated');

                }
            }
            die;
        }
    }

    public function delete_cuisine($id){
        if(!empty($id)){
            $result = $this->Restaurant_cuisine_model->delete_cuisine($id);
            if($result){
                set_alert('success', 'Cuisine Deleted');
                redirect(admin_url('restaurant_cuisine'));
            }
            else{
                set_alert('danger', 'It cannot be Deleted');
                redirect(admin_url('restaurant_cuisine'));
            }
        }
    }
}
