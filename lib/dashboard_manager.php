<?php

function get_dashboard_summary() {
    $stats = get_project_stats();
    return [
        'total_projects' => $stats['total'],
        'active_projects' => $stats['active'],
        'completion_rate' => round($stats['completion_rate']*100)
    ];
}

function get_project_stats() {
    return [
        'total' => 15,
        'active' => 8,
        'completed' => 7,
        'completion_rate' => 0.47
    ];
}

// 获取紧急生产指令 (模拟数据)
function get_urgent_orders() {
    return [
        ['product_name' => '产品A', 'quantity' => 100],
        ['product_name' => '产品B', 'quantity' => 50],
        ['product_name' => '产品C', 'quantity' => 200]
    ];
}

// 获取最近审批记录 (模拟数据)
function get_recent_approvals() {
    return [
        ['record_type' => '合同', 'record_id' => 1001, 'approval_result' => true],
        ['record_type' => '订单', 'record_id' => 2005, 'approval_result' => false],
        ['record_type' => '项目', 'record_id' => 3002, 'approval_result' => true]
    ];
}
