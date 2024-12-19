<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ //this is for column name
    'category_id',
    'category',
    'image',
];
$sIndexColumn     = 'category_id';
$sTable           = db_prefix() . 'ingredient_categories';

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

        if ($aColumns[$i] == 'id') {
            $_data = $aRow['id'];
        } elseif ($aColumns[$i] == 'category') {
            $_data = '<a href="#" data-toggle="modal" data-target="#edit-ingredients" data-id="' . $aRow['id'] . '">' . $aRow['category'] . '</a>';
            $_data = '<b>' . $_data . '</b>';
        } elseif ($aColumns[$i] == 'image') {
            continue;
        }

        $row[]              = $_data;
    }
    $options = icon_btn('#', 'pencil-square-o', 'btn-default-my-btn', [
        'data-toggle' => 'modal', 
        'data-target' => '#edit-ingredients',
        'data-id' => $aRow['category_id']
    ]);
    $row[]   = $options .= icon_btn('ingredients/delete_ingredients/' . $aRow['category_id'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
