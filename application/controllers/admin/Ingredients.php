<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ingredients extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Ingredients_model');
    }

    public function index()
    {

        if (!has_permission('ingredient', '', 'view')) {
            access_denied('ingredient');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('ingredients_table');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'ingredient Categories';
        $this->load->view('admin/ingredients/view_ingredients', $data);
    }

    public function index1()
    {

        if (!has_permission('ingredient', '', 'view')) {
            access_denied('ingredient');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('ingredients_table');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = 'ingredient Categories';
        $this->load->view('admin/ingredients/view_ingredients', $data);
    }

    public function create_ingredients($id = '')
    {

        if (!has_permission('ingredient', '', 'create')) {
            access_denied('ingredient');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $result = $this->Ingredients_model->add_ingredients($data);

                // if ($result['id']) {
                //     set_alert('success', 'Success');
                //     echo json_encode("success")
                // } else {
                //     set_alert('danger', 'Failed');
                //     redirect(admin_url('ingredients/index1'));
                // }
                header('Content-Type: application/json');
                if ($result['id']) {
                    set_alert('success', 'Success');
                    echo json_encode(['success' => true]);
                } else {
                    set_alert('danger', 'Failed');
                    echo json_encode(['success' => false, 'message' => 'An error occurred.']);
                }
            } else {
                if (!has_permission('ingredient', '', 'edit')) {
                    access_denied('ingredient');
                }
                $result = $this->Ingredients_model->update_ingredients($data, $data['id']);
                if ($result['result']) {
                    set_alert('success', $result['alert']);
                    redirect(admin_url('ingredients'));
                } else {
                    set_alert('danger', $result['alert']);
                    redirect(admin_url('ingredients'));
                }
            }
            // die;
        }
    }

    public function delete_ingredients($id)
    {
        if (!has_permission('ingredient', '', 'delete')) {
            access_denied('ingredient');
        }
        if (!empty($id)) {
            $result = $this->Ingredients_model->delete_ingredient($id);
            if ($result) {
                set_alert('success', 'Category and its ingredient\s deleted');
                redirect(admin_url('ingredients'));
            } else {
                set_alert('danger', 'Cannot delete');
                redirect(admin_url('ingredients'));
            }
        }
    }
}
