<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC) as tags',
    'start_time',
    'end_time',
    'rel_id',
    'end_time - start_time',
    'end_time - start_time',
    );
$time_h_column = 5;
$time_d_column = 6;
$tags_column = 1;
if($view_all == true){
    array_unshift($aColumns, 'staff_id');
    $time_h_column = 6;
    $time_d_column = 7;
    $tags_column = 2;
}

$sIndexColumn = "id";
$sTable       = 'tbltaskstimers';
$join         = array(
    'LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id'
    );

$where = array();

$staff_id = false;

if($this->_instance->input->post('staff_id')){
    $staff_id = $this->_instance->input->post('staff_id');
} else if($view_all == false){
    $staff_id = get_staff_user_id();
}

if($staff_id != false){
$where        = array(
    'AND staff_id=' . $staff_id
    );
}

if($this->_instance->input->post('project_id')){
    array_push($where,'AND task_id IN (SELECT id FROM tblstafftasks WHERE rel_type = "project" AND rel_id = "'.$this->_instance->input->post('project_id').'")');
}

$filter = $this->_instance->input->post('range');
if ($filter != 'period') {
    if ($filter == 'today') {
        $beginOfDay = strtotime("midnight");
        $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
        array_push($where, ' AND start_time BETWEEN ' . $beginOfDay . ' AND ' . $endOfDay);
    } else if ($filter == 'this_month') {
        $beginThisMonth = date('Y-m-01');
        $endThisMonth   = date('Y-m-t 23:59:59');
        array_push($where, ' AND start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth));
    } else if ($filter == 'last_month') {
        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
        array_push($where, ' AND start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth));
    } else if ($filter == 'this_week') {
        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        array_push($where, ' AND start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
    } else if ($filter == 'last_week') {
        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
        array_push($where, ' AND start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek));
    }
} else {
    $start_date = to_sql_date($this->_instance->input->post('period-from'));
    $end_date   = to_sql_date($this->_instance->input->post('period-to'));
    array_push($where, ' AND start_time BETWEEN ' . strtotime($start_date. ' 00:00:00') . ' AND ' . strtotime($end_date.' 23:59:00'));
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tbltaskstimers.id',
    'task_id',
    'rel_type',
    'status'
    ));

$output                           = $result['output'];
$rResult                          = $result['rResult'];

$footer_data['total_logged_time_h'] = 0;
$footer_data['total_logged_time_d'] = 0;

$footer_data['chart'] = array();
$footer_data['chart']['labels'] = array();
$footer_data['chart']['data'] = array();

$temp_weekdays_data = array();
$temp_months_data = array();
$chart_type = 'today';
$chart_type_month_from_filter = false;
$weekDay = date('w', strtotime(date('Y-m-d H:i:s')));
if($filter == 'today'){
    $footer_data['chart']['labels'] = array(_l('today'));
} else if($filter == 'this_week' || $filter == 'last_week'){
    foreach(get_weekdays() as $day){
        array_push($footer_data['chart']['labels'],$day);
    }
    $i = 0;
    foreach(get_weekdays_original() as $day){
        if($weekDay != "0"){
             $footer_data['chart']['labels'][$i] = date('d',strtotime($day. ' ' . str_replace('_', ' ', $filter))). ' - ' .$footer_data['chart']['labels'][$i];
        } else {
            if($filter == 'this_week'){
                $strtotime = 'last '.$day;
                if($day == 'Sunday'){
                    $strtotime = 'sunday this week';
                }
                $footer_data['chart']['labels'][$i] = date('d',strtotime($strtotime)). ' - ' .$footer_data['chart']['labels'][$i];
            } else {
                $strtotime = $day .' last week';
                $footer_data['chart']['labels'][$i] = date('d',strtotime($strtotime)). ' - ' .$footer_data['chart']['labels'][$i];
            }
        }
        $i++;
    }

    $chart_type = 'week';
} else if($filter == 'this_month' || $filter == 'last_month') {
    $month = ($filter == 'this_month') ? date('m') : date('m',strtotime('first day last month'));
    $month_year = ($filter == 'this_month') ? date('Y') : date('Y',strtotime('first day last month'));

    for ($d = 1; $d <= 31; $d++) {
        $time = mktime(12, 0, 0, $month, $d, $month_year);
        if (date('m', $time) == $month) {
            array_push($footer_data['chart']['labels'],date('Y-m-d', $time));
        }
    }
    $chart_type = 'month';
} else {
    $_start_time = new DateTime(date('Y-m-d',strtotime($start_date)));
    $_end_time = new DateTime(date('Y-m-d',strtotime($end_date)));
    $interval = $_end_time->diff($_start_time);
    $difference = $interval->format('%m');
    if($difference == 0){
        $chart_type = 'month';
        $month = date('m',strtotime($start_date));
        $month_year = date('Y',strtotime($start_date));
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $month_year);
            if (date('m', $time) == $month) {
                array_push($footer_data['chart']['labels'],date('Y-m-d', $time));
            }
        }
    } else {
        $chart_type = 'weeks_split';
        $weeks = get_weekdays_between_dates($_start_time,$_end_time);
        $total_weeks = count($weeks);
        for($i = 1; $i<=$total_weeks;$i++){
            array_push($footer_data['chart']['labels'],split_weeks_chart_label($weeks,$i));
        }
    }
}

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        if ($aColumns[$i] == 'name') {
            $_data = '<a href="'.admin_url('tasks/view/'.$aRow['task_id']).'" onclick="init_task_modal(' . $aRow['task_id'] . '); return false;">' . $aRow['name'] . '</a>';
            $status = get_task_status_by_id($aRow['status']);
            $_data .= '<span class="hidden"> - </span><span class="inline-block pull-right mright5 label" style="border:1px solid '.$status['color'].';color:'.$status['color'].'" task-status-table="' . $aRow['status'] . '">' . $status['name'] . '</span>';
        } else if($aColumns[$i] == 'staff_id'){
            $_data = '<a href="'.admin_url('staff/member/'.$staff_id).'" target="_blank">'.get_staff_full_name($_data).'</a>';
        } else if ($aColumns[$i] == 'start_time' || $aColumns[$i] == 'end_time') {
            if ($aColumns[$i] == 'end_time' && $_data == NULL) {
                $_data = '';
            } else {
                $_data = strftime(get_current_date_format() . ' %H:%M', $_data);
            }
        } else if ($aColumns[$i] == 'rel_id') {
            $rel_data   = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
            $rel_values = get_relation_values($rel_data, $aRow['rel_type']);
            $_data      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
        } else if($i == $tags_column){
            $_data = render_tags($_data);
        } else {
            if ($i == $time_h_column) {
                $total_logged_time = 0;
                if ($_data == NULL) {
                    $total_logged_time = time() - $aRow['start_time'];
                } else {
                    $total_logged_time = $_data;
                }
                $_data = seconds_to_time_format($total_logged_time);
                $footer_data['total_logged_time_h'] += $total_logged_time;
            } else if($i == $time_d_column){

                $total_logged_time = 0;
                if ($_data == NULL) {
                    $total_logged_time = time() - $aRow['start_time'];
                } else {
                    $total_logged_time = $_data;
                }
                if($chart_type == 'today'){
                    array_push($footer_data['chart']['data'],$total_logged_time);
                } else if($chart_type == 'week'){
                    $weekday = date('N', $aRow['start_time']);
                    if(!isset($temp_weekdays_data[$weekday])){
                        $temp_weekdays_data[$weekday] = 0;
                    }
                    $temp_weekdays_data[$weekday] += $total_logged_time;
                } else if($chart_type == 'month') {
                    $month = intval(strftime('%d', $aRow['start_time']));

                    if(!isset($temp_months_data[$month])){
                        $temp_months_data[$month] = 0;
                    }

                    $temp_months_data[$month] += $total_logged_time;

                } else if($chart_type == 'weeks_split'){

                    $w = 1;
                    foreach($weeks as $week){
                        $start_time_date = strftime('%Y-%m-%d', $aRow['start_time']);
                        if(!isset($weeks[$w]['total'])) {
                           $weeks[$w]['total'] = 0;
                        }
                        if(in_array($start_time_date, $week)){
                            $weeks[$w]['total'] += $total_logged_time;
                        }
                        $w++;
                    }
                }
                $footer_data['total_logged_time_d'] += $total_logged_time;
                $_data = sec2qty($total_logged_time);
            }
        }
        $row[] = $_data;
    }

    $output['aaData'][]    = $row;
}

if($chart_type == 'today'){
    $footer_data['chart']['data'] = array(sec2qty(array_sum($footer_data['chart']['data'])));
} else if($chart_type == 'week'){
    ksort($temp_weekdays_data);
    for($i = 1;$i<=7;$i++){
        $total_logged_time = 0;
        if(isset($temp_weekdays_data[$i])){
            $total_logged_time = $temp_weekdays_data[$i];
        }
        array_push($footer_data['chart']['data'],sec2qty($total_logged_time));
    }
} else if($chart_type == 'month') {

    ksort($temp_months_data);

    for($i = 1;$i<=31;$i++){
        $total_logged_time = 0;
        if(isset($temp_months_data[$i])){
            $total_logged_time = $temp_months_data[$i];
        }
        array_push($footer_data['chart']['data'],sec2qty($total_logged_time));
    }

} else if($chart_type == 'weeks_split'){

    foreach($weeks as $week){
        $total = 0;
        if(isset($week['total'])){
            $total = $week['total'];
        }
        $total_logged_time = $total;
        array_push($footer_data['chart']['data'],sec2qty($total_logged_time));
    }
}

$output['chart'] = $footer_data['chart'];
$output['chart_type'] = $chart_type;
unset($footer_data['chart']);

$footer_data['total_logged_time_h'] = seconds_to_time_format($footer_data['total_logged_time_h']);
$footer_data['total_logged_time_d'] = sec2qty($footer_data['total_logged_time_d']);
$output['logged_time'] = $footer_data;

