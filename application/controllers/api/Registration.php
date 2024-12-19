<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;
use net\authorize\api\contract\v1\EmvTagType;

class Registration extends RestController{

    public function __construct(){
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $this->load->model('api/RegistrationApi_model');
        $this->load->library('Authorization_Token');
    }

    public function onboarding_post(){
        $inputdata = json_decode(file_get_contents("php://input"),true);

        $result = $this->RegistrationApi_model->restaurant_registration($inputdata);
        if(!empty($result)){
            $token_data['id'] =  $result['userid'];
            $token_data['name']=$result[0]['business_name'];
            $token_data['email'] = $result[0]['client_email'];

            $token = $this->authorization_token->generateToken($token_data);
            $this->response([
                'status' => 200,
                'userId' => $result['userid'],
                'restaurantName' => $result[0]['business_name'],
                'clientMail' => $result[0]['client_email'],
                'planType' => $result['plan_type'],
                'planCost' => $result['plan_cost'],
                'message' => 'Restaurant registered successfully',
                'token' => $token
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'status' => 500,
                'message' => 'Registration Failed'
            ],RestController::HTTP_BAD_REQUEST);
        }
    }

    
    public function email_validation_post(){

        $inputdata = json_decode(file_get_contents("php://input"),true);
        if(!empty($inputdata)){
            $email = $inputdata['email'];
            $result =  $this->RegistrationApi_model->check_email_exist($email);
            if(!$result){
                $this->response([
                    'status' => 200,
                    'message' => 'Success'
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'status' => 500,
                    'message' => 'Email already exist'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }


    public function commonForAll_post(){
        $database = new RegistrationApi_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        $decodedToken = $this->authorization_token->validateToken();

        if ($decodedToken['status'] == false){
            $this->response([
                'status' => 400,
                'message' => $decodedToken['message']
            ], RestController::HTTP_BAD_REQUEST);
        }    

        if(!empty($inputdata)){
    
            $userId = $inputdata['userId'];
            $isUserExist = isUserExist($userId);
    
            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }
    
            $result = $database->commonForAll($userId);
    
            if($result){
                $this->response([
                    'userId' => $userId,
                    'Data' => $result
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'No data'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }




}