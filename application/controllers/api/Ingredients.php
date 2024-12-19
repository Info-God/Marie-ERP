<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';



use chriskacerguis\RestServer\RestController;

class Ingredients extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/Ingredient_model');
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

    public function ingredients_post()
    {
        $ingredient_model = new Ingredient_model;

        $data=$ingredient_model->get_ingredients();
        if($data){
            $this->response([
                'status' => 200,
                'Ingredients' => $data[0],
                'Images' => $data[1]
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'message' => 'Bad Request'
            ],RestController::HTTP_BAD_REQUEST);
        }
    }

    public function ingredients_list_post()
    {

        $ingredient_model = new Ingredient_model;

        $inputdata = json_decode(file_get_contents("php://input"),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        $userId = $inputdata['userId'];
        $category = $inputdata['category'];

        if(empty($userId || $category)){
            $this->response([
                'message' => 'Input was empty'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $isUserExist = isUserExist($userId);

        if(!$isUserExist){
            $this->response([
                'message' => 'User was not exist'
            ],RestController::HTTP_BAD_REQUEST);
        }
        
        $result = $ingredient_model->get_ingredients_list($userId,$category);
        if($result){
            $this->response([
                'userId' => $userId,
                'category' => $category,
                'data' => $result
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'message' => 'Bad Request'
            ],RestController::HTTP_BAD_REQUEST);
        }
    }

    public function storeIngredients_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents("php://input"),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(empty($inputdata)){
            $this->response([
                'message' => 'Input field was empty'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $userId = $inputdata['userId'];

        $isUserExist = isUserExist($userId);

        if(!$isUserExist){
            $this->response([
                'message' => 'User was not exist'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $category = $inputdata['category'];

        $data = $inputdata['data'];

        $result = $ingredient_model->storeIngredients($userId,$category,$data);

        if($result){
            $this->response([
                'message' => 'Data stored'
            ],RestController::HTTP_OK);
        }else{
            $this->response([
                'message' => 'Something went wrong'
            ],RestController::HTTP_BAD_REQUEST);
        }

    }

    // public function stocks_post(){
    //     $ingredient_model = new Ingredient_model;
    //     $inputdata = json_decode(file_get_contents("php://input"),true);

    //     if(empty($inputdata)){
    //         $this->response([
    //             'message' => 'Input field was empty'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $userId = $inputdata['userId'];

    //     $isUserExist = isUserExist($userId);

    //     if(!$isUserExist){
    //         $this->response([

    //             'message' => 'User was not exist'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $category = $inputdata['category'];
        
    //     $item = $inputdata['ingredient'];

    //     $subtype = $inputdata['subtype'];

    //     $data = $inputdata['data'];

    //     $result = $ingredient_model->storeStock($userId,$category,$item,$subtype,$data);

    //     if(!empty($result)){
    //         $this->response([
    //             'message' => 'success'
    //         ],RestController::HTTP_OK);
    //     }else{
    //         $this->response([
    //             'message' => 'Something went wrong'
    //         ],RestController::HTTP_BAD_REQUEST); 
    //     }

    // }

    public function selectingStock_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents("php://input"),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(empty($inputdata)){
            $this->response([
                'message' => 'Input field was empty'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $userId = $inputdata['userId'];

        $isUserExist = isUserExist($userId);

        if(!$isUserExist){
            $this->response([
                'message' => 'User was not exist'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $result = $ingredient_model->selectingStock($userId);

        if(!empty($result)){
            $this->response([
                'userId' => $userId,
                'selectedData' => $result
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'message' => 'Empty data'
            ],RestController::HTTP_OK);
        }
    }

    // public function purchaseStock_post(){
    //     $ingredient_model = new Ingredient_model;
    //     $inputdata = json_decode(file_get_contents("php://input"),true);

    //     if(empty($inputdata)){
    //         $this->response([
    //             'message' => 'Input field was empty'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $userId = $inputdata['userId'];

    //     $isUserExist = isUserExist($userId);

    //     if(!$isUserExist){
    //         $this->response([
    //             'message' => 'User was not exist'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $category = $inputdata['category'];
        
    //     $item = $inputdata['ingredient'];

    //     $subtype = $inputdata['subtype'];

    //     $data = $inputdata['data'];

    //     $result = $ingredient_model->purchaseStock($userId,$category,$item,$subtype,$data);

    //     if(!empty($result)){
    //         $this->response([
    //             'message' => 'success'
    //         ],RestController::HTTP_OK);
    //     }else{
    //         $this->response([
    //             'message' => 'Something went wrong'
    //         ],RestController::HTTP_BAD_REQUEST); 
    //     }
    // }

    // public function purchaseList_post(){

    //     $ingredient_model = new Ingredient_model;
    //     $inputdata = json_decode(file_get_contents("php://input"),true);

    //     if(empty($inputdata)){
    //         $this->response([
    //             'message' => 'Input field was empty'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $userId = $inputdata['userId'];

    //     $isUserExist = isUserExist($userId);

    //     if(!$isUserExist){
    //         $this->response([
    //             'message' => 'User was not exist'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $category = $inputdata['category'];
        
    //     $item = $inputdata['ingredient'];

    //     $subtype = $inputdata['subtype'];

    //     $result = $ingredient_model->purchaseList($userId,$category,$item,$subtype);

    //     if(!empty($result)){
    //         $this->response([
    //             "userId" => $userId,
    //             "category" => $category,
    //             "item" => $item,
    //             "subtype" => $subtype,
    //             'data' => $result
    //         ],RestController::HTTP_OK);
    //     }else{
    //         $this->response([
    //             'message' => 'Something went wrong'
    //         ],RestController::HTTP_BAD_REQUEST); 
    //     }
    // }

    // public function stockList_post(){

    //     $ingredient_model = new Ingredient_model;
    //     $inputdata = json_decode(file_get_contents("php://input"),true);

    //     if(empty($inputdata)){
    //         $this->response([
    //             'message' => 'Input field was empty'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $userId = $inputdata['userId'];

    //     $isUserExist = isUserExist($userId);

    //     if(!$isUserExist){
    //         $this->response([
    //             'message' => 'User was not exist'
    //         ],RestController::HTTP_BAD_REQUEST);
    //     }

    //     $category = $inputdata['category'];
        
    //     $item = $inputdata['ingredient'];

    //     $subtype = $inputdata['subtype'];

    //     $result = $ingredient_model->stockList($userId,$category,$item,$subtype);

    
    //     $this->response([
    //             "userId" => $userId,
    //             "category" => $category,
    //             "item" => $item,
    //             "subtype" => $subtype,
    //             'data' => $result
    //         ],RestController::HTTP_OK);
    // }
    public function selectingIngredients_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents("php://input"),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(empty($inputdata)){
            $this->response([
                'message' => 'Input field was empty'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $userId = $inputdata['userId'];
        $month = $inputdata['month'];
        $year = $inputdata['year'];

        $isUserExist = isUserExist($userId);

        if(!$isUserExist){
            $this->response([
                'message' => 'User was not exist'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $result = $ingredient_model->selectingIngredients_costing($userId,$month,$year);

        if(!empty($result)){
            $this->response([
                'userId' => $userId,
                'selectedData' => $result
            ],RestController::HTTP_OK);
        }
        else{
            $this->response([
                'message' => 'Empty data'
            ],RestController::HTTP_OK);
        }
        
    }

    public function createStock_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){

            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);

            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }

            $result = $ingredient_model->createStock($data);

            if(!empty($result)){
                $this->response(RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Edit went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function editStock_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){

        $data = $inputdata;
        $isUserExist = isUserExist($data['userId']);

        if(!$isUserExist){
            $this->response([
                'message' => 'User was not exist'
            ],RestController::HTTP_BAD_REQUEST);
        }

        $result = $ingredient_model->editStock($data);

        if(!empty($result)){
            $this->response(RestController::HTTP_OK);
        }
        else{
            $this->response([
                'message' => 'Edit went wrong'
            ],RestController::HTTP_BAD_REQUEST);
        }
        }
    }

    public function deleteStock_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){
    
            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);
    
            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }
    
            $result = $ingredient_model->deleteStock($data);
    
            if(!empty($result)){
                $this->response(RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Edit went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function stockLists_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){
    
            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);
    
            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }
    
            $result = $ingredient_model->stockLists($data);
    
            if(!empty($result)){
                $this->response([
                    'userId' => $data['userId'],
                    'category' => $data['category'],
                    'item' => $data['item'],
                    'stocks' => $result
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Edit went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function addIngredients_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){

            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);

            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }

            $result = $ingredient_model->createIngredient($data);

            if($result){
                $this->response([
                    'message' => $result
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Add ingredients went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function download_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){

            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);

            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }

            $result = $ingredient_model->download($data);

            if($result){
                $this->response([
                    'data' => $result
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Add ingredients went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function downloadMobile_post()
    {
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'), true);
        if (!empty($inputdata)) {

            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);

            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }

            $result = $ingredient_model->download_mobile($data);

            if ($result) {
                $this->response([
                    'data' => $result
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'Download went wrong'
                ], RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function dragDrop_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents('php://input'),true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if(!empty($inputdata)){

            $data = $inputdata;
            $isUserExist = isUserExist($data['userId']);

            if(!$isUserExist){
                $this->response([
                    'message' => 'User was not exist'
                ],RestController::HTTP_BAD_REQUEST);
            }

            $result = $ingredient_model->dragDrop($data);

            if($result){
                $this->response([
                    'data' => $result
                ],RestController::HTTP_OK);
            }
            else{
                $this->response([
                    'message' => 'Add ingredients went wrong'
                ],RestController::HTTP_BAD_REQUEST);
            }
        }
    }

    public function editIngredients_post(){
        $ingredient_model = new Ingredient_model;
        $inputdata = json_decode(file_get_contents("php://input"), true);

        // $decodedToken = $this->authorization_token->validateToken();

        // if ($decodedToken['status'] == false){
        //     $this->response([
        //         'status' => 400,
        //         'message' => $decodedToken['message']
        //     ], RestController::HTTP_BAD_REQUEST);
        // }

        if (empty($inputdata)) {
            $this->response([
                'message' => 'Input field was empty'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $userId = $inputdata['userId'];

        $isUserExist = isUserExist($userId);

        if (!$isUserExist) {
            $this->response([
                'message' => 'User was not exist'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $data = $inputdata['data'];

        $result = $ingredient_model->editIngredients($userId, $data);

        if ($result) {
            $this->response([
                'message' => 'Edit Ok'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'message' => 'Already exists'
            ], RestController::HTTP_BAD_REQUEST);
        }
    }
}