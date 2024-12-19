<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'r_id',
    'cuisine_types'
    ];
$sIndexColumn     = 'r_id';
$sTable           = db_prefix() . 'restaurant_cuisine';
// $join = [//join query for article and groups
//     'LEFT JOIN ' . db_prefix() . 'knowledge_base_groups ON ' . db_prefix() . 'knowledge_base_groups.groupid = ' . db_prefix() . 'knowledge_base.articlegroup',
//     ];
$join = [];
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

        if ($aColumns[$i] == 'r_id') {
            $_data = $aRow['r_id'];
        } elseif ($aColumns[$i] == 'cuisine_types') {
            $_data = $aRow['cuisine_types'];
            $_data = '<b>' . $_data . '</b>';
            
        }

        $row[]              = $_data;

    }
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#add-cuisine', 'data-id' => $aRow['r_id']]);
    $row[]   = $options .= icon_btn('restaurant_cuisine/delete_cuisine/' . $aRow['r_id'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
