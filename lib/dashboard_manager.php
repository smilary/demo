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

/**
 * 获取服务确认列表
 * @param array $params 查询参数
 * @return array 服务确认数据列表
 */
function get_service_confirmations($params = []) {
    // 模拟数据返回，实际项目中应该从数据库查询
    return [
        [
            'id' => 1,
            'contract_no' => 'HT202401001',
            'service_type' => '现场服务',
            'service_date' => '2024-01-15',
            'customer_name' => '客户A',
            'status' => '待确认',
            'created_at' => '2024-01-10 10:00:00'
        ],
        [
            'id' => 2,
            'contract_no' => 'HT202401002',
            'service_type' => '远程支持',
            'service_date' => '2024-01-16',
            'customer_name' => '客户B',
            'status' => '已确认',
            'created_at' => '2024-01-11 14:30:00'
        ],
        [
            'id' => 3,
            'contract_no' => 'HT202401003',
            'service_type' => '设备维护',
            'service_date' => '2024-01-17',
            'customer_name' => '客户C',
            'status' => '待确认',
            'created_at' => '2024-01-12 09:15:00'
        ]
    ];
}

/**
 * 获取服务确认详情
 * @param int $id 服务确认ID
 * @return array|null 服务确认详情
 */
function get_service_confirmation_detail($id) {
    $list = get_service_confirmations();
    foreach ($list as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}
