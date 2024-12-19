<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;
use net\authorize\api\contract\v1\EmvTagType;

class Dashboard extends RestController{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $this->load->model('api/MarieLogin_model');
        $this->load->model('api/Dashboard_model');
        $this->load->library('Authorization_Token');
    }

    public function email_verification_post(){

        $inputdata = json_decode(file_get_contents("php://input"),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }
        $login_model = new MarieLogin_model;

        $restaurant_name = $inputdata['restaurantName'];
        $email = $inputdata['email'];
        $restaurant_id = $inputdata['userId'];

        $forgot =$login_model->random_code_generator();
        // $restaurant_name =$login_model->get_restaurant_Name($restaurant_id);

        $result = sendMail($restaurant_name, $email, $forgot);
    
        if($result){
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'key' => $forgot
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'status' => 500,
                'message' => 'Email not sent'
            ],RestController::HTTP_BAD_REQUEST);
        }
    }

    public function email_verify_post(){
        $inputdata = json_decode(file_get_contents("php://input"),true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }

        if(!empty($inputdata)){
            $restaurant_id = $inputdata['userId'];
            $result = $this->Dashboard_model->verify_status_update($restaurant_id);
            if($result){
                $this->response([
                    'status' => 200,
                    'message' => 'Success',
                    'verificationStatus' => '1',
                    'email' => $result[0]['client_email'] 
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'status' => 500,
                    'message' => 'Email expired'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
        
    }

    public function findings_post()
    {
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $overheads = new Dashboard_model;

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $r_id = $inputdata['userId'];
        $name = $inputdata['name'];
        $salesChannel = $inputdata['salesChannel'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];

        $result = $overheads->findings($name, $r_id, $salesChannel, $year, $month);

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