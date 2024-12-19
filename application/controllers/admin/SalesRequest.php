<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SalesRequest extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_request_model');
    }

    public function index()
    {
        
        if (!has_permission('SalesRequest', '', 'view')) {
            access_denied('SalesRequest');
        }
        if ($this->input->is_ajax_request()) {
           $data =  $this->app->get_table_data('sales_request');
           
        }
        // $data['groups']    = $this->knowledge_base_model->get_kbg();
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'Sales Request';
        $this->load->view('admin/salesRequest/view', $data);
    }

    public function accept($id)
    {

        if (!has_permission('SalesRequest', '', 'view')) {
            access_denied('SalesRequest');
        }

            if ($id) {
                $result = $this->Sales_request_model->accept($id);
                if ($result) {
                    set_alert('success', 'Request Accepted');
                redirect(admin_url('SalesRequest'));
                }else{
                    set_alert('danger', 'Failed to accept');
                redirect(admin_url('SalesRequest'));
                }
            }
    }

    public function decline($id)
    {

        if (!has_permission('SalesRequest', '', 'view')) {
            access_denied('SalesRequest');
        }

        if ($id) {
            $result = $this->Sales_request_model->decline($id);
            if ($result) {
                set_alert('success', 'Request Declined');
                redirect(admin_url('SalesRequest'));
            } else {
                set_alert('danger', 'Failed to decline');
                redirect(admin_url('SalesRequest'));
            }
        }
    }
}
