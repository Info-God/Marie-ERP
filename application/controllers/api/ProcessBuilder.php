<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class ProcessBuilder extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/ProcessBuilderApi_model');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
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
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new ProcessBuilderApi_model;

        $id = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        if(!isset($id) || !isset($month) || !isset($year)){
            $this->response([
                'status' => 400,
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data['categories'] = $categoryModal->getCategory();
        $data['description'] = $categoryModal->getDescription();
        $data['activities'] = $categoryModal->getProcessBuilder($id,$month,$year);
        $data['default'] = $categoryModal->getDefault();


        if ($data) {
            $this->response([
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function saving_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new ProcessBuilderApi_model;

        $id = $inputdata['userId'];
        $data = $inputdata['data'];

        if(!isset($id) || !isset($data)){
            $this->response([
                'status' => 400,
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data['activities'] = $categoryModal->saving($id,$data);

        if ($data) {
            $this->response([
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function fetch_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new ProcessBuilderApi_model;

        $id = $inputdata['userId'];

        if(!isset($id)){
            $this->response([
                'status' => 400,
            ], RestController::HTTP_BAD_REQUEST);
        }
        
        $data['processBuilder'] = $categoryModal->fetch($id);
        $data['expenses'] = $categoryModal->expenses($id);
        // $data['categories'] = $categoryModal->getCategory();
        $data['description'] = $categoryModal->getDescription();
        $data['default'] = $categoryModal->getDefault();


        if ($data) {
            $this->response([
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function change_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new ProcessBuilderApi_model;

        $id = $inputdata['userId'];

        $data['processBuilder'] = $categoryModal->change($id);

        if ($data) {
            $this->response([
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
