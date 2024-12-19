<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [ //this is for column name
    'plan_id',
    'restaurant_plan',
    'plan_cost'
];
$sIndexColumn     = 'plan_id';
$sTable           = db_prefix() . 'restaurant_plans';
// $join = [//join query for article and groups
//     'LEFT JOIN ' . db_prefix() . 'knowledge_base_groups ON ' . db_prefix() . 'knowledge_base_groups.groupid = ' . db_prefix() . 'knowledge_base.articlegroup',
//     ];
$join = [];
$additionalSelect = ['restaurant_plan'];
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

        if ($aColumns[$i] == 'plan_id') {
            $_data = $aRow['plan_id'];
        } elseif ($aColumns[$i] == 'restaurant_plan') {
            $_data = $aRow['restaurant_plan'];
            $_data = '<b>' . $_data . '</b>';
        } elseif ($aColumns[$i] == 'plan_cost') {
            $_data ="RM ". $aRow['plan_cost'];
        }

        $row[]              = $_data;
    }
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#plans-modal', 'data-id' => $aRow['plan_id']]);
    $row[]   = $options .= icon_btn('plans/delete_plans/' . $aRow['plan_id'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
