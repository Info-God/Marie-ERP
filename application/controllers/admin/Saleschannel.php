<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Saleschannel extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Saleschannel_model');

    }

    public function index()
    {

        if (!has_permission('Saleschannel', '', 'view')) {
            access_denied('Saleschannel');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('saleschannel_view');
        }
        
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('Saleschannel');
        $this->load->view('admin/saleschannel/view_saleschannel', $data);
    }

    public function create_saleschannel($id = ''){
        
        if (!has_permission('Saleschannel', '', 'create')) {
            access_denied('Saleschannel');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {
            
                $result = $this->Saleschannel_model->add_saleschannel($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }

            } else {
                if (!has_permission('saleschannel', '', 'edit')) {
                    access_denied('saleschannel');
                }
                $id = $this->input->post('id');
                $result = $this->Saleschannel_model->update_saleschannel($data, $id);
                if ($result) {
                    set_alert('success', $result['alert']);
                }else{
                    set_alert('danger', $result['alert']);
                }
            }
            redirect(admin_url('saleschannel'));
        }
    }

    public function delete_saleschannel($id) {
        if (!has_permission('saleschannel', '', 'delete')) {
            access_denied('saleschannel');
        }
        
        if (is_numeric($id)) {
            $result = $this->Saleschannel_model->delete_saleschannel($id);
            if ($result) {
                set_alert('success', 'Saleschannel Deleted');
            } else {
                set_alert('danger', 'Cannot delete');
            }
        }
        redirect(admin_url('saleschannel'));
    }
    
}