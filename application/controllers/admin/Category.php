<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model');
    }

    public function index()
    {
    
        if (!has_permission('items', '', 'view')) {
            access_denied('items');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('categories');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'Categories';
        $this->load->view('admin/categories/view_categories',$data);
    }

    public function create_category($id = ''){

            if (!has_permission('items', '', 'create')) {
                access_denied('items');
            }
    
            if ($this->input->post()) {
                $data = $this->input->post();
    
                if (!$this->input->post('id')) {
                    $result = $this->Category_model->add_category($data);
                    if ($result['id']) {
                        set_alert('success', 'Category Added');
                    }else{
                        set_alert('danger', 'Category already exist');
                    }
    
                } else {
                    if (!has_permission('items', '', 'edit')) {
                        access_denied('items');
                    }
                    $id = $id;
                    $result = $this->Items_model->update_item($data, $id);
                    if ($result) {
                        set_alert('success', $result['alert']);
                    }
                }
                die;
            }
    }

    public function delete_category($id){
        if (!has_permission('category', '', 'delete')) {
            access_denied('items');
        }
        if(!empty($id)){
            $result = $this->Category_model->delete_category($id);
            if($result){
                set_alert('success', 'Category deleted');
                redirect(admin_url('category'));
            }
            else{
                set_alert('danger', 'Cannot delete');
                redirect(admin_url('category'));
            }
        }
    }
}

