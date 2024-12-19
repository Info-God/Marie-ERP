<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'overheads_id',
    'category',
    'predefined_items'
    ];
$sIndexColumn     = 'overheads_id';
$sTable           = db_prefix() . 'overheads';

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

        if ($aColumns[$i] == 'overheads_id') {
            $_data = $aRow['overheads_id'];
        } elseif ($aColumns[$i] == 'category') {
            $_data = $aRow['category'];
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';

            if (has_permission('overhead', '', 'edit')) {
                $_data .= ' <a href="#" data-toggle="modal" data-target="#add-items" data-id="' . $aRow['overheads_id'] . '">' . _l('edit') . '</a>';
            
            }

            if (has_permission('overhead', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('overhead/delete_overhead/' . $aRow['overheads_id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
            
            
        }elseif ($aColumns[$i] == 'predefined_items') {
            $_data = $aRow['predefined_items'];
            $_data = '<b>' . $_data . '</b>';

           
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    

    $output['aaData'][] = $row;
}
