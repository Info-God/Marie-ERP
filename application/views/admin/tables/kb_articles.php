<?php

use Ddeboer\Imap\Search\Text\Subject;

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [//this is for column name
    'subject',
    'articlegroup',
    'datecreated',
    'articleNumber'
    ];
$sIndexColumn     = 'articleid';
$sTable           = db_prefix() . 'knowledge_base';
$additionalSelect = [
    'name',
    'groupid',
    'articleid',
    'slug',
    'staff_article',
     db_prefix() . 'knowledge_base.description',
    ];
$join = [//join query for article and groups
    'LEFT JOIN ' . db_prefix() . 'knowledge_base_groups ON ' . db_prefix() . 'knowledge_base_groups.groupid = ' . db_prefix() . 'knowledge_base.articlegroup',
    ];

$where   = [];
$filter  = [];
$groups  = $this->ci->knowledge_base_model->get_kbg();//get groups data in array
$_groups = [];
foreach ($groups as $group) {
    if ($this->ci->input->post('kb_group_' . $group['groupid'])) {
        array_push($_groups, $group['groupid']);
    }
}
if (count($_groups) > 0) {
    array_push($filter, 'AND articlegroup IN (' . implode(', ', $_groups) . ')');
}
if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (!has_permission('knowledge_base', '', 'create') && !has_permission('knowledge_base', '', 'edit')) {
    array_push($where, ' AND ' . db_prefix() . 'knowledge_base.active=1');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'articlegroup') {
            $_data = $aRow['name'];
        } elseif ($aColumns[$i] == 'subject') {
            $link = admin_url('knowledge_base/view/' . $aRow['slug']);
            if ($aRow['staff_article'] == 0) {
                $link = site_url('knowledge-base/article/' . $aRow['slug']);
            }

            $_data = '<b>' . $_data . '</b>';
            if (has_permission('knowledge_base', '', 'edit')) {
                $_data = '<a href="' . admin_url('knowledge_base/article/' . $aRow['articleid']) . '" class="font-size-14">' . $_data . '</a>';
            } else {
                $_data = '<a href="' . $link . '" target="_blank" class="font-size-14">' . $_data . '</a>';
            }

            if ($aRow['staff_article'] == 1) {
                $_data .= '<span class="label label-default pull-right">' . _l('internal_article') . '</span>';
            }

            $_data .= '<div class="row-options">';

            $_data .= '<a href="' . $link . '" target="_blank">' . _l('view') . '</a>';

            if (has_permission('knowledge_base', '', 'edit')) {
                $_data .= ' | <a href="' . admin_url('knowledge_base/article/' . $aRow['articleid']) . '">' . _l('edit') . '</a>';
            }

            if (has_permission('knowledge_base', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('knowledge_base/delete_article/' . $aRow['articleid']) . '" class="_delete text-danger">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'datecreated') {
            $_data = _dt($_data);
        }
        elseif ($aColumns[$i] == 'articleNumber') {
            $_data = $aRow['articleNumber'];
        }

        $row[]              = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }

    $output['aaData'][] = $row;
}
