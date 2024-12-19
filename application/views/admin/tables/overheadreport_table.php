<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'id',
    'report',
    'description'
    ];
$sIndexColumn     = 'id';
$sTable           = db_prefix() . 'overheads_report';

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

        if ($aColumns[$i] == 'id') {
            $_data = $aRow['id'];
        } elseif ($aColumns[$i] == 'report') {
            $_data = $aRow['report'];
            $_data = '<b>' . $_data . '</b>';
            $_data .= '<div class="row-options">';

            if (has_permission('knowledge_base', '', 'edit')) {
                $_data .= ' <a href="#" data-toggle="modal" data-target="#add-items" data-id="' . $aRow['id'] . '">' . _l('edit') . '</a>';
            
            }

            if (has_permission('knowledge_base', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('overhead/delete_report/' . $aRow['id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
            
            
        }elseif ($aColumns[$i] == 'description') {
            $_data = $aRow['description'];
            $_data = '<b>' . $_data . '</b>';

           
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    

    $output['aaData'][] = $row;
}
