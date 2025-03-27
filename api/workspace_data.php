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

// 获取用户ID
$user_id = $_SESSION['user_id'];

// 获取带缓存的数据
$data = [];

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