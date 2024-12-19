<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ // This is for column names
    'p_id',
    db_prefix() . 'restaurant_registration.business_name',
    db_prefix() . 'restaurant_plans.restaurant_plan', // Updated column
    db_prefix() . 'restaurant_user_plans.datecreated',
    db_prefix() . 'restaurant_user_plans.nextduedate',
    db_prefix() . 'restaurant_user_plans.restaurant_user',
    db_prefix() . 'client_details.deactivate',
];
$sIndexColumn     = 'p_id';
$sTable           = db_prefix() . 'restaurant_user_plans';
$join = [ // Join query for article and groups
    'JOIN ' . db_prefix() . 'restaurant_registration ON ' . db_prefix() . 'restaurant_registration.restaurant_id = ' . db_prefix() . 'restaurant_user_plans.restaurant_user',
    'JOIN ' . db_prefix() . 'restaurant_plans ON ' . db_prefix() . 'restaurant_plans.plan_id = ' . db_prefix() . 'restaurant_user_plans.restaurant_plan',
    'JOIN ' . db_prefix() . 'client_details ON ' . db_prefix() . 'client_details.client_user = ' . db_prefix() . 'restaurant_user_plans.restaurant_user', // New join
];

$additionalSelect = [];
$where   = [];
$filter  = [];
// $groups  = $this->ci->knowledge_base_model->get_kbg();//get groups data in array
// $_groups = [];
// foreach ($groups as $group) {
//     if ($this->ci->input->post('kb_group_' . $group['groupid'])) {
//         array_push($_groups, $group['groupid']);
//     }
// }
// if (count($_groups) > 0) {
//     array_push($filter, 'AND articlegroup IN (' . implode(', ', $_groups) . ')');
// }
// if (count($filter) > 0) {
//     array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
// }

// if (!has_permission('knowledge_base', '', 'create') && !has_permission('knowledge_base', '', 'edit')) {
//     array_push($where, ' AND ' . db_prefix() . 'knowledge_base.active=1');
// }

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'p_id') {
            $_data = $aRow['p_id'];
        } elseif ($aColumns[$i] == 'tblrestaurant_registration.business_name') {
            $_data = $aRow['tblrestaurant_registration.business_name'];
            $_data = '<b>' . $_data . '</b>';
        } elseif ($aColumns[$i] == 'restaurant_plan') {
            $_data = $aRow['restaurant_plan'];
        } elseif ($aColumns[$i] == 'tblrestaurant_user_plans.datecreated') {
            $_data = $aRow['tblrestaurant_user_plans.datecreated'];
        } elseif ($aColumns[$i] == 'tblrestaurant_user_plans.nextduedate') {
            $_data = $aRow['tblrestaurant_user_plans.nextduedate'];
        }elseif ($aColumns[$i] == 'tblrestaurant_user_plans.restaurant_user') {
            $_data = "Paid";
        }elseif($aColumns[$i] == 'tblclient_details.deactivate'){
            if ($aRow['tblclient_details.deactivate'] == 0) {
                $_data = '<a href="Billing/deactivate/'.$aRow["tblrestaurant_user_plans.restaurant_user"].'" class="btn btn-danger btn-icon"><i class="fa fa-times"></i> Deactivate </a>';
            } else {
                $_data = '<a href="Billing/activate/'.$aRow["tblrestaurant_user_plans.restaurant_user"].'" class="btn btn-success btn-icon"><i class="fa fa-check"></i> Activate </a>';
            }
        }

        $row[]              = $_data;
    }

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
