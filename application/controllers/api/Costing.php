<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class Costing extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/CostingApi_model');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $this->load->helper('security');
        $this->load->library('Authorization_Token');

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }
    }

    public function initialization_post()
    {
        $costing_model = new CostingApi_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $id = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];

        $data = $costing_model->initialization($id, $month, $year);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function overheads_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;
        $userId = $inputdata['userId'];
        // $category = $inputdata['category'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $data['salesType'] = $inputdata['salesType'];
        $data['sales_channel'] = $inputdata['salesChannel'];
        $data['ing_count'] = $inputdata['ingCount'];
        $data['preparation'] = $inputdata['preparation'];
        $data['cooking'] = $inputdata['cooking'];
        $data['batch'] = $inputdata['batch'];

        $result = $overheads->costing($userId, $year, $month, $data);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
    public function indirect_labour_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;
        $userId = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $data['salesType'] = $inputdata['salesType'];
        $data['sales_channel'] = $inputdata['salesChannel'];
        $data['ing_count'] = $inputdata['ingCount'];
        $data['preparation'] = $inputdata['preparation'];
        $data['cooking'] = $inputdata['cooking'];
        $data['batch'] = $inputdata['batch'];

        $result = $overheads->indirect_labour($userId, $year, $month, $data);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sides_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;
        $userId = $inputdata['userId'];
        $data = $inputdata;

        $result = $overheads->save_sides($userId, $data);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function fetch_sides_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;
        $userId = $inputdata['userId'];
        $sideName = $inputdata['sideName'];

        $result = $overheads->fetch_sides($userId, $sideName);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function save_costing_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $userId = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $data = $inputdata;

        $result = $overheads->save_costing($userId, $year, $month, $data);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function fetch_costing_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $id = $inputdata['id'];

        $result = $overheads->fetch_costing($id);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function delete_costing_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $id = $inputdata['id'];

        $result = $overheads->delete_costing($id);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function performance_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $id = $inputdata['id'];
        $r_id = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];

        $result = $overheads->performance($id, $r_id, $month, $year);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function homeScreen_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $sales_channel = $inputdata['sales_Channel'];
        $id = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $search = $inputdata['search'];

        $result = $overheads->homescreen($id, $month, $year, $sales_channel, $search);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function copy_costing_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;

        $name = $inputdata['name'];
        $id = $inputdata['userId'];
        $sales_channel = $inputdata['sales_channel'];

        $result = $overheads->copy_costing($name, $id, $sales_channel);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function sides_delete_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new CostingApi_model;
        $userId = $inputdata['userId'];
        $data = $inputdata['data'];

        $result = $overheads->sides_delete($userId, $data);
        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
