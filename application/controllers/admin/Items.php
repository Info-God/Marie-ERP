<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Items extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Items_model');
    }

    public function index()
    {

        if (!has_permission('items', '', 'view')) {
            access_denied('items');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('items_table1');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'Items and Categories';
        $this->load->view('admin/items/view_items', $data);
    }

    public function validate()
    {

        if (!has_permission('items', '', 'view')) {
            access_denied('items');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('items_table2');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['method']     = $this->router->method;
        $data['title']     = 'Items and Categories';
        $this->load->view('admin/items/view_items', $data);
    }

    public function create_items($id = '')
    {

        if (!has_permission('items', '', 'create')) {
            access_denied('items');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$data['id']) {

                $result = $this->Items_model->add_item($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                } else {
                    set_alert('danger', $result['alert']);
                }
            } else {
                if (!has_permission('items', '', 'edit')) {
                    access_denied('items');
                }
                $result = $this->Items_model->update_item($data, $data['id']);
                if ($result) {
                    set_alert('success', $result['alert']);
                }
            }
            die;
        }
    }

    public function delete_item($id)
    {
        if (!has_permission('items', '', 'delete')) {
            access_denied('items');
        }
        if (!empty($id)) {
            $result = $this->Items_model->delete_item($id);
            if ($result) {
                set_alert('success', 'Item Deleted');
                redirect(admin_url('items'));
            } else {
                set_alert('danger', 'Cannot delete');
                redirect(admin_url('items'));
            }
        }
    }

    public function validation($id)
    {
        if (!has_permission('items', '', 'edit')) {
            access_denied('items');
        }
        if (!empty($id)) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'restaurant_menu_categories', ["validated" => 1]);

            if ($this->db->affected_rows() > 0) {
                set_alert('success', 'Item is validated');
                redirect(admin_url('items'));
            } else {
                set_alert('danger', 'Cannot validate');
                redirect(admin_url('items'));
            }
        }
    }
}
