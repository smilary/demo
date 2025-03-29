<?php
session_start();

// 检查是否为POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Method Not Allowed');
}

// 检查CSRF Token
if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    exit('Invalid CSRF Token');
}

// 检查是否提供了标签ID
if (!isset($_POST['tab_id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Missing tab_id parameter');
}

$tab_id = $_POST['tab_id'];

// 初始化响应数组
$response = [
    'success' => false,
    'message' => '',
    'redirect_url' => ''
];

// 检查标签会话是否存在
if (!isset($_SESSION['tabs']) || empty($_SESSION['tabs'])) {
    $response['message'] = '没有可用的标签';
    echo json_encode($response);
    exit;
}

// 查找标签并设置为活动状态
$found = false;
foreach ($_SESSION['tabs'] as $key => $tab) {
    if ($tab['id'] === $tab_id) {
        $_SESSION['tabs'][$key]['active'] = true;
        $response['success'] = true;
        $response['message'] = '标签已激活';
        $response['redirect_url'] = $tab['url'];
        $found = true;
    } else {
        $_SESSION['tabs'][$key]['active'] = false;
    }
}

if (!$found) {
    $response['message'] = '标签不存在';
}

// 返回JSON响应
header('Content-Type: application/json');
echo json_encode($response);