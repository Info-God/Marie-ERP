<?php

use Twilio\Deserialize;

defined('BASEPATH') or exit('No direct script access allowed');

class Ingredient_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_ingredients()
    {

        $result = $this->db->get(db_prefix() . 'restaurant_ingredients')->result_array();

        foreach ($result as  $values) {
            $ingredientName[] = $values["ingredient"];
            $ingredientImage[] = $values["image"];
        }
        $data[0] = $ingredientName;
        $data[1] = $ingredientImage;
        return $data;
    }

    public function get_ingredients_list($userId, $category)
    {


        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $this->db->select('tbluser_ingredients.ingredients');
        $this->db->from('tbluser_ingredients');
        $this->db->where('tbluser_ingredients.restaurant', $userId);
        $this->db->where('tbluser_ingredients.category', $category_id);
        $isDataExist = $this->db->get()->result_array();


        if (!empty($isDataExist)) {

            $ingredientsData = $isDataExist[0]['ingredients'];



            $unserializedIngredientsData = unserialize($ingredientsData);

            //  print_r($unserializedIngredientsData);die();

            $trueValue = [];
            $falseValue = [];
            foreach ($unserializedIngredientsData as $data1) {
                if ($data1['isChecked'] == true) {
                    $trueValue[] = $data1;
                } else {
                    $falseValue[] = $data1;
                }
            }

            $final = array_merge($trueValue, $falseValue);



            $data['categoryListing'][$category]  = $final;

            $measurement = $this->db->get(db_prefix() . 'ingredient_measurement')->result_array();

            $i = 0;
            while (count($measurement) > $i) {
                $measure[$i] = $measurement[$i];
                $i++;
            }

            $data['measurements'] = $measure;


            return $data;
        }

        $ingredients = [];
        $organizedIngredients = [];
        $i = 0;
        // $this->db->select('tblingredients_listing.ingredient_id,tblingredient_categories.category,tblingredients_listing.ingredient_name,tblingredients_listing.units_of_measure');
        // $this->db->from('tblingredients_listing');
        // $this->db->join('tblingredient_categories','tblingredient_categories.category_id = tblingredients_listing.category');

        $this->db->select('*');
        $this->db->from('tblingredients_listing');
        $this->db->where('category', $category_id);
        $ingredients = $this->db->get()->result_array();


        $categories = $this->db->get('tblingredient_categories')->result_array();
        while (count($categories) > $i) {
            $list_categories[$i] = $categories[$i]['category'];
            $i++;
        }

        $measurement = $this->db->get(db_prefix() . 'ingredient_measurement')->result_array();


        $i = 0;
        while (count($measurement) > $i) {
            $measure[$i] = $measurement[$i];
            $i++;
        }

        $data['measurements'] = $measure;


        foreach ($ingredients as $ingredient) {
            $ingredientId = $ingredient['ingredient_id'];
            // $category = $ingredient['category'];
            $ingredientName = $ingredient['ingredient_name'];
            $units_of_measure = $ingredient['units_of_measure'];

            // Create a new ingredient entry
            $newIngredient = [
                'ingredientId' => $ingredientId,
                'ingredient' => $ingredientName, // Convert to lowercase for consistency
                'unitsOfMeasure' => $units_of_measure,
            ];

            // Check if the category already exists in the organized array
            if (array_key_exists($category, $organizedIngredients)) {
                // If the category exists, add the new ingredient to it
                $organizedIngredients[$category][] = $newIngredient;
            } else {
                // If the category doesn't exist, create a new category entry
                $organizedIngredients[$category] = [$newIngredient];
            }
        }

        // Convert the organizedIngredients array to JSON
        $data['categoryListing'] = $organizedIngredients;

        return $data;
    }

    public function storeIngredients($userId, $category, $data)
    {


        $data = serialize($data);
        // print_r(unserialize($data));die();
        $datecreated = date('Y-m-d H:i:s');

        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $insertionData = [
            'restaurant' => $userId,
            'category' => $category_id,
            'ingredients' => $data,
            'datecreated' => $datecreated
        ];

        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $result = $this->db->get()->result();

        if ($result) {
            $this->db->where('category', $category_id);
            $this->db->where('restaurant', $userId);
            $this->db->update('tbluser_ingredients', $insertionData);
            return true;
        }

        $this->db->insert(db_prefix() . 'user_ingredients', $insertionData);
        return true;
    }

    public function selectingStock($userId)
    {
        $i = 0;
        $count = 0;
        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $stockDatas = $this->db->get()->result_array();

        if (!empty($stockDatas)) {
            foreach ($stockDatas as $stock) {
                $category = $stock['category'];

                $this->db->select('tblingredient_categories.category');
                $this->db->from('tblingredient_categories');
                $this->db->where('category_id', $category);
                $category = $this->db->get()->result();
                $category = $category[0]->category;
                $ingredients = unserialize($stock['ingredients']);
                foreach ($ingredients as $ingredient) {
                    if ($ingredient['isChecked'] == false) {
                        continue;
                    }
                    $finalIngredients[] = $ingredient;
                }

                $listingIngredients[$i]['category'] = $category;
                $listingIngredients[$i]['ingredients'] = $finalIngredients;
                unset($finalIngredients);
                $i++;
            }

            return $listingIngredients;
        } else {
            return false;
        }
    }

    public function selectingIngredients_costing($userId)
    {

        $i = 0;
        $count = 0;
        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $stockDatas = $this->db->get()->result_array();
        // print_r($stockDatas);die();
        if (!empty($stockDatas)) {
            foreach ($stockDatas as $stock) {
                $category = $stock['category'];
                $ingredients = unserialize($stock['ingredients']);
                foreach ($ingredients as $ingredient) {
                    if ($ingredient['isChecked'] == false) {
                        continue;
                    }

                    // $item = $ingredient['ingredient'];
                    // // $subtype = $ingredient['subtype'];
                    // //Get ingredient id
                    // $this->db->select('tblingredients_listing.ingredient_id');
                    // $this->db->from(db_prefix() . 'ingredients_listing');
                    // $this->db->where('ingredient_name', $item);
                    // $ingredient_id = $this->db->get()->result();
                    // $ingredient_id = $ingredient_id[0]->ingredient_id;
                    $ingredient_id = $ingredient['ingredientId'];

                    // if($item=="Mango Pulp"){
                    //     print_r($ingredient_id);die();
                    // }

                    //Get category_id
                    $this->db->select('tblingredient_categories.category');
                    $this->db->from('tblingredient_categories');
                    $this->db->where('category_id', $category);
                    $category_id = $this->db->get()->result();
                    $Category = $category_id[0]->category;

                    $Today = new DateTime('today');
                    $Today = $Today->format('Y-m-d');

                    $querys = $this->db->select('*')
                    ->from('tblingredient_stock_cards')
                    ->where('restaurant', $userId)
                    ->where('category', $category)
                    ->where('item', $ingredient_id)
                    ->where('pricePerUnit !=', 0)
                    ->where('datecreated <=', $Today)
                    ->order_by('datecreated', 'DESC')
                    // ->limit(4)
                    ->get()
                    ->result_array();

                    $sums = [];

                    if ($querys) {
                        // print_r($querys);die;
                        $query[0]=$querys[0];
                        $query[1]=$querys[1];
                        $query[2]=$querys[2];
                        $query[3]=$querys[3];
                        $result = [];

                        foreach ($query as $singleData) {
                            if (isset($singleData['pricePerUnit']) && $singleData['pricePerUnit'] !== "" && $singleData['pricePerUnit'] !== 0) {
                                $result[] = $singleData['pricePerUnit'];
                            }
                        }


                        $count = count($result);
                        $sum = array_sum($result);
                        unset($result);


                        // settype($sum, 'Integer');
                        if ($sum != 0 && $count != 0) {
                            $unit_price = $sum / $count;
                            $unit_price = number_format((float)$unit_price, 2, '.', '');
                            // $unit_price = round($unit_price, 2);
                        } else {
                            $unit_price = 0; // Set a default value or handle it according to your logic
                            // break 1;
                        }
                        $ingredient['unit_price'] = number_format($unit_price,2);
                    } else {
                        $ingredient['unit_price'] = 0;
                        // unset($ingredient);
                    }

                    // if($ingredient_id=="80"){
                    //     print_r($ingredient);die();
                    // }
                    $finalIngredients[] = $ingredient;
                }


                $listingIngredients[$i]['category'] = $Category;
                $listingIngredients[$i]['ingredients'] = $finalIngredients;

                unset($finalIngredients);
                $i++;
            }

            return $listingIngredients;
        } else {
            return false;
        }
    }

    public function createStock($data)
    {
        $userId = $data['userId'];
        $category = $data['category'];
        $item = $data['item'];
        $ingredientData = $data['ingredientsData'];

        // print_r($item);die();


        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        // Check stock is created
        $this->db->select('*');
        $this->db->from(db_prefix() . 'ingredient_stock_cards');
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $this->db->where('item', $item);
        $this->db->order_by('datecreated', 'desc');
        $this->db->limit(1);
        $isStockCreated = $this->db->get()->result_array();

        $datetime = date('Y-m-d H:i:s');

        if (empty($isStockCreated)) {
            $insertStock = [
                'restaurant' => $userId,
                'category' => $category_id,
                'item' => $item,
                'stockCount' => $ingredientData['stockCount'],
                'planToBuy' => $ingredientData['planToBuy'],
                'bought' => $ingredientData['bought'],
                'pricePerUnit' => $ingredientData['pricePerUnit'],
                'closingStock' => $ingredientData['closingStock'],
                'consumption' => $ingredientData['consumption'],
                'datecreated' => $ingredientData['date'],
                'datetime' => $datetime
            ];

            $this->db->insert(db_prefix() . 'ingredient_stock_cards', $insertStock);
            return $this->db->insert_id();
        }

        $closing_stock = $ingredientData['stockCount'];
        $last_stock = $isStockCreated[0]['stockCount'];
        $purchase_count = $isStockCreated[0]['bought'];

        //data type conversion
        floatval($purchase_count);
        floatval($closing_stock);
        floatval($last_stock);

        $consumption = ($last_stock + $purchase_count) - $closing_stock;

        $updateData = [
            'consumption' => $consumption,
            'closingStock' => $closing_stock
        ];

        $this->db->where('datecreated', $isStockCreated[0]['datecreated']);
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $this->db->where('item', $item);
        $this->db->update('tblingredient_stock_cards', $updateData);

        $insertStock = [
            'restaurant' => $userId,
            'category' => $category_id,
            'item' => $item,
            'stockCount' => $ingredientData['stockCount'],
            'planToBuy' => $ingredientData['planToBuy'],
            'bought' => $ingredientData['bought'],
            'pricePerUnit' => $ingredientData['pricePerUnit'],
            'closingStock' => $ingredientData['closingStock'],
            'consumption' => $ingredientData['consumption'],
            'datecreated' => $ingredientData['date']
        ];

        $this->db->insert(db_prefix() . 'ingredient_stock_cards', $insertStock);
        return $this->db->insert_id();
    }

    public function editStock($data)
    {
        $userId = $data['userId'];
        $id = $data['stockId'];
        $ingredientData = $data['ingredientsData'];

        $this->db->where('id', $id);
        $this->db->update('tblingredient_stock_cards', $ingredientData);

        return true;
    }

    public function deleteStock($data)
    {
        $userId = $data['userId'];
        $id = $data['stockId'];

        $this->db->where('id', $id);
        $this->db->delete('tblingredient_stock_cards');

        return true;
    }

    public function stockLists($data)
    {
        $i = 0;
        $output = [];
        $userId = $data['userId'];
        $category = $data['category'];
        $item = $data['item'];

        // //Get ingredient id 
        // $this->db->select('*');
        // $this->db->from(db_prefix().'ingredients_listing');
        // $this->db->where('ingredient_name',$item);
        // $ingredient_id = $this->db->get()->result();

        // print_r($ingredient_id);die();
        // $unit= $ingredient_id[0]->units_of_measure;
        // $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $stocks = $this->db->select('*')
            ->from('tblingredient_stock_cards')
            ->where('restaurant', $userId)
            ->where('category', $category_id)
            ->where('item', $item)
            ->order_by('datecreated','desc')
            ->get()->result_array();


        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $result = $this->db->get()->result_array();

        $result = unserialize($result[0]['ingredients']);

        // print_r($result);die();

        foreach ($result as $value) {
            if ($value["ingredientId"] == $item) {
                $unit = $value["measurement"];
            }
        }


        foreach ($stocks as $stock) {

            $output[$i] = $stock;
            $output[$i]['unit'] = $unit;
            $i++;
        }

        return $output;
    }

    public function createIngredient($data)
    {
        $ingredientsData1 = [];
        $i = 1;
        $userId = $data['userId'];
        $category = $data['category'];
        $ingredientsData = $data['ingredientsData'];

        $new = $ingredientsData[0];

        $ingredientsData[0]['ingredient'] = ucwords(strtolower($ingredientsData[0]['ingredient']));
        $new[0]['ingredient'] = ucwords(strtolower($ingredientsData[0]['ingredient']));

        $datecreated = date('Y-m-d H:i:s');

        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $result = $this->db->get()->result_array();

        foreach ($result as $key => $Value) {
            $allData = unserialize($Value['ingredients']);

            foreach ($allData as $KEY => $value) {

                if ($value['ingredient'] == $new[0]['ingredient']) {
                    // if (stripos($value['ingredient'], $new[0]['ingredient']) !== false) {
                    return $ingredientsData[0]['ingredient'] . ' already exist';
                }
            }
        }

        $this->db->select('*');
        $this->db->where('category', $category_id);
        $checkData = $this->db->get('tblingredients_listing')->result_array();

        // $dataCount = count($checkData);
        // $randomNumber = rand($dataCount, $dataCount + $i);

        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $result1 = $this->db->get()->result_array();

        if ($result1) {

            $ingredientsData1 = unserialize($result1[0]['ingredients']);

            // foreach ($ingredientsData1 as $data) {
            //     if ($data['ingredient'] == $ingredientsData[0]['ingredient']) {
            //         return $ingredientsData[0]['ingredient'] . ' already exist';
            //     }
            // }

            // Extract ingredientId values into a separate array
            $ingredientIds = [];
            foreach ($ingredientsData1 as $single) {
                $ingredientIds[] = $single['ingredientId'];
            }

            // Sort the ingredientId values in ascending order
            sort($ingredientIds);

            $last=end($ingredientIds);

            $lastIngredientId = $last + 1;

            settype($lastIngredientId, 'string');

            $ingredientsData[0]['ingredientId'] = $lastIngredientId;

            $insertionData = [
                'ingredients' => serialize($ingredientsData),
            ];

            $this->db->where('category', $category_id);
            $this->db->where('restaurant', $userId);
            $this->db->update('tbluser_ingredients', $insertionData);

            if ($this->db->affected_rows() > 0) {
                return $ingredientsData[0]['ingredient'] . ' created';
            }
            return false;
        }

        $ingredientIds = [];
        foreach ($checkData as $single) {
            $ingredientIds[] = $single['ingredient_id'];
        }

        // Sort the ingredientId values in ascending order
        sort($ingredientIds);

        // Fetch the last number in the sorted array
        $lastIngredientId = end($ingredientIds) + 1;
        // print_r($ingredientIds);print_r($lastIngredientId);die();

        settype($lastIngredientId, 'string');

        $ingredientsData[0]['ingredientId'] = $lastIngredientId;


        // gettype($randomNumber);die();

        // $ingredientsData[0]['ingredientId'] = $randomNumber;

        // settype($ingredientsData[0]['ingredientId'],"String");

        $insertionData = [
            'restaurant' => $userId,
            'category' => $category_id,
            'ingredients' => serialize($ingredientsData),
            'datecreated' => $datecreated
        ];

        $result = $this->db->insert(db_prefix() . 'user_ingredients', $insertionData);

        if ($result) {
            return $ingredientsData[0]['ingredient'] . ' created';
        }
        return false;
    }

    public function download($data)
    {

        $output = [];
        $userId = $data['userId'];
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];

        $stocks = $this->db->select('*')
            ->from('tblingredient_stock_cards')
            ->where('restaurant', $userId)
            ->where('datecreated >=', $from_date) // Date greater than or equal to from_date
            ->where('datecreated <=', $to_date)   // Date less than or equal to to_date
            ->order_by('category', 'ASC')
            ->order_by('item', 'ASC')
            ->get()
            ->result_array();

        foreach ($stocks as $stock) {
            $category[$stock["category"]][] = $stock;
        }

        $output['fromDate'] = $from_date;
        $output['toDate'] = $to_date;

        foreach ($category as $key => $value) {
            $this->db->select('tblingredient_categories.category');
            $this->db->from('tblingredient_categories');
            $this->db->where('category_id', $key);
            $Category = $this->db->get()->result();
            $Category = $Category[0]->category;

            foreach ($value as $Key => $Value) {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'user_ingredients');
                $this->db->where('restaurant', $userId);
                $this->db->where('category', $Value['category']);
                $Ingredient = $this->db->get()->result();
                $ingredients = $Ingredient[0]->ingredients;
                $ingredients=unserialize($ingredients);

                foreach ($ingredients as $KEY => $VALUE) {
                    if($Value['item']==$VALUE['ingredientId']){
                        $ingredient=$VALUE['ingredient'];
                    }
                }

                $Value['item'] = $ingredient;
                $Value['category'] = $Category;

                $output['category'][$Category]['stock'][$ingredient]['stocks'][] = $Value;
            }
        }

        return $output;
    }

    public function download_mobile($data)
    {

        $output = [];
        $userId = $data['userId'];
        $from_date = $data['from_date'];
        $to_date = $data['to_date'];

        $stocks = $this->db->select('*')
            ->from('tblingredient_stock_cards')
            ->where('restaurant', $userId)
            ->where('datecreated >=', $from_date) // Date greater than or equal to from_date
            ->where('datecreated <=', $to_date)   // Date less than or equal to to_date
            ->order_by('category', 'ASC')
            ->order_by('item', 'ASC')
            ->get()
            ->result_array();

        foreach ($stocks as $stock) {
            $category[$stock["category"]][] = $stock;
        }

        $output['fromDate'] = $from_date;
        $output['toDate'] = $to_date;

        foreach ($category as $key => $value) {
            $this->db->select('tblingredient_categories.category');
            $this->db->from('tblingredient_categories');
            $this->db->where('category_id', $key);
            $Category = $this->db->get()->result();
            $Category = $Category[0]->category;
            $i=0;

            foreach ($value as $Key => $Value) {
                $this->db->select('*');
                $this->db->from(db_prefix() . 'user_ingredients');
                $this->db->where('restaurant', $userId);
                $this->db->where('category', $Value['category']);
                $Ingredient = $this->db->get()->result();
                $ingredients = $Ingredient[0]->ingredients;
                $ingredients = unserialize($ingredients);

                foreach ($ingredients as $KEY => $VALUE) {
                    if ($Value['item'] == $VALUE['ingredientId']) {
                        $ingredient = $VALUE['ingredient'];
                    }
                }

                $Value['item'] = $ingredient;
                $Value['category'] = $Category;

                $output['category'][$Category]['stocks'][$i]['subCategory'] = $ingredient;
                $output['category'][$Category]['stocks'][$i]['datas'][] = $Value;
                $i++;
            }
        }

        return $output;
    }

    public function dragDrop($data)
    {
        $userId = $data['userId'];
        $category = $data['category'];
        $trueData = $data['data'];
        $falseData = [];

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category', $category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $this->db->select('tbluser_ingredients.ingredients');
        $this->db->from('tbluser_ingredients');
        $this->db->where('tbluser_ingredients.restaurant', $userId);
        $this->db->where('tbluser_ingredients.category', $category_id);
        $isDataExist = $this->db->get()->result_array();

        $ingredientsData = $isDataExist[0]['ingredients'];
        $unserializedIngredientsData = unserialize($ingredientsData);

        foreach ($unserializedIngredientsData as $value) {
            if ($value['isChecked'] == false) {
                $falseData[] = $value;
            }
        }

        $ingredients = array_merge($trueData, $falseData);

        $insertStock = [
            'ingredients' => serialize($ingredients)
        ];
        $this->db->where('restaurant', $userId);
        $this->db->where('category', $category_id);
        $output = $this->db->update(db_prefix() . 'user_ingredients', $insertStock);

        return $output;
    }

    public function editIngredients($userId, $data)
    {

        $data[0]['ingredient'] = ucwords(strtolower($data[0]['ingredient']));

        $this->db->select('*');
        $this->db->from(db_prefix() . 'user_ingredients');
        $this->db->where('restaurant', $userId);
        $result = $this->db->get()->result_array();

        foreach ($result as $key => $Value) {
            $allData = unserialize($Value['ingredients']);

            foreach ($allData as $KEY => $value) {

                if ($value['ingredient'] == $data[0]['ingredient']) {
                    if ($value["measurement"] == $data[0]["measurement"]) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
