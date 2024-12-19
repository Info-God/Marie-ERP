<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'r_id',
    'types',
    ];
$sIndexColumn     = 'r_id';
$sTable           = db_prefix() . 'restaurant_business_types';

$join = [];
$additionalSelect = [];

$filter  = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join,  $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'r_id') {
            $_data = $aRow['r_id'];
        } elseif ($aColumns[$i] == 'types') {
            $_data = $aRow['types'];
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';

            if (has_permission('knowledge_base', '', 'edit')) {
                $_data .= ' | <a href="#" data-toggle="modal" data-target="#add-items" data-id="' . $aRow['r_id'] . '">' . _l('edit') . '</a>';
            
            }

            if (has_permission('knowledge_base', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('restaurant/delete_businesstype/' . $aRow['r_id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
            
            
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    

    $output['aaData'][] = $row;
}
