<?php
session_start();
require_once __DIR__.'/config/db_connect.php';
require_once __DIR__.'/lib/dashboard_manager.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// 生成CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

// 获取项目统计数据(带缓存)
$cache_key = 'dashboard_stats_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 300)) {
    $_SESSION[$cache_key] = [
        'data' => get_project_stats(),
        'timestamp' => time()
    ];
}
$stats = $_SESSION[$cache_key]['data'];

// 获取紧急订单(带缓存)
$cache_key = 'urgent_orders_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 180)) {
    $_SESSION[$cache_key] = [
        'data' => get_urgent_orders(),
        'timestamp' => time()
    ];
}
$orders = $_SESSION[$cache_key]['data'];

// 获取最近审批(带缓存)
$cache_key = 'recent_approvals_' . $_SESSION['user_id'];
if (!isset($_SESSION[$cache_key]) || (time() - $_SESSION[$cache_key]['timestamp'] > 180)) {
    $_SESSION[$cache_key] = [
        'data' => get_recent_approvals(),
        'timestamp' => time()
    ];
}
$approvals = $_SESSION[$cache_key]['data'];

// 设置内容视图路径
$content_view = __DIR__.'/app/views/dashboard/workspace.php';

// 设置页面标题
$page_title = '个人工作台 - 项目管理系统';

// 设置激活菜单
$active_menu = '';

// 设置额外CSS文件
$extra_css = ['styles/workspace.css'];

// 包含布局模板
require_once __DIR__.'/app/views/templates/layout.php';