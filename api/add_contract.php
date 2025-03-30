<?php
session_start();
require_once __DIR__.'/../config/db_connect.php';
require_once __DIR__.'/../lib/contract_manager.php';

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

// 获取表单数据
$required_fields = ['contract_no', 'contract_name', 'client_name', 'amount', 'sign_date'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Missing required fields',
        'fields' => $missing_fields
    ]);
    exit;
}

// 准备合同数据
$contract_data = [
    'contract_no' => $_POST['contract_no'],
    'contract_name' => $_POST['contract_name'],
    'contract_type' => isset($_POST['contract_type']) ? $_POST['contract_type'] : 'SALES',
    'client_name' => $_POST['client_name'],
    'amount' => $_POST['amount'],
    'payment_terms' => isset($_POST['payment_terms']) ? $_POST['payment_terms'] : null,
    'delivery_terms' => isset($_POST['delivery_terms']) ? $_POST['delivery_terms'] : null,
    'sign_date' => $_POST['sign_date'],
    'signed_by' => isset($_POST['signed_by']) ? $_POST['signed_by'] : null,
    'effective_date' => isset($_POST['effective_date']) ? $_POST['effective_date'] : null,
    'expiry_date' => isset($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
    'remark' => isset($_POST['remark']) ? $_POST['remark'] : null
];

try {
    // 添加合同
    $contract_id = add_contract($contract_data);
    
    if ($contract_id) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '合同添加成功',
            'contract_id' => $contract_id,
            'redirect' => 'main.php?view=contract_details&contract_id=' . $contract_id
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => '合同添加失败',
            'message' => '数据库操作失败'
        ]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => '合同添加失败',
        'message' => $e->getMessage()
    ]);
}