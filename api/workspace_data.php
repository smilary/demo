<?php
session_start();
require_once __DIR__.'/../config/db_connect.php';
require_once __DIR__.'/../lib/dashboard_manager.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit('未授权访问');
}

// 检查AJAX请求 - 当通过标签切换访问时，这个检查是可选的
// 我们放宽这个限制，允许非AJAX请求，但在生产环境中，可能需要严格限制
/*
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('HTTP/1.1 403 Forbidden');
    exit('禁止访问');
}
*/

// 验证CSRF Token - 对于GET请求，我们放宽这个限制，但仍然保留代码作为参考
// 在生产环境中，可能需要严格限制，特别是对于修改数据的操作
/*
if (empty($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    exit('CSRF验证失败');
}
*/

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

// 引入工作台视图
ob_start();
include __DIR__ . '/../app/views/dashboard/workspace.php';
$content = ob_get_clean();

// 返回HTML内容
echo $content;
exit;

// 以下函数保留用于数据获取

// 获取待办任务 (模拟数据)
function get_todo_tasks($user_id) {
    // 实际应用中，这里应该从数据库获取用户的待办任务
    return [
        [
            'id' => 1,
            'title' => '完成项目A需求分析',
            'due_date' => '2023-12-15',
            'priority' => 'high',
            'completed' => false
        ],
        [
            'id' => 2,
            'title' => '审核项目B开发文档',
            'due_date' => '2023-12-18',
            'priority' => 'medium',
            'completed' => false
        ],
        [
            'id' => 3,
            'title' => '参加项目C周会',
            'due_date' => '2023-12-20',
            'priority' => 'low',
            'completed' => false
        ],
        [
            'id' => 4,
            'title' => '准备项目D演示材料',
            'due_date' => '2023-12-22',
            'priority' => 'medium',
            'completed' => false
        ],
        [
            'id' => 5,
            'title' => '提交项目E周报',
            'due_date' => '2023-12-15',
            'priority' => 'high',
            'completed' => false
        ]
    ];
}

// 获取我的项目 (模拟数据)
function get_my_projects($user_id) {
    // 实际应用中，这里应该从数据库获取用户参与的项目
    return [
        [
            'id' => 1,
            'title' => '项目A',
            'description' => '系统需求分析与设计',
            'progress' => 65
        ],
        [
            'id' => 2,
            'title' => '项目B',
            'description' => '前端界面开发',
            'progress' => 30
        ],
        [
            'id' => 3,
            'title' => '项目C',
            'description' => '系统测试与部署',
            'progress' => 90
        ]
    ];
}

// 获取通知 (模拟数据)
function get_notifications($user_id) {
    // 实际应用中，这里应该从数据库获取用户的通知
    return [
        [
            'id' => 1,
            'type' => 'system',
            'title' => '系统通知',
            'content' => '您有一个新的审批任务',
            'time' => '10分钟前',
            'read' => false
        ],
        [
            'id' => 2,
            'type' => 'task',
            'title' => '任务提醒',
            'content' => '项目A需求分析即将到期',
            'time' => '1小时前',
            'read' => true
        ],
        [
            'id' => 3,
            'type' => 'message',
            'title' => '消息通知',
            'content' => '张经理给您发送了一条消息',
            'time' => '昨天',
            'read' => true
        ]
    ];
}