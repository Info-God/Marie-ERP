<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'restaurant_id',
    'business_name',
    'client_email',
    'b_address',
    'b_country',
    'datecreated',
    ];
$sIndexColumn     = 'restaurant_id';
$sTable           = db_prefix() . 'restaurant_registration';
// $join = [//join query for article and groups
//     'LEFT JOIN ' . db_prefix() . 'knowledge_base_groups ON ' . db_prefix() . 'knowledge_base_groups.groupid = ' . db_prefix() . 'knowledge_base.articlegroup',
//     ];
$join = [
    'LEFT JOIN ' . db_prefix() . 'client_details ON ' . db_prefix() . 'client_details.client_user = ' . db_prefix() . 'restaurant_registration.restaurant_id'
];
$additionalSelect = [
    'restaurant_id'
];
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

        if ($aColumns[$i] == 'b_country') {
            $_data = $aRow['b_country'];
        } elseif ($aColumns[$i] == 'business_name') {
            $link = admin_url('sales/month/' . $aRow['restaurant_id']);

            $_data = '<b>' . $_data . '</b>';
            if (has_permission('knowledge_base', '', 'edit')) {
                $_data = '<a href="' . admin_url('restaurant/restaurant_menus/' . $aRow['restaurant_id']) . '" class="font-size-14">' . $_data . '</a>';
            } else {
                $_data = '<a href="' . $link . '" target="_blank" class="font-size-14">' . $_data . '</a>';
            }

            $_data .= '<div class="row-options">';

            $_data .= '<a href="' . $link . '" target="">' . _l('view') . '</a>';

            if (has_permission('knowledge_base', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('sales/sales_channel/' . $aRow['restaurant_id']) . '">' . _l('edit') . '</a>';
            }

            if (has_permission('knowledge_base', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('restaurant/delete_restaurant/' . $aRow['restaurant_id']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'datecreated') {
            $_data = _dt($_data);
        }
        elseif ($aColumns[$i] == 'b_address') {
            $_data = $aRow['b_address'];
        }
        elseif ($aColumns[$i] == 'restaurant_id') {
            $_data = $aRow['restaurant_id'];
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }

    $output['aaData'][] = $row;
}
