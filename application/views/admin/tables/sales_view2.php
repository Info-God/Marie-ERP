<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$id = $this->id;
$aColumns = [ //this is for column name
    'id',
    'name',
];
$sIndexColumn     = 'id';
$sTable           = db_prefix() . 'month';
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

$j=0;
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'id') {
            $j++;
        } elseif ($aColumns[$i] == 'name') {
            $_data =$aRow['name'];
            $_data = '<b>' . $_data . '</b>';
        } 

        $row[]              = $_data;

    }

    $options = icon_btn(admin_url('sales/sales_channel/'.$id.'/'.$aRow['name']), 'pencil-square-o', 'btn-default');
    $row[]   = $options .= icon_btn('sales/delete_month_data/'. $id.'/'.$aRow['name'], 'remove', 'btn-danger _delete');
    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
