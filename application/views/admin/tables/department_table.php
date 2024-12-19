<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'dept_id',
    'department_name',
    ];
$sIndexColumn     = 'dept_id';
$sTable           = db_prefix() . 'restaurant_departments';

$join = [];
$additionalSelect = [];
$where   = [];
$filter  = [];

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'dept_id') {
            $_data = $aRow['dept_id'];
        } elseif ($aColumns[$i] == 'department_name') {
            $_data = $aRow['department_name'];
            $_data = '<b>' . $_data . '</b>';
            
        }

        $row[]              = $_data;

    }
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#add-departments', 'data-id' => $aRow['dept_id']]);
    $row[]   = $options .= icon_btn('restaurant_departments/delete_department/' . $aRow['dept_id'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
