<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class Sales extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/SalesApi_model');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $this->load->library('Authorization_Token');
    }

    public function init_post()
    {
        $sales_model = new SalesApi_model;

        $data = $sales_model->init();

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    
    public function sales_channel_post()
    {
        $sales_model = new SalesApi_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data['reach'] = $inputdata['channels'];
        $data['food_values'] = $inputdata['food'];
        $data['beverages_values'] = $inputdata['beverages'];
        $data['values'] = $inputdata['values'];
        $data['year'] = $inputdata['year'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];
        $data['month'] = $inputdata['month'];
        $id = $inputdata['userId'];


        $results = $sales_model->sales_channels($data, $id);
        $result = $sales_model->sales_data_by_channel_api($data, $id);

        if ($result && $results) {
            $values = $sales_model->get_sales_data($id, $month,$year);
            if ($values) {
                $data['food_values'] = $values['food_values'];
                $data['beverages_values'] = $values['beverages_values'];
            }
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sales_menu_post()
    {
        $sales_model = new SalesApi_model;

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }
        $result = $sales_model->sales_menu();

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'categories' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Bad Request'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sales_menu_insert_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);
        
        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data = $inputdata['data'];
        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];
        // print_r($inputdata);die('hii');

        $result = $sales_model->validate_sales_menu_data($data, $id, $month, $year);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function get_sales_month_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);
        
        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }

        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        if(!isset($id) || !isset($month) || !isset($year)){
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data['sales_channel'] = $sales_model->get_sales_channel($id, $month,$year);
        $data['sales_channel_data'] = $sales_model->get_sales_data($id, $month,$year);
        $data['sales_menu'] = $sales_model->get_sales_menu($id, $month,$year);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sales_menu_row_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);
        
        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }

        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $row_id = $inputdata['item_no'];
        $year = $inputdata['year'];

        $data = $sales_model->get_sales_menu_row($id, $month, $row_id,$year);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed To Fetch'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sales_menu_upload_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }
        $data = $inputdata['data'];
        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        $result = $sales_model->validate_sales_menu_data($data, $id, $month,$year);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sales_menu_edit_delete_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }
        $data = $inputdata['data'];
        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        // print_r($inputdata);
        // die('hii');

        if ($inputdata['button'] == 'edit') {
            $row_id = $sales_model->validate_sales_menu_row_data($id, $month , $year, $data);
            // print_r($row_id);die('hii');

            if ($row_id) {
                $data = $sales_model->get_sales_menu_row($id, $month, $row_id,$year);
                $this->response([
                    'status' => 200,
                    'message' => 'Success',
                    'data' => $data
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => 500,
                    'message' => 'Failed to Edit'
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $result = $sales_model->delete_row($id, $month,$year, $data[0]['id']);
            if ($result) {
                $this->response([
                    'status' => 200,
                    'message' => 'Delete Success',
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => 500,
                    'message' => 'Failed to Delete'
                ], RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function request_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }
        $data = $inputdata['selectedChannels'];
        $id = $inputdata['userId'];

        $result = $sales_model->request($data, $id);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function delete_multiple_post()
    {
        $sales_model = new SalesApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }
        $delete = $inputdata['delete'];
        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        $result = $sales_model->delete_row_multiple($id, $month, $year, $delete);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
