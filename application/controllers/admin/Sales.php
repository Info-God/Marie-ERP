<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_model');
        $this->load->model('restaurant_model');
        $this->load->model('clients_model');
    }

    public function index()
    {

        if (!has_permission('Sales', '', 'view')) {
            access_denied('Sales');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('sales_view');
        }
        $data['id'] = $this->Sales_model->get_id();
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('Sales');
        $this->load->view('admin/sales/view_sales', $data);
    }

    public function sales_channel($id, $Month = '')
    {
        if (!has_permission('Sales', '', 'edit')) {
            access_denied('Sales');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['reach'])) {
                if (isset($data['month'])) {
                    if ($data['month'] == '') {
                        set_alert('warning', 'Please select month');
                        redirect(admin_url('sales/sales_channel/' . $id));
                    }
                }
            }

            if (!empty($data['categories']) && !empty($data['items']) && !empty($data['sales_unit'])) {
                $this->row($id, $data);
            }
            if (
                $this->input->post()
                && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
            ) {
                $this->sales_import($id);
            }
            $result = $this->Sales_model->sales_channel($data, $id);

            if (!empty($data['food_values']) || !empty($data['beverages_values'])) {
                $result = $this->Sales_model->sales_data_by_channel($data, $id);
            }
            if ($result) {
                set_alert('success', 'Successfully Saved');
                if($Month!= ''){
                redirect(admin_url('sales/sales_channel/' . $id.'/'.$Month));
                }
                redirect(admin_url('sales/sales_channel/' . $id));
            } else {
                set_alert('danger', 'Deleted');
                if($Month!= ''){
                    redirect(admin_url('sales/sales_channel/' . $id.'/'.$Month));
                    }
                redirect(admin_url('sales/sales_channel/' . $id));
            }
        } else {
            $data['sales_channel'] = $this->restaurant_model->get_salesChannel($id);
            $values = $this->restaurant_model->get_sales_data($id, $Month);
            if ($values) {
                $data['food_values'] = $values['food_values'];
                $data['beverages_values'] = $values['beverages_values'];
                $data['month'] = $values['month'];
                $data['months'] = $values['month'];
                $data['id'] = $id;
                $value = $this->restaurant_model->get_sales_menu($id, $data['month']);
                if ($value) {
                    $data['sales_month'] = $value['sales_month'];
                    $data['count'] = $value['count'];
                    $data['row_id'] = $value['row_id'];
                    $data['menu_data'] = $value['menu_data'];
                }
            }
        }
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = 'Sales Module';
        $this->load->view('admin/sales/sales_type', $data);
    }

    public function sales_import($id = '')
    {
        if (!has_permission('sales', '', 'create')) {
            access_denied('sales');
        }

        $dbFields = $this->db->list_fields(db_prefix() . 'restaurant_sales_menu_data_sample');

        $this->load->library('import/import_sales', [], 'import');

        $this->import->setDatabaseFields($dbFields)
            ->setCustomFields(get_custom_fields('customers'));

        $this->import->id = $id;

        $data = $this->input->post();

        $this->import->month = $data['month'];

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if (
            $this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if ($this->import->totalImported() != 0 && $this->import->totalUpdated() != 0) {
                set_alert('success', 'Total Uploaded Rows: ' . $this->import->totalImported() .'<br>'. '  Total Updated Rows: ' . $this->import->totalUpdated());
                if($data['month']!= ''){
                    redirect(admin_url('sales/sales_channel/' . $id.'/'.$data['month']));
                    }
                redirect(admin_url('sales/sales_channel/' . $id));
            } elseif ($this->import->totalUpdated() != 0) {
                set_alert('success', _l('import_total_updated '. $this->import->totalUpdated()));
                if($data['month']!= ''){
                    redirect(admin_url('sales/sales_channel/' . $id.'/'.$data['month']));
                    }
                redirect(admin_url('sales/sales_channel/' . $id));
            } elseif ($this->import->totalImported() != 0) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
                if($data['month']!= ''){
                    redirect(admin_url('sales/sales_channel/' . $id.'/'.$data['month']));
                    }
                redirect(admin_url('sales/sales_channel/' . $id));
            } else {
                set_alert('warning', 'Given Rows already exists');
                if($data['month']!= ''){
                    redirect(admin_url('sales/sales_channel/' . $id.'/'.$data['month']));
                    }
                redirect(admin_url('sales/sales_channel/' . $id));
            }
        }
    }

    public function row($id, $data)
    {
        if (!has_permission('Sales', '', 'edit')) {
            access_denied('Sales');
        }
        $month = $data['month'];
        $result = $this->Sales_model->validate_row_sales_menu_data($id, $month,$data);
        if ($result) {
            set_alert('success', 'Successfully Saved');
            redirect(admin_url('sales/sales_channel/' . $id));
        } else {
            set_alert('danger', 'Failed to Save');
            redirect(admin_url('sales/sales_channel/' . $id));
        }
    }

    public function delete_row($id, $month,$row_id)
    {
        if (!has_permission('Sales', '', 'edit')) {
            access_denied('Sales');
        }
        $result = $this->Sales_model->delete_row($id, $month,$row_id);
        if ($result) {
            set_alert('success', 'Successfully Deleted');
            redirect(admin_url('sales/sales_channel/' . $id));
        } else {
            set_alert('danger', 'Failed to Delete');
            redirect(admin_url('sales/sales_channel/' . $id));
        }
    }
    
    public function month($id)
    {
        if (!has_permission('Sales', '', 'view')) {
            access_denied('Sales');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->id = $id;
            $this->app->get_table_data('sales_view2');
        }
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('Sales');
        $this->load->view('admin/sales/view_month', $data);
    }

    public function delete_month_data($id, $month)
    {
        if (!has_permission('Sales', '', 'edit')) {
            access_denied('Sales');
        }
        $result = $this->Sales_model->delete_month_data($id, $month);
        if ($result) {
            set_alert('success', 'Successfully Deleted');
            redirect(admin_url('sales/month/' . $id));
        } else {
            set_alert('danger', 'Failed to Delete');
            redirect(admin_url('sales/month/' . $id));
        }
    }
}
