<?php
session_start();
require_once __DIR__.'/../config/db_connect.php';
require_once __DIR__.'/../lib/dashboard_manager.php';

// 检查AJAX请求
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

// 验证CSRF Token
if (empty($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

// 获取带缓存的数据
$data = [];

// 项目统计数据
$cache_key = 'dashboard_stats_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key])) {
    $_SESSION[$cache_key] = [
        'data' => get_project_stats(),
        'timestamp' => time()
    ];
}
$data['stats'] = $_SESSION[$cache_key]['data'];

// 紧急订单
$cache_key = 'urgent_orders_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key])) {
    $_SESSION[$cache_key] = [
        'data' => get_urgent_orders(),
        'timestamp' => time()
    ];
}
$data['orders'] = $_SESSION[$cache_key]['data'];

// 最近审批
$cache_key = 'recent_approvals_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key])) {
    $_SESSION[$cache_key] = [
        'data' => get_recent_approvals(),
        'timestamp' => time()
    ];
}
$data['approvals'] = $_SESSION[$cache_key]['data'];

// 返回JSON数据
header('Content-Type: application/json');
echo json_encode($data);
