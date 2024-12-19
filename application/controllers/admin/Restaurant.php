<?php

use Twilio\TwiML\Voice\Echo_;

defined('BASEPATH') or exit('No direct script access allowed');

class Restaurant extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('knowledge_base_model');
        $this->load->model('restaurant_model');
        $this->load->library('form_validation');
    }
    public function index()
    {

        if (!has_permission('restaurant', '', 'view')) {
            access_denied('restaurant');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('restaurant_view');
        }
        // $data['groups']    = $this->knowledge_base_model->get_kbg();
        $data['bodyclass'] = 'top-tabs kan-ban-body';
        $data['title']     = _l('restaurant_title');
        $this->load->view('admin/restaurant/view_restaurant', $data);
    }

    public function delete_restaurant($id)
    {
        if (!has_permission('knowledge_base', '', 'delete')) {
            access_denied('knowledge_base');
        }
        if (!$id) {
            redirect(admin_url('restaurant'));
        }
        $response = $this->restaurant_model->delete_restaurant($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('restaurant_name')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('kb_article_lowercase')));
        }
        redirect(admin_url('restaurant'));
    }

    public function password_creation($id)
    {

        if ($this->input->post()) {
            echo "helo";
            exit();
            $data = $this->input->post();
            $isexist = $this->restaurant_model->check_email_by_id($id, $data);
            echo $isexist;
            exit();
            if ($isexist) {
                set_alert('danger', _l('inactive_account'));
            }
        }

        $data['title'] = _l('admin_auth_login_heading');
        //$this->load->view('admin/restaurant/password_creation',$data);
    }

    public function restaurant_menus($id = '')
    {

        if (!has_permission('restaurant', '', 'view')) {
            access_denied('restaurant');
        }
        $group         = !$this->input->get('group') ? 'basic_information' : $this->input->get('group');

        if ($this->input->post()) {

            if ($id == '') {
                if (!has_permission('restaurant', '', 'create')) {
                    access_denied('restaurant');
                }

                $data = $this->input->post();
                $data['group'] = $group;

                $id = $this->restaurant_model->add_restaurantBusiness($data);

                if ($id) {
                    if ($data['payment_status'] == '1') {
                        set_alert('success', 'Email was sent');
                    }
                    redirect(admin_url('restaurant/restaurant_menus/' . $id . '?group=cuisine_types'));
                } else {
                    set_alert('warning', 'You cannot');
                    redirect(admin_url('restaurant/restaurant_menus/'));
                }
            } else { //update profile data
                if (!has_permission('customers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('customers');
                    }
                }

                $data = $this->input->post();
                $data['group'] = $group;

                switch ($group) {

                    case 'cuisine_types':
                        $id = $this->restaurant_model->cuisine_types($data, $id);
                        break;

                    case 'sales_channel':
                        $id = $this->restaurant_model->sales_channel($data, $id);
                        break;

                    case 'departments':
                        $id = $this->restaurant_model->departments($data, $id);
                        break;
                    default:
                        # code...
                        break;
                }

                $id = $this->restaurant_model->add_restaurantBusiness($data, $id);

                if (!empty($id)) {
                    if ($group == 'basic_information') {
                        redirect(admin_url('restaurant/restaurant_menus/' . $id));
                    } elseif ($group == 'cuisine_types') {
                        redirect(admin_url('restaurant/restaurant_menus/' . $id . '?group=sales_channel'));
                    } elseif ($group == 'sales_channel') {
                        redirect(admin_url('restaurant/restaurant_menus/' . $id . '?group=sales_channel'));
                    }
                }
            }
        }

        $data['group'] = $group;
        $data['customer_tabs'] = get_restaurant_setup_tabs();
        $data['tab']      = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;

        if (!$data['tab']) {
            show_404();
        }

        $client = $this->restaurant_model->get_restaurant($id);
        $plan = $this->restaurant_model->get_plan($client[0]['business_name']);
        if ($client) {
            if ($group == 'basic_information') {
                $business_type = $this->restaurant_model->get_restaurant_types($id);
                $data['business_type'] = $business_type[0]['name'];
            } elseif ($group == 'cuisine_types') {
                $result_array = $this->restaurant_model->get_cuisine_types($id);
                $data['cuisine_types'] = $result_array;
            } elseif ($group == 'departments') {
                $result_array = $this->restaurant_model->get_departments($id);
                $data['departments'] = $result_array;
            } elseif ($group == 'sales_channel') {
                $data['sales_channel'] = $this->restaurant_model->get_salesChannel($id);
                $values = $this->restaurant_model->get_sales_data($id);
                $data['food_values'] = $values['food_values'];
                $data['beverages_values'] = $values['beverages_values'];
            }
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['restaurant_details'] = $client;
        $data['plan'] = $plan;
        $title          = $client->company;

        // Get all active staff members (used to add reminder)
        $data['members'] = $data['staff'];






        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title']     = $title;
        $this->load->view('admin/restaurant/add_restaurant', $data);
    }

    public function view($id)
    {
        if (!has_permission('restaurant', '', 'view')) {
            access_denied('View Knowledge Base Article');
        }

        $data['res'] = $this->restaurant_model->get($id);
        $data['client'] = $this->restaurant_model->get_client_details($id);
        $data['cuisine'] = $this->restaurant_model->get_cuisine($id);
        $data['sales_channel'] = $this->restaurant_model->get_sales_channel($id);
        $data['plan'] = $this->restaurant_model->get_plan($id);
        $data['plan_duration'] = $this->restaurant_model->get_plan_duration($id);

        if (!$data) {
            show_404();
        }

        $this->load->view('admin/restaurant/view', $data);
    }

    public function restaurant_business()
    {
        if (!has_permission('restaurant', '', 'view')) {
            access_denied('restaurant');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('restaurant_business_view');
        }
        $this->load->view('admin/restaurant/view_business_type');
    }

    public function create_businesstype()
    {
        if (!has_permission('restaurant', '', 'create')) {
            access_denied('restaurant');
        }
        if ($this->input->post()) {
            $data = $this->input->post();

            if (!$this->input->post('id')) {

                $result = $this->restaurant_model->add_businesstype($data);
                if ($result['id']) {
                    set_alert('success', $result['alert']);
                } else {
                    set_alert('danger', $result['alert']);
                }
            } else {
                if (!has_permission('restaurant', '', 'edit')) {
                    access_denied('restaurant');
                }
                $id = $this->input->post('id');
                $result = $this->restaurant_model->update_businesstype($data, $id);
                if ($result) {
                    set_alert('success', $result['alert']);
                } else {
                    set_alert('danger', $result['alert']);
                }
            }

            redirect(admin_url('restaurant/restaurant_business'));
            die;
        }
    }

    public function delete_businesstype($id)
    {
        if (!has_permission('restaurant', '', 'delete')) {
            access_denied('restaurant');
        }

        if (is_numeric($id)) {
            $result = $this->restaurant_model->delete_businesstype($id);
            if ($result) {
                set_alert('success', 'Businesstype Deleted');
            } else {
                set_alert('danger', 'Cannot delete');
            }
        }
        redirect(admin_url('restaurant/restaurant_business'));
    }
}
