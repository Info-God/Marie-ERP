<?php

use function PHPSTORM_META\type;

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_sales extends App_import
{
    protected $notImportableFields = [];

    protected $requiredFields = ['Item_No', 'Category', 'Item_Name', 'Total_Unit_Sales_Volume'];

    public function __construct()
    {
        $this->addImportGuidelinesInfo('Duplicate Item Name rows won\'t be imported.', true);

        $this->addImportGuidelinesInfo('Categories are Predefined,<b> Please check before Importing</b>' .
            '<ol>
                                                        <li>I.Main Courses</li>
                                                        <li>II.Appetizers</li>
                                                        <li>III.Desserts</li>
                                                        <li>IV.Beverages</li>
                                                        </ol>', false);

        parent::__construct();
    }

    public function perform()
    {
        $this->initialize();

        $databaseFields      = $this->getImportableDatabaseFields();
        $totalDatabaseFields = count($databaseFields);
        $month = $this->month;
        $year = $this->year;
        $id = $this->id;
        $row_id = $this->row_count($id,$month);

        foreach ($this->getRows() as $rowNumber => $row) {
            $insert    = [];
            $duplicate = false;

            for ($i = 0; $i < $totalDatabaseFields; $i++) {
                if (!isset($row[$i])) {
                    continue;
                }

                $row[$i] = $this->checkNullValueAddedByUser($row[$i]);

                if (
                    in_array($databaseFields[$i], $this->requiredFields) &&
                    $row[$i] == ''
                ) {
                    $row[$i] = '/';
                } elseif ($databaseFields[$i] == 'Item_Name') {
                    $row[2] = ucfirst($row[2]);
                    $dup = $this->ci->Sales_model->exists_item_name($row[2]);
                    $check = $this->ci->Sales_model->check_item_name($id, $row[2],$row[3], $month,$year);
                    if (is_string($check)) {
                        $update=true;
                    } elseif($check == false) {
                        $duplicate=false;
                    }elseif($check==true){
                        $duplicate=true;
                    }
                } elseif ($databaseFields[$i] == 'Category') {
                    $category = $this->ci->Sales_model->list_category();
                    if (!in_array($row[1], $category)) {
                        $this->new_category = true;
                    }
                }

                $insert[$databaseFields[$i]] = $row[$i];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {

                $row_id = $row_id + 1;

                if(!isset($update)){
                $this->addSalesData($insert, $row_id, $month,$year, $id);
                $this->incrementImported();
                }else{
                $this->updateSalesData($insert, $check, $month,$year, $id);
                $this->incrementUpdated();
                }
                if (!$dup && ($this->new_category || !$this->new_category)) {
                    $this->ci->Sales_model->add_category_item_import($row[1], $row[2]);
                }
                unset($dup);
                $this->simulationData[$rowNumber] = $insert;
                continue;
            }
            if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
                break;
            }
        }
    }

    public function formatFieldNameForHeading($field)
    {
        if (strtolower($field) == 'title') {
            return 'Position';
        }

        return parent::formatFieldNameForHeading($field);
    }

    protected function email_formatSampleData()
    {
        return uniqid() . '@example.com';
    }

    protected function failureRedirectURL()
    {
        return admin_url('sales/sales_import');
    }

    protected function afterSampleTableHeadingText($field)
    {
        $contactFields = [
            'firstname', 'lastname', 'email', 'contact_phonenumber', 'title',
        ];

        if (in_array($field, $contactFields)) {
            return '<br /><span class="text-info">' . _l('import_contact_field') . '</span>';
        }
    }

    private function updateSalesData($data, $row_id, $month,$year, $id)
    {
        $SalesFields = $this->getSalesMenuFields();
        $import = 1;
        $tmpInsert       = [];

        foreach ($data as $key => $val) {
            foreach ($SalesFields as $tmpSalesField) {
                if (isset($data[$tmpSalesField])) {
                    $tmpInsert[$tmpSalesField] = $data[$tmpSalesField];
                }
            }
        }

        $this->ci->Sales_model->update_sales_menu_data($tmpInsert, $id, $month,$year, $row_id, $import);
        }

    private function addSalesData($data, $row_id, $month,$year, $id)
    {
        $SalesFields = $this->getSalesMenuFields();
        $import = 1;
        $tmpInsert       = [];

        foreach ($data as $key => $val) {
            foreach ($SalesFields as $tmpSalesField) {
                if (isset($data[$tmpSalesField])) {
                    $tmpInsert[$tmpSalesField] = $data[$tmpSalesField];
                }
            }
        }

        $this->ci->Sales_model->add_sales_menu_data($tmpInsert, $id, $month,$year, $row_id, $import);
    }

    private function getSalesMenuFields()
    {
        return $this->ci->db->list_fields(db_prefix() . 'restaurant_sales_menu_data_sample');
    }

    private function row_count($id,$month)
    {
        return $this->ci->Sales_model->get_row_count($id, $month);
    }
}
