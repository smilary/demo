<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 检查CSRF Token
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

// 获取要关闭的标签ID
$tab_id = isset($_POST['tab_id']) ? $_POST['tab_id'] : '';

if (empty($tab_id) || !isset($_SESSION['tabs'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// 查找要关闭的标签
$tab_index = -1;
$is_active_tab = false;
$active_tab_index = -1;

foreach ($_SESSION['tabs'] as $index => $tab) {
    if ($tab['id'] === $tab_id) {
        $tab_index = $index;
        $is_active_tab = isset($tab['active']) && $tab['active'];
    }
    
    // 记录当前激活的标签索引
    if (isset($tab['active']) && $tab['active']) {
        $active_tab_index = $index;
    }
}

// 如果找到了标签，则移除它
if ($tab_index >= 0) {
    // 移除标签
    array_splice($_SESSION['tabs'], $tab_index, 1);
    
    // 如果关闭的是当前激活的标签，则激活前一个标签
    if ($is_active_tab && count($_SESSION['tabs']) > 0) {
        // 确定要激活的标签索引
        $new_active_index = min($tab_index, count($_SESSION['tabs']) - 1);
        
        // 重置所有标签的激活状态
        foreach ($_SESSION['tabs'] as $index => $tab) {
            $_SESSION['tabs'][$index]['active'] = ($index == $new_active_index);
        }
        
        // 返回重定向URL
        header('Content-Type: application/json');
        echo json_encode(['redirect' => $_SESSION['tabs'][$new_active_index]['url']]);
        exit;
    }
}

// 返回成功响应
header('Content-Type: application/json');
echo json_encode(['success' => true]);