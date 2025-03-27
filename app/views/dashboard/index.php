<?php
// 个人工作台入口文件
require_once __DIR__.'/../../../lib/dashboard_manager.php';

// 设置页面标题
$page_title = '个人工作台 - 项目管理系统';

// 设置激活菜单
$active_menu = '';

// 设置额外CSS文件
$extra_css = ['styles/workspace.css'];

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

// 设置内容视图
$content_view = __DIR__.'/workspace.php';

// 包含布局模板
require_once __DIR__.'/../templates/layout.php';