<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class MarieLogin extends RestController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('api/MarieLogin_model');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    public function index_get()
    {
        echo "hello";
    }

    public function password_creation_post()
    {
        $login_model = new MarieLogin_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);
            $client_email = $inputdata['email'];
            $new_password = $inputdata['new_password'];
            $confirm_password = $inputdata['confirm_password'];
    
        $isuser = $login_model->check_email($client_email);
        if ($isuser) {
            if ($new_password === $confirm_password) {
                $client_password=$login_model->hash_password($new_password);
                $user_data = [
                    'client_password' => $client_password
                ];

                $result = $login_model->create_password($client_email, $user_data);

                if ($result > 0) {
                    $this->response([
                        'status' => 200,
                        'message' => 'success'
                    ], RestController::HTTP_OK);
                } else {
                    $this->response([
                        'status' => 500,
                        'error' => 'Password not created'
                    ], RestController::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response([
                    'status' => 500,
                    'message' => 'Password not same'
                ], RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response([
                'status' => 400,
                'error' => 'User is not valid'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }



    // public function registration_post(){}

    public function login_post()
    {
        $login_model = new MarieLogin_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $email = $inputdata['email'];

        if (!$inputdata['link'] == true) {
            $password = $inputdata['password'];

            $result = $login_model->login_check($email, $password);
        } else {

            $result = $login_model->get_email_id($email);
        }

        if ($result['deactivate'] == false) {
            if ($result['verify'] == true) {

                $data = $login_model->email_verify($email);

                $token_data['id'] =  $result['id'];
                $token_data['name'] = $data['name'];
                $token_data['email'] =  $email;

                $data['token'] = $this->authorization_token->generateToken($token_data);

                if ($data) {
                    $this->response([
                        'status' => 200,
                        'message' => 'Success',
                        'userId' => $result['id'],
                        'clientMail' => $data['email'],
                        'restaurantName' => $data['name'],
                        'verificationStatus' => $data['verification'],
                        'planType' => $data['type'],
                        'token' => $data['token']
                    ], RestController::HTTP_OK);
                }
            } else {
                $this->response([
                    'status' => 401,
                    'message' => 'Incorrect Password or Email'
                ], RestController::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response([
                'status' => 403,
                'message' => 'Account Deactivated'
            ], RestController::HTTP_FORBIDDEN);
        }
    }

    public function forgot_password_post()
    {

        $login_model = new MarieLogin_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        $email = $inputdata['email'];

        $restaurant_id =$login_model->get_restaurant_id($email);

        $isUserExist = isUserExist($restaurant_id);

        // if (!$isUserExist) {
        //     $this->response([
        //         'message' => 'User was not exist'
        //     ], RestController::HTTP_UNAUTHORIZED);
        // }

        $forgot =$login_model->random_code_generator();
        $restaurant_name =$login_model->get_restaurant_Name($restaurant_id);

        $result = send_mail($restaurant_name, $email, $forgot);

        if ($result) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'code' => $forgot
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 400,
                'message' => 'Email not sent'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
