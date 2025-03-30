<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 检查CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// 获取标签信息
$title = isset($_POST['title']) ? $_POST['title'] : '';
$url = isset($_POST['url']) ? $_POST['url'] : '';
$tab_id = isset($_POST['tab_id']) ? $_POST['tab_id'] : 'tab_' . uniqid();

if (empty($title) || empty($url)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// 初始化标签会话存储
if (!isset($_SESSION['tabs'])) {
    $_SESSION['tabs'] = [];
}

// 检查标签是否已存在
$tab_exists = false;
$tab_index = -1;

foreach ($_SESSION['tabs'] as $index => $tab) {
    if ($tab['url'] == $url) {
        $tab_exists = true;
        $tab_index = $index;
        break;
    }
}

// 重置所有标签的激活状态
foreach ($_SESSION['tabs'] as $index => $tab) {
    $_SESSION['tabs'][$index]['active'] = false;
}

// 如果标签已存在，则激活它
if ($tab_exists) {
    $_SESSION['tabs'][$tab_index]['active'] = true;
    $_SESSION['tabs'][$tab_index]['id'] = $tab_id;
} else {
    // 否则，创建新标签
    $_SESSION['tabs'][] = [
        'id' => $tab_id,
        'title' => $title,
        'url' => $url,
        'active' => true
    ];
}

// 返回成功响应
header('Content-Type: application/json');
echo json_encode(['success' => true, 'tab_id' => $tab_id]);