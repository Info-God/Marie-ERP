<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'id',
    'category',
    'item',
    ];
$sIndexColumn     = 'id';
$sTable           = db_prefix() . 'restaurant_menu_categories';

$join = [];
$additionalSelect = [];
$where   = ['where validated=1'];
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
            $_data = $aRow['category'];
            $_data = '<b>' . $_data . '</b>';
        } elseif ($aColumns[$i] == 'item') {
            $_data = $aRow['item'];
            $_data = '<b>' . $_data . '</b>';
        }

        $row[]              = $_data;

    }
    $options = icon_btn('#/'.$aRow['id'], 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#add-items', 'data-id' => $aRow['id']]);
    $row[]   = $options .= icon_btn('items/delete_item/' . $aRow['id'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
