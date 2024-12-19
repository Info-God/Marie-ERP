<?php

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

    public function get_ingredients_list($name, $id)
    {

        for ($i = 0; $i < count($name); $i++) {
            $value = $name[$i];
            $this->db->insert(db_prefix() . 'restaurant_ingredients_menu_data', [
                "r_id" => $id,
                "selections" => $value
            ]);
            if ($value == 'Vegetables') {
                $data['vegetables'] = [
                    "Banana - Cooking",
                    "Banana - Flower",
                    "Banana - Stem",
                    "Bayam",
                    "Beans - Big",
                    "Beans - Small",
                    "Beetroot",
                    "Bitter Gourd",
                    "Bottle Gourd",
                    "Brinjal - Big",
                    "Brinjal - Small",
                    "Cabbage",
                    "Carrot",
                    "Cauliflower",
                    "Chilli - Green",
                    "Chilli - Padi",
                    "Chow Chow",
                    "Coconut",
                    "Coriander",
                    "Corn - Baby",
                    "Curry Leaves",
                    "Garlic",
                    "Ginger",
                    "Green Peas",
                    "Ladies Finger",
                    "Mushroom",
                    "Onion - Big",
                    "Onion - Small",
                    "Peppermint",
                    "Pumpkin",
                    "Pumpkin - White",
                    "Radish",
                    "Ridged Gourd",
                    "Snake Gourd",
                    "Spinach",
                    "Spring Onion",
                    "Tomato",
                    "Potato",
                    "Drumstick",
                    "Capsicum",
                    "Broad Beans"
                ];
            } elseif ($value == 'BeveragesHot') {
                $data['beverages_hot'] = [
                    "Tea Powder",
                    "Bru",
                    "Sukumali",
                    "Narasus",
                    "MTR Badam",
                    "Teapot",
                    "Carnation",
                    "Horlicks",
                    "Milo"
                ];
            } elseif ($value == 'BeveragesCold') {
                $data['Beverages_cold'] = [
                    "Soda",
                    "Coca-Cola",
                    "Coca-Cola Light"
                ];
            } elseif ($value == 'Sauces') {
                $data['sauces'] = [
                    "Chilli Paste",
                    "Chilli Sauce",
                    "Kimball Sauce",
                    "Kicap Cair",
                    "Kicap Pekat"
                ];
            } elseif ($value == 'Vegetarian') {
                $data['Vegetarian'] = ["Chicken"];
            } elseif ($value == 'Flour') {
                $data['flour'] = [
                    "Rava",
                    "Rice",
                    "Corn",
                    "Wheat",
                    "Ragi",
                    "Maida",
                    "Godhumai Rava",
                    "Semiya - White",
                    "Semiya - Ragi"
                ];
            } elseif ($value == 'Rice') {
                $data['Rice'] = [
                    "Basmati",
                    "Ponni",
                    "Idly",
                    "Parboiled",
                    "Pacharsi"
                ];
            } elseif ($value == 'Fruits') {
                $data['fruits'] = [
                    "Orange",
                    "Apple",
                    "Watermelon",
                    "Pineapple",
                    "Grapes",
                    "Pomegranate",
                    "Lime",
                    "Cucumber"
                ];
            } elseif ($value == 'Oils') {
                $data['oils'] = [
                    "Cooking",
                    "Sesame",
                    "Ghee",
                    "Dalda",
                    "Mazola"
                ];
            } elseif ($value == 'Seafoods') {
                $data['seafoods'] = [
                    "Tenggiri",
                    "Toman",
                    "Nethili",
                    "Kilangan",
                    "Veeral",
                    "Sora",
                    "Prawn",
                    "Squid",
                    "Crab"
                ];
            } elseif ($value == 'Meats') {
                $data['meats'] = [
                    "Chicken - Full",
                    "Chicken - Legs",
                    "Chicken - Wings",
                    "Chicken - Minced",
                    "Nattukozhi",
                    "Mutton - Boneless",
                    "Mutton - Bonemarrow",
                    "Mutton - Tripe",
                    "Mutton - Minced",
                    "Mutton - Chops",
                    "Mutton - Ribs",
                    "Turkey",
                    "Quail",
                    "Rabbit",
                    "Eggs"
                ];
            } elseif ($value == 'Lentils') {
                $data['lentils'] = [
                    "Thuvarum Paruppu",
                    "Pasi Paruppu",
                    "Kadalai Paruppu",
                    "Ulunthu",
                    "Pottukadalai"
                ];
            } elseif ($value == 'Spices') {
                $data['spices'] = [
                    "Melagu",
                    "Jeeragam",
                    "Sombu",
                    "Vendhayam",
                    "Kadugu",
                    "Kerambu",
                    "Star Anise",
                    "Pattai",
                    "Kalpasi",
                    "Bay Leaf",
                    "Elakai",
                    "Jathikai",
                    "Poppy Seeds",
                    "Jathipathri",
                    "Perungayam",
                    "Vellai Ellu",
                    "Tamarind",
                    "Salt",
                    "Sundaka Vathal",
                    "Moor Milagai",
                    "Raisin",
                    "Cashew",
                    "Appalam",
                    "Javvarisi",
                    "Bush - Red",
                    "Bush - Yellow",
                    "Cherry - Red",
                    "Cherry - Green",
                    "Cherry - Yellow",
                    "Honey",
                    "Sugar",
                    "Ajinomoto"
                ];
            } elseif ($value == 'Powders') {
                $data['Powders'] = [
                    "Chilli",
                    "Coriander / Malli",
                    "Cumin / Siragam",
                    "Garam Masala",
                    "Garlic",
                    "Ginger",
                    "Jal Jeera",
                    "Turmeric",
                    "Coconut"
                ];
            } elseif ($value == 'Dairy') {
                $data['dairy'] = [
                    "Cow's",
                    "Sweet",
                    "Butter",
                    "Cheese"
                ];
            }
        }

        // $this->db->where('id', $id);
        // $result = $this->db->get(db_prefix() . 'restaurant_ingredients_menu')->result_array();

        return $data;
    }
}
