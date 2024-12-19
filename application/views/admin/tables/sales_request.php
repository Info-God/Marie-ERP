<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ //this is for column name
    'tblrestaurant_reach_request.id',
    'tblrestaurant_reach_request.restaurant',
    'GROUP_CONCAT(tblrestaurant_reach_data.reach) as reach',
    'tblrestaurant_reach_request.channels',
    'tblrestaurant_reach_request.datecreated',
];
$sIndexColumn     = 'id';
$sTable           = db_prefix() . 'restaurant_reach_request';
$join = [
    'JOIN ' . db_prefix() . 'restaurant_reach_data ON ' . db_prefix() . 'restaurant_reach_data.r_id = ' . db_prefix() . 'restaurant_reach_request.r_id',
];
$additionalSelect = ['tblrestaurant_reach_request.status','tblrestaurant_reach_request.r_id'];
// $where   = ["where status = 0"];
$where   = [];
$filter  = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect, 'GROUP BY tblrestaurant_reach_request.r_id, tblrestaurant_reach_request.id, tblrestaurant_reach_request.restaurant, tblrestaurant_reach_request.channels, tblrestaurant_reach_request.datecreated, tblrestaurant_reach_request.status');
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $requested = [];
    $nullvalue = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'tblrestaurant_reach_request.id') {
            $_data = $aRow['tblrestaurant_reach_request.id'];
        } elseif ($aColumns[$i] == 'tblrestaurant_reach_request.restaurant') {
            $_data = $aRow['tblrestaurant_reach_request.restaurant'];
            $_data = '<b>' . $_data . '</b>';
        } elseif ($aColumns[$i] == 'GROUP_CONCAT(tblrestaurant_reach_data.reach) as reach') {
            $_data = $aRow['reach'];
            $_data = '<b>' . $_data . '</b>';
            $channels_present = explode(",", $aRow['reach']);
        } elseif ($aColumns[$i] == 'tblrestaurant_reach_request.channels') {
            $channels = unserialize($aRow['tblrestaurant_reach_request.channels']);
            if ($channels == null || $channels == "") {
                $nullvalue = true;
            } else {
                foreach ($channels as $channels_value) {
                    if (!in_array($channels_value, $channels_present)) {
                        $requested[] = $channels_value;
                    }
                }
                if ($requested == []) {
                    $nullvalue = true;
                }
                $aRow['tblrestaurant_reach_request.channels'] = implode(", ", $requested);
                $_data = $aRow['tblrestaurant_reach_request.channels'];
                $_data = '<b>' . $_data . '</b>';
            }
        } elseif ($aColumns[$i] == 'tblrestaurant_reach_request.datecreated') {
            $_data = $aRow['tblrestaurant_reach_request.datecreated'];
            $_data = '<b>' . $_data . '</b>';
        }

        $row[]              = $_data;
    }

    if ($nullvalue == true) {
        continue;
    }

    if ($aRow['status'] == 0) {
        $options = icon_btn('SalesRequest/accept/' . $aRow['tblrestaurant_reach_request.id'], 'check', 'btn-default');
        $row[]   = $options .= icon_btn('SalesRequest/decline/' . $aRow['tblrestaurant_reach_request.id'], 'remove', 'btn-danger _delete');
        $row['DT_RowClass'] = 'has-row-options';
    } elseif ($aRow['status'] == 1) {
        $_data = "Accepted";
    } elseif ($aRow['status'] == 2) {
        $_data = "Declined";
    }
    $row[]              = $_data;

    $output['aaData'][] = $row;
}
