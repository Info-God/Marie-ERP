<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class Labour extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/LabourApi_model');
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

    public function store_setup_post()
    {
        $labour_model = new LabourApi_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $data = $inputdata['data'];

        $result = $labour_model->save_traceable($id, $data);
        $result = $labour_model->save_leave_policy($id, $data);

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

    public function labour_activity_post()
    {
        $labour_model = new LabourApi_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $fulltime = $inputdata['fulltime'];
        $foreigner = $inputdata['foreigner'];

        $data = $labour_model->activity($id, $fulltime, $foreigner);

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

    public function save_labour_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $data['name'] = $inputdata['name'];
        $data['fulltime'] = $inputdata['fulltime'];
        $data['foreigner'] = $inputdata['foreigner'];
        $data['salary'] = $inputdata['salary'];
        $data['productivity'] = $inputdata['productivity'];
        $data['traceable'] = $inputdata['traceable'];
        $data['activity'] = $inputdata['activity'];
        $data['restDays'] = $inputdata['restDays'][0];

        $result = $labour_model->insert_labour($id, $data);

        if ($result['insert_id'] || $result) {
            unset($result['insert_id']);
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $result,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Incorrect Password or Email'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function get_labour_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $employee_id = $inputdata['employeeId'];

        $data = $labour_model->fetch_labour($id, $employee_id);

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

    public function labour_list_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];

        $data = $labour_model->fetch_labour_list($id);

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

    public function fetch_setup_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];

        $data['traceable'] = $labour_model->fetch_traceable($id);
        $data['leave'] = $labour_model->fetch_leave($id);

        if ($data['traceable'] && $data['leave']) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function delete_labour_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];

        $data = $labour_model->delete_labour($id, $e_id);

        if ($data) {
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

    public function edit_labour_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $data['name'] = $inputdata['name'];
        $data['fulltime'] = $inputdata['fulltime'];
        $data['foreigner'] = $inputdata['foreigner'];
        $data['salary'] = $inputdata['salary'];
        $data['productivity'] = $inputdata['productivity'];
        $data['activity'] = $inputdata['activity'];
        $data['employeeId'] = $inputdata['employeeId'];
        $data['change'] = $inputdata['change'];
        $data['restDays'] = $inputdata['restDays'][0];

        $result = $labour_model->update_labour($id, $data);

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

    // public function store_working_days_post()
    // {
    //     $labour_model = new LabourApi_model;

    //     $inputdata = json_decode(file_get_contents("php://input"), true);

    //     $id = $inputdata['userId'];
    //     $e_id = $inputdata['employeeId'];
    //     $month = $inputdata['month'];
    //     $year = $inputdata['year'];
    //     $working_days = $inputdata['workingDays'];

    //     $data = $labour_model->insert_working_days($id, $e_id, $year, $month, $working_days);

    //     if ($data) {
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

    // public function fetch_working_days_post()
    // {
    //     $labour_model = new LabourApi_model;

    //     $inputdata = json_decode(file_get_contents("php://input"), true);

    //     $id = $inputdata['userId'];
    //     $e_id = $inputdata['employeeId'];
    //     $year = $inputdata['year'];

    //     $data = $labour_model->fetch_working_days($id, $e_id, $year);

    //     if ($data) {
    //         $this->response([
    //             'status' => 200,
    //             'message' => 'Success',
    //             'data' => $data
    //         ], RestController::HTTP_OK);
    //     } else {
    //         $this->response([
    //             'status' => 500,
    //             'message' => 'Failed'
    //         ], RestController::HTTP_BAD_REQUEST);
    //     }
    // }

    public function overtime_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $day = $inputdata['day'];
        $hrs = $inputdata['hours'];

        $data = $labour_model->overtime($id, $e_id, $year, $month, $day, $hrs);

        if ($data) {
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

    public function leave_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $day = $inputdata['day'];
        $name = $inputdata['name'];

        $data = $labour_model->leave($id, $e_id, $year, $month, $day, $name);

        if ($data) {
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

    public function attendance_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $day = $inputdata['day'];

        $data = $labour_model->attendance($id, $year, $month, $day);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function homeScreen_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $year = $inputdata['year'];
        $month = $inputdata['month'];
        $day = $inputdata['day'];

        $data['calender'] = $labour_model->calender($id, $year, $month);
        $data['summary'] = $labour_model->summary($id, $year, $month, $day);
        $data['setup'] = $labour_model->check_setup($id);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function restDays_post()
    {
        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];
        $year = $inputdata['restDays'][0]['year'];
        $month = $inputdata['restDays'][0]['month'];
        $day = $inputdata['restDays'][0];

        $data['summary'] = $labour_model->restdays($id, $e_id, $year, $month, $day);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function leave_check_post()
    {

        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];
        $e_id = $inputdata['employeeId'];
        $year = $inputdata['restDays'][0]['year'];
        $month = $inputdata['restDays'][0]['month'];
        $day = $inputdata['restDays'][0];

        $data['summary'] = $labour_model->leave_check($year, $month, $day);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }

    public function traceable_post()
    {

        $labour_model = new LabourApi_model;

        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $id = $inputdata['userId'];

        $data = $labour_model->generate_traceable($id);

        if ($data) {
            $this->response([
                'status' => 200,
                'message' => 'Success',
                'data' => $data,
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 500,
                'message' => 'Failed'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}
