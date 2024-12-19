<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class Overheads extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/OverheadsApi_model');
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

        $categoryModal = new OverheadsApi_model;

        $id = $inputdata['userId'];

        $getCategory = $categoryModal->initialization($id);

        if ($getCategory) {
            $this->response([
                'data' => $getCategory,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function storeUserData_post()
    {

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new OverheadsApi_model;

        $userId = $inputdata['userId'];
        $category = $inputdata['category'];
        $report = $inputdata['report'];
        $cycle = $inputdata['cycle'];
        $data = $inputdata['data'];

        // $isUserExist = isUserExist($userId);

        // if (!$isUserExist) {
        //     $this->response([
        //         'message' => 'User was not exist'
        //     ], RestController::HTTP_NOT_FOUND);
        // }
        $storeCategory = $categoryModal->storeData($userId, $category, $report, $cycle, $data);

        if ($storeCategory) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    // public function storeCategories_post()
    // {
    //     $inputdata = json_decode(file_get_contents("php://input"), true);

    //     $categoryModal = new OverheadsApi_model;

    //     $userId = $inputdata['userId'];
    //     $data = $inputdata['categories'];
    //     $year = $inputdata['year'];
    //     $month = $inputdata['month'];

    //     // $isUserExist = isUserExist($userId);

    //     // if (!$isUserExist) {
    //     //     $this->response([
    //     //         'message' => 'User was not exist'
    //     //     ], RestController::HTTP_NOT_FOUND);
    //     // }
    //     $storeCategory = $categoryModal->storeCategories($userId, $data,$year,$month);
    //     if ($storeCategory) {
    //         $this->response([
    //             'status' => 200,
    //             'message' => 'Success',
    //         ], RestController::HTTP_OK);
    //     } else {
    //         $this->response([
    //             'status' => 500,
    //             'message' => 'Failed'
    //         ], RestController::HTTP_BAD_REQUEST);
    //     }
    // }

    // public function fetchStoredCategories_post()
    // {
    //     $inputdata = json_decode(file_get_contents("php://input"), true);

    //     $categoryModal = new OverheadsApi_model;

    //     $userId = $inputdata['userId'];
    //     $year = $inputdata['year'];
    //     $month = $inputdata['month'];

    //     // $isUserExist = isUserExist($userId);

    //     // if (!$isUserExist) {
    //     //     $this->response([
    //     //         'message' => 'User was not exist'
    //     //     ], RestController::HTTP_NOT_FOUND);
    //     // }
    //     $fetchCategory = $categoryModal->fetchCategories($userId,$year,$month);
    //     if ($fetchCategory) {
    //         $this->response([
    //             'status' => 200,
    //             'message' => 'Success',
    //         ], RestController::HTTP_OK);
    //     } else {
    //         $this->response([
    //             'status' => 500,
    //             'message' => 'Failed'
    //         ], RestController::HTTP_BAD_REQUEST);
    //     }
    // }

    public function fetchUserData_post()
    {

        $inputdata = json_decode(file_get_contents("php://input"), true);

        $categoryModal = new OverheadsApi_model;


        $userId = $inputdata['userId'];
        $category = $inputdata['category'];

        // $isUserExist = isUserExist($userId);

        // if (!$isUserExist) {
        //     $this->response([
        //         'message' => 'User was not exist'
        //     ], RestController::HTTP_NOT_FOUND);
        // }
        $fetch = $categoryModal->fetchData($userId, $category);
        if ($fetch) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $fetch,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
