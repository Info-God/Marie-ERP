<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Overhead extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Overhead_model');
    }
    

    public function index()
    {

        if (!has_permission('overhead', '', 'view')) {
            access_denied('overhead');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('overhead_table');
        }
        
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('Overheads');
        $this->load->view('admin/overhead/manage',$data);
    }

    public function create_overheads($id = ''){

        if (!has_permission('overhead', '', 'create')) {
            access_denied('overhead');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $result = $this->Overhead_model->add_overheads($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }

            } else {
                if (!has_permission('saleschannel', '', 'edit')) {
                    access_denied('saleschannel');
                }
                $id = $id;
                $result = $this->Overhead_model->update_overheads($data, $id);
                if ($result) {
                    set_alert('success', $result['alert']);
                }
            }
            
            redirect(admin_url('overhead'));
            die;
        }
    }

    public function delete_overhead($id) {
        if (!has_permission('overhead', '', 'delete')) {
            access_denied('overhead');
        }
        
        if (is_numeric($id)) {
            $result = $this->Overhead_model->delete_overhead($id);
            if ($result) {
                set_alert('success', 'Overhead Deleted');
            } else {
                set_alert('danger', 'Cannot delete');
            }
        }
        redirect(admin_url('overhead'));
    }

    //Overhead Cycle

    public function viewoverheadcycle(){
        if (!has_permission('overhead', '', 'view')) {
            access_denied('overhead');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('overheadcycle_table');
        }
        $this->load->view('admin/overhead/viewcycle');
    }

    public function create_cycle($id = ''){

        if (!has_permission('overhead', '', 'create')) {
            access_denied('overhead');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $result = $this->Overhead_model->add_cycle($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }
            }else {
                if (!has_permission('overhead', '', 'edit')) {
                    access_denied('overhead');
                }
                $id = $this->input->post('id');
                $result = $this->Overhead_model->update_cycle($data, $id);
                if ($result) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }
            }
            
            redirect(admin_url('overhead/viewoverheadcycle'));
            die;
        }
    }
    
    public function delete_cycle($id) {
        if (!has_permission('overhead', '', 'delete')) {
            access_denied('overhead');
        }
        
        if (is_numeric($id)) {
            $result = $this->Overhead_model->delete_cycle($id);
            if ($result) {
                set_alert('success', 'Overhead Cycle Deleted');
            } else {
                set_alert('danger', 'Cannot delete');
            }
        }
        redirect(admin_url('overhead/viewoverheadcycle'));
    }

    //Overhead Report

    public function viewoverheadreport(){
        if (!has_permission('overhead', '', 'view')){
            access_denied('overhead');
        }
        if ($this->input->is_ajax_request()){
            $this->app->get_table_data('overheadreport_table');
        }
        $this->load->view(('admin/overhead/viewreport'));
    }

    public function create_report($id = ''){

        if (!has_permission('overhead', '', 'create')) {
            access_denied('overhead');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $result = $this->Overhead_model->add_report($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }

            }else {
                if (!has_permission('overhead', '', 'edit')) {
                    access_denied('overhead');
                }
                $id = $this->input->post('id');
                $result = $this->Overhead_model->update_report($data, $id);
                if ($result) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }
            }
            
            redirect(admin_url('overhead/viewoverheadreport'));
            die;
        }
    }

    public function delete_report($id) {
        if (!has_permission('overhead', '', 'delete')) {
            access_denied('overhead');
        }

        if (is_numeric($id)) {
            $result = $this->Overhead_model->delete_report($id);
            if ($result) {
                set_alert('success', 'Overhead Report Deleted');
            } else {
                set_alert('danger', 'Cannot delete');
            }
        }
        redirect(admin_url('overhead/viewoverheadreport'));
    }
    
}