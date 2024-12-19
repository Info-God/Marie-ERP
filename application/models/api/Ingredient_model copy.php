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

    public function get_ingredients_list($userId,$category)
    {

        $this->db->select('tbluser_ingredients.ingredients');
        $this->db->from('tbluser_ingredients');
        $this->db->where('tbluser_ingredients.restaurant',$userId);
        $this->db->where('tbluser_ingredients.category',$category);
        $isDataExist = $this->db->get()->result_array();
        if(!empty($isDataExist)){

            $ingredientsData = $isDataExist[0]['ingredients'];
            
            $data['categoryListing'][$category]  = unserialize($ingredientsData);

            $purchase_cycle = $this->db->get(db_prefix(). 'ingredient_purchase_cycle')->result_array();
            $i = 0;
            while(count($purchase_cycle) > $i){
                $purchase[$i] = $purchase_cycle[$i] ;
                $i++;
            }
    
            $data['purchase_cycle'] = $purchase;
    
            $measurement = $this->db->get(db_prefix(). 'ingredient_measurement')->result_array();
            $i = 0;
            while(count($measurement) > $i){
                $measure[$i] = $measurement[$i] ;
                $i++;
            }
    
            $data['measurements'] = $measure;

            return $data;
        }

        $ingredients = [];
        $organizedIngredients = [];
        $i = 0;
        $this->db->select('tblingredients_listing.ingredient_id,tblingredient_categories.category,tblingredients_listing.ingredient_name,tblingredients_listing.ingredient_subtype');
        $this->db->from('tblingredients_listing');
        $this->db->join('tblingredient_categories','tblingredient_categories.category_id = tblingredients_listing.category');
        $ingredients = $this->db->get()->result_array();

        
        $categories = $this->db->get('tblingredient_categories')->result_array();
        while(count($categories) > $i){
            $list_categories[$i] = $categories[$i]['category'];
            $i++;
        }

        $purchase_cycle = $this->db->get(db_prefix(). 'ingredient_purchase_cycle')->result_array();
        $i = 0;
        while(count($purchase_cycle) > $i){
            $purchase[$i] = $purchase_cycle[$i] ;
            $i++;
        }

        $data['purchase_cycle'] = $purchase;

        $measurement = $this->db->get(db_prefix(). 'ingredient_measurement')->result_array();
        $i = 0;
        while(count($measurement) > $i){
            $measure[$i] = $measurement[$i] ;
            $i++;
        }

        $data['measurements'] = $measure;

        foreach ($ingredients as $ingredient) {
            $category = $ingredient['category'];
            $ingredientName = $ingredient['ingredient_name'];
            $subtype = $ingredient['ingredient_subtype'];
        
            // Create a new ingredient entry
            $newIngredient = [
                'ingredient' => $ingredientName, // Convert to lowercase for consistency
                'subtype' => $subtype,
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

    public function storeIngredients($userId,$category,$data){

        $data = serialize($data);
        $datecreated = date('Y-m-d H:i:s');
        $insertionData = [
            'restaurant' => $userId,
            'category' => $category,
            'ingredients' => $data,
            'datecreated' => $datecreated
        ];

        $this->db->select('*');
        $this->db->from(db_prefix().'user_ingredients');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category);
        $result = $this->db->get()->result();

        if($result){
            $this->db->where('category',$category);
            $this->db->where('restaurant',$userId);
            $this->db->update('tbluser_ingredients',$insertionData);
            return true;
        }

        $this->db->insert(db_prefix().'user_ingredients',$insertionData);
        return true;
    }

    public function storeStock($userId,$category,$item,$subtype,$data){
     
        //Get ingredient id 
        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $this->db->where('ingredient_subtype',$subtype);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id

        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        // Check stock is created
        $this->db->select('*');
        $this->db->from(db_prefix().'ingredient_stock');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('ingredient',$ingredient_id);
        $this->db->order_by('stock_count_date', 'desc');
        $this->db->limit(1);
        $isStockCreated = $this->db->get()->result_array();
        
        if(empty($isStockCreated)){
            $insertStock = [
                'restaurant' => $userId,
                'category' => $category_id,
                'ingredient' => $ingredient_id,
                'stock_count' => $data['stockCount'],
                'next_purchase_count' => $data['nextForecast'],
                'closing_stock' => '',
                'consumption' => '',
                'stock_count_date' => $data['stockCountDate']
            ];

            $this->db->insert(db_prefix().'ingredient_stock',$insertStock);
            return $this->db->insert_id();
        
        }
        $closing_stock = $data['stockCount'];
        $last_stock = $isStockCreated[0]['stock_count'];

        $this->db->select('*');
        $this->db->from(db_prefix().'ingredient_purchase_stock');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('ingredient',$ingredient_id);
        $this->db->order_by('purchase_date', 'desc');
        $this->db->limit(1);
        $purchase = $this->db->get()->result_array();
        $purchase_count = $purchase[0]['purchase_count'];
        floatval($purchase_count);
        floatval($closing_stock);
        floatval($last_stock);

        $consumption = ($last_stock+$purchase_count)-$closing_stock;


        
        
        $updateData = [
            'consumption' => $consumption,
            'closing_stock' =>$closing_stock
        ];

        $this->db->where('stock_count_date',$isStockCreated[0]['stock_count_date']);
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('ingredient',$ingredient_id);
        $this->db->update('tblingredient_stock',$updateData);
        
        $insertStock = [
            'restaurant' => $userId,
            'category' => $category_id,
            'ingredient' => $ingredient_id,
            'stock_count' => $data['stockCount'],
            'next_purchase_count' => $data['nextForecast'],
            'closing_stock' => '',
            'consumption' => '',
            'stock_count_date' => $data['stockCountDate']
        ];

        $this->db->insert(db_prefix().'ingredient_stock',$insertStock);
        return $this->db->insert_id();
    }
    
    public function selectingStock($userId){
        $i = 0;
        $count = 0;
        $this->db->select('*');
        $this->db->from(db_prefix().'user_ingredients');
        $this->db->where('restaurant',$userId);
        $stockDatas = $this->db->get()->result_array();
        if(!empty($stockDatas)){
            foreach($stockDatas as $stock){
                $category = $stock['category'];
                $ingredients = unserialize($stock['ingredients']);
                foreach($ingredients as $ingredient){
                    if($ingredient['isChecked'] == false){
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
        }else{
            return false;
        }
    }

    public function purchaseStock($userId,$category,$item,$subtype,$data){

        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $this->db->where('ingredient_subtype',$subtype);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $insertPurchaseStock = [
            'restaurant' => $userId,
            'category' => $category_id,
            'ingredient' => $ingredient_id,
            'purchase_count' => $data['purchaseCount'],
            'purchase_price' => $data['purchasePrice'],
            'purchase_date' => $data['purchaseDate'],
            'currency' => $data['currency']
        ];

        $this->db->insert(db_prefix().'ingredient_purchase_stock',$insertPurchaseStock);
        return $this->db->insert_id();

    }

    public function purchaseList($userId, $category, $item, $subtype){
        
        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $this->db->where('ingredient_subtype',$subtype);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $this->db->select('*');
        $this->db->from('tblingredient_purchase_stock');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('ingredient',$ingredient_id);

        $result = $this->db->get()->result_array();
        $i = 0;
        foreach($result as $singleData){
            $purchaseList[$i]['purchaseId'] = $singleData['purchase_id'];

            $purchaseList[$i]['purchaseCount'] = $singleData['purchase_count'];
            $purchaseList[$i]['purchasePrice'] = $singleData['purchase_price'];
            $purchaseList[$i]['purchaseDate'] = $singleData['purchase_date'];
            $purchaseList[$i]['currency'] = $singleData['currency'];
            
            $i++;

        }

        return $purchaseList;

    }
    public function stockList($userId, $category, $item, $subtype){
        
        //Get ingredient id
        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $this->db->where('ingredient_subtype',$subtype);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        $this->db->select('*');
        $this->db->from('tblingredient_stock');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('ingredient',$ingredient_id);

        $result = $this->db->get()->result_array();
        $i = 0;
        
        if(!empty($result)){

            foreach($result as $singleData){
                $stockList[$i]['stockId'] = $singleData['stock_id'];
                $stockList[$i]['stockCount'] = $singleData['stock_count'];
                $stockList[$i]['nextForcast'] = $singleData['next_purchase_count'];
                $stockList[$i]['closingStock'] = $singleData['closing_stock'];
                $stockList[$i]['consumption'] = $singleData['consumption'];
                $stockList[$i]['stockDate'] = $singleData['stock_count_date'];
                
                $i++;

            }
        }else{
            $stockList = [];
        }

        return $stockList;

    }
    public function selectingIngredients_costing($userId,$month,$year){
        
        $i = 0;
        $count = 0;
        $this->db->select('*');
        $this->db->from(db_prefix().'user_ingredients');
        $this->db->where('restaurant',$userId);
        $stockDatas = $this->db->get()->result_array();
        // print_r($stockDatas);die();
        if(!empty($stockDatas)){
            foreach($stockDatas as $stock){
                $category = $stock['category'];
                $ingredients = unserialize($stock['ingredients']);
                foreach($ingredients as $ingredient){
                    if($ingredient['isChecked'] == false){
                        continue;
                    }
                    
                    $item = $ingredient['ingredient'];
                    $subtype = $ingredient['subtype'];
                    //Get ingredient id
                    $this->db->select('tblingredients_listing.ingredient_id');
                    $this->db->from(db_prefix().'ingredients_listing');
                    $this->db->where('ingredient_name',$item);
                    $this->db->where('ingredient_subtype',$subtype);
                    $ingredient_id = $this->db->get()->result();
                    $ingredient_id = $ingredient_id[0]->ingredient_id;
                    

                    //Get category_id
                    $this->db->select('tblingredient_categories.category_id');
                    $this->db->from('tblingredient_categories');
                    $this->db->where('category',$category);
                    $category_id = $this->db->get()->result();
                    $category_id = $category_id[0]->category_id;
                    $query = $this->db->select('*')
                            ->from('tblingredient_purchase_stock')
                            ->where('restaurant',$userId)
                            ->where('category',$category_id)
                            ->where('ingredient',$ingredient_id)
                            ->where('MONTH(purchase_date)',$month)
                            ->where('YEAR(purchase_date)',$year)
                            ->get()->result_array();

                            
                    
                    
                    $j = 0;
                    $sums = [];
                    
                   
                    if($query){
                        foreach($query as $singleData){

                            $currency = $singleData['currency'];
                            $result[$j]['purchaseCount'] = $singleData['purchase_count'];
                            $result[$j]['purchasePrice'] = $singleData['purchase_price'];
                            $j++;
                        }
                        

                    
                        foreach ($result as $subArray) {

                            // Iterate through each key-value pair in the sub-array
                            foreach ($subArray as $key => $value) {
                                if(empty($value)){
                                    $value = 0;
                                }
                                // If the key exists in the sums array, add the value to it, otherwise, initialize it with the value
                                if (array_key_exists($key, $sums)) {
                                    $sums[$key] += $value;
                                } else {
                                    $sums[$key] = $value;
                                }
                            }
                        }

                       
                        
                    
                        settype($sums['purchasePrice'],'Integer');
                        settype($sums['purchaseCount'],'Integer');
                        // Check if both $sums['purchase_price'] and $sums['purchase_count'] are not zero or null
                        if ($sums['purchaseCount'] != 0 && $sums['purchaseCount'] != null) {
                            $unit_price = $sums['purchasePrice'] / $sums['purchaseCount'];
                            $unit_price = number_format((float)$unit_price, 2, '.', '');
                        } else {
                            // Handle the case where division might result in an error (e.g., division by zero or null)
                            $unit_price = 0; // Set a default value or handle it according to your logic
                        }
                        $ingredient['unit_price'] = $unit_price;
                        
                    }else{
                        $ingredient['unit_price'] = 0;
                    }
                    
            
                    $finalIngredients[]= $ingredient;
                    
                }


                $listingIngredients[$i]['category'] = $category;
                $listingIngredients[$i]['ingredients'] = $finalIngredients;
                // foreach($listingIngredients[$i]['ingredients'] as $ing){
                //     if($ing['unit_price'] == 0){
                //         unset($ing);
                //     }
                // }

                unset($finalIngredients);
                $i++;
            }
            
            return $listingIngredients;
        }        
        else{
            return false;
        }
    }

    public function createStock($data){
        $userId = $data['userId'];
        $category = $data['category'];
        $item = $data['item'];
        $ingredientData = $data['ingredientsData'];

        
  
         //Get ingredient id 
        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

       // Check stock is created
        $this->db->select('*');
        $this->db->from(db_prefix().'ingredient_stock_cards');
        $this->db->where('restaurant',$userId);
        $this->db->where('category',$category_id);
        $this->db->where('item',$ingredient_id);
        $this->db->order_by('datecreated', 'desc');
        $this->db->limit(1);
        $isStockCreated = $this->db->get()->result_array();
        
       
        if(empty($isStockCreated)){
            $insertStock = [
                'restaurant' => $userId,
                'category' => $category_id,
                'item' => $ingredient_id,
                'stockCount' => $ingredientData['stockCount'],
                'planToBuy' => $ingredientData['planToBuy'],
                'bought' => $ingredientData['bought'],
                'pricePerUnit' => $ingredientData['pricePerUnit'],
                'closingStock' => $ingredientData['closingStock'],
                'consumption' => $ingredientData['consumption'],
                'datecreated' => $ingredientData['date']
            ];
            
            $this->db->insert(db_prefix().'ingredient_stock_cards',$insertStock);
            return $this->db->insert_id();
        
        }
        

        $closing_stock = $ingredientData['stockCount'];
    
        $last_stock = $isStockCreated[0]['stockCount'];
       
        $purchase_count = $isStockCreated[0]['bought'];

        if(!empty($closing_stock && $last_stock && $purchase_count)){

            $consumption = ($last_stock+$purchase_count) - $closing_stock;

            $updateData = [
                'consumption' => $consumption,
                'closingStock' =>$closing_stock
            ];

            $this->db->where('datecreated',$isStockCreated[0]['datecreated']);
            $this->db->where('restaurant',$userId);
            $this->db->where('category',$category_id);
            $this->db->where('item',$ingredient_id);
            $this->db->update('tblingredient_stock_cards',$updateData);
        }
        
        $insertStock = [
            'restaurant' => $userId,
            'category' => $category_id,
            'item' => $ingredient_id,
            'stockCount' => $ingredientData['stockCount'],
            'planToBuy' => $ingredientData['planToBuy'],
            'bought' => $ingredientData['bought'],
            'pricePerUnit' => $ingredientData['pricePerUnit'],
            'closingStock' => $ingredientData['closingStock'],
            'consumption' => $ingredientData['consumption'],
            'datecreated' => $ingredientData['date']
        ];

        $this->db->insert(db_prefix().'ingredient_stock_cards',$insertStock);
        return $this->db->insert_id();
    }

    public function editStock($data){
        $userId = $data['userId'];
        $id = $data['stockId'];
        $ingredientData = $data['ingredientsData'];

        $this->db->where('id',$id);
        $this->db->update('tblingredient_stock_cards',$ingredientData);

        return true;
    }

    public function deleteStock($data){
        $userId = $data['userId'];
        $id = $data['stockId'];

        $this->db->where('id',$id);
        $this->db->delete('tblingredient_stock_cards');

        return true;
    }

    public function stockLists($data){

        
        $i = 0;
        $output = [];
        $userId = $data['userId'];
        $category = $data['category'];
        $item = $data['item'];

        //Get ingredient id 
        $this->db->select('tblingredients_listing.ingredient_id');
        $this->db->from(db_prefix().'ingredients_listing');
        $this->db->where('ingredient_name',$item);
        $ingredient_id = $this->db->get()->result();
        $ingredient_id = $ingredient_id[0]->ingredient_id;

        //Get category_id
        $this->db->select('tblingredient_categories.category_id');
        $this->db->from('tblingredient_categories');
        $this->db->where('category',$category);
        $category_id = $this->db->get()->result();
        $category_id = $category_id[0]->category_id;

        
        $stocks = $this->db->select('*')
                            ->from('tblingredient_stock_cards')
                            ->where('restaurant',$userId)
                            ->where('category',$category_id)
                            ->where('item',$ingredient_id)
                            ->get()->result_array();
        
        if($stocks){

            foreach($stocks as $stock){
            
                $output[$i] = $stock;

                $i++;
            }
            return $output;
        }

        $output= [
            'isStock' => false
        ];

        return $output;

        
    }

    public function createIngredient($data){
        $userId = $data['userId'];

        $category = $data['category'];

        $ingredientsData = $data['ingredientsData'];

        $storeIngredients = $this->storeIngredients($userId,$category,$ingredientsData);

        if($storeIngredients){
            return true;
        }

        return false;
        
    }


}