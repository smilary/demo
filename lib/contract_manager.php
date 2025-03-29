<?php
require_once __DIR__.'/../config/db_connect.php';

// 合同列表(支持搜索和分页)
function get_contracts($search = '', $page = 1, $per_page = 10) {
    global $db;
    
    $offset = ($page - 1) * $per_page;
    $params = [];
    $where = '';
    
    if (!empty($search)) {
        $where = "WHERE (contract_no LIKE ? OR client_name LIKE ?)";
        $params = ["%$search%", "%$search%"];
    }
    
    // 获取总数
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM contracts $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetchColumn();
    
    // 获取分页数据
    $stmt = $db->prepare("SELECT * FROM contracts $where 
                         ORDER BY created_at DESC 
                         LIMIT $per_page OFFSET $offset");
    $stmt->execute($params);
    
    return [
        'data' => $stmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page
    ];
}

// 添加合同
function add_contract($data) {
    global $db;
    
    $stmt = $db->prepare("INSERT INTO contracts 
                         (contract_no, contract_type, client_name, amount, payment_terms, delivery_terms, 
                          sign_date, signed_by, effective_date, expiry_date, file_path, remark, contract_name)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['contract_no'],
        isset($data['contract_type']) ? $data['contract_type'] : 'SALES',
        $data['client_name'],
        $data['amount'],
        isset($data['payment_terms']) ? $data['payment_terms'] : null,
        isset($data['delivery_terms']) ? $data['delivery_terms'] : null,
        $data['sign_date'],
        isset($data['signed_by']) ? $data['signed_by'] : null,
        isset($data['effective_date']) ? $data['effective_date'] : null,
        isset($data['expiry_date']) ? $data['expiry_date'] : null,
        isset($data['file_path']) ? $data['file_path'] : null,
        isset($data['remark']) ? $data['remark'] : null,
        isset($data['contract_name']) ? $data['contract_name'] : null
    ]);
    
    return $db->lastInsertId();
}

// 获取单个合同
function get_contract($id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contracts WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// 更新合同
function update_contract($id, $data) {
    global $db;
    
    $stmt = $db->prepare("UPDATE contracts SET 
                         contract_no = ?, 
                         contract_type = ?,
                         client_name = ?,
                         amount = ?,
                         payment_terms = ?,
                         delivery_terms = ?,
                         sign_date = ?,
                         signed_by = ?,
                         effective_date = ?,
                         expiry_date = ?,
                         file_path = ?,
                         remark = ?,
                         contract_name = ?
                         WHERE id = ?");
    $stmt->execute([
        $data['contract_no'],
        isset($data['contract_type']) ? $data['contract_type'] : 'SALES',
        $data['client_name'],
        $data['amount'],
        isset($data['payment_terms']) ? $data['payment_terms'] : null,
        isset($data['delivery_terms']) ? $data['delivery_terms'] : null,
        $data['sign_date'],
        isset($data['signed_by']) ? $data['signed_by'] : null,
        isset($data['effective_date']) ? $data['effective_date'] : null,
        isset($data['expiry_date']) ? $data['expiry_date'] : null,
        isset($data['file_path']) ? $data['file_path'] : null,
        isset($data['remark']) ? $data['remark'] : null,
        isset($data['contract_name']) ? $data['contract_name'] : null,
        $id
    ]);
    
    return $stmt->rowCount();
}

// 软删除合同(标记为作废和删除状态)

// 添加项目需求
function add_requirement($project_id, $data) {
    global $db;
    
    $stmt = $db->prepare("INSERT INTO project_requirements 
                         (project_id, requirement_no, description, priority, status, created_by)
                         VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $project_id,
        $data['requirement_no'],
        $data['description'],
        $data['priority'],
        $data['status'],
        $data['created_by']
    ]);
    
    return $db->lastInsertId();
}

// 更新需求状态并记录日志
function update_requirement_status($requirement_id, $new_status, $user_id, $comments = null) {
    global $db;
    
    $db->beginTransaction();
    
    try {
        // 获取当前状态
        $stmt = $db->prepare("SELECT status FROM project_requirements WHERE id = ?");
        $stmt->execute([$requirement_id]);
        $old_status = $stmt->fetchColumn();
        
        // 更新需求状态
        $stmt = $db->prepare("UPDATE project_requirements SET 
                             status = ?, 
                             updated_at = CURRENT_TIMESTAMP
                             WHERE id = ?");
        $stmt->execute([$new_status, $requirement_id]);
        
        // 记录状态变更日志
        $stmt = $db->prepare("INSERT INTO requirement_status_logs 
                             (requirement_id, old_status, new_status, changed_by, comments)
                             VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $requirement_id,
            $old_status,
            $new_status,
            $user_id,
            $comments
        ]);
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

// 关联生产指令到需求
function link_production_order($requirement_id, $order_id) {
    global $db;
    
    $stmt = $db->prepare("UPDATE project_requirements SET 
                         production_order_id = ?, 
                         updated_at = CURRENT_TIMESTAMP
                         WHERE id = ?");
    return $stmt->execute([$order_id, $requirement_id]);
}

// 更新物流状态
function update_logistics_status($requirement_id, $status) {
    global $db;
    
    $stmt = $db->prepare("UPDATE project_requirements SET 
                         logistics_status = ?, 
                         updated_at = CURRENT_TIMESTAMP
                         WHERE id = ?");
    return $stmt->execute([$status, $requirement_id]);
}
function delete_contract($id) {
    global $db;
    
    $stmt = $db->prepare("UPDATE contracts SET 
                         is_deleted = 1,
                         deleted_at = CURRENT_TIMESTAMP
                         WHERE id = ?");
    return $stmt->execute([$id]);
}

// 获取合同明细列表
function get_contract_items($contract_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contract_items 
                         WHERE contract_id = ? 
                         ORDER BY item_no ASC");
    $stmt->execute([$contract_id]);
    return $stmt->fetchAll();
}

// 获取单个合同明细
function get_contract_item($item_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contract_items WHERE id = ?");
    $stmt->execute([$item_id]);
    return $stmt->fetch();
}

// 添加合同明细
function add_contract_item($contract_id, $data) {
    global $db;
    
    $stmt = $db->prepare("INSERT INTO contract_items 
                         (contract_id, item_no, model_type, material_name, material_code, 
                          specification, execution_standard, unit, quantity, unit_price, remark)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $contract_id,
        $data['item_no'],
        isset($data['model_type']) ? $data['model_type'] : null,
        $data['material_name'],
        isset($data['material_code']) ? $data['material_code'] : null,
        isset($data['specification']) ? $data['specification'] : null,
        isset($data['execution_standard']) ? $data['execution_standard'] : null,
        $data['unit'],
        $data['quantity'],
        isset($data['unit_price']) ? $data['unit_price'] : null,
        isset($data['remark']) ? $data['remark'] : null
    ]);
    
    return $db->lastInsertId();
}

// 更新合同明细
function update_contract_item($item_id, $data) {
    global $db;
    
    $stmt = $db->prepare("UPDATE contract_items SET 
                         item_no = ?, 
                         model_type = ?,
                         material_name = ?,
                         material_code = ?,
                         specification = ?,
                         execution_standard = ?,
                         unit = ?,
                         quantity = ?,
                         unit_price = ?,
                         remark = ?,
                         updated_at = CURRENT_TIMESTAMP
                         WHERE id = ?");
    $stmt->execute([
        $data['item_no'],
        isset($data['model_type']) ? $data['model_type'] : null,
        $data['material_name'],
        isset($data['material_code']) ? $data['material_code'] : null,
        isset($data['specification']) ? $data['specification'] : null,
        isset($data['execution_standard']) ? $data['execution_standard'] : null,
        $data['unit'],
        $data['quantity'],
        isset($data['unit_price']) ? $data['unit_price'] : null,
        isset($data['remark']) ? $data['remark'] : null,
        $item_id
    ]);
    
    return $stmt->rowCount();
}

// 删除合同明细
function delete_contract_item($item_id) {
    global $db;
    
    $stmt = $db->prepare("DELETE FROM contract_items WHERE id = ?");
    return $stmt->execute([$item_id]);
}

// 获取合同文件列表
function get_contract_files($contract_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contract_files 
                         WHERE contract_id = ? 
                         ORDER BY uploaded_at DESC");
    $stmt->execute([$contract_id]);
    return $stmt->fetchAll();
}

// 添加合同文件
function add_contract_file($contract_id, $data) {
    global $db;
    
    $stmt = $db->prepare("INSERT INTO contract_files 
                         (contract_id, file_name, file_path, file_type, file_size, uploaded_by)
                         VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $contract_id,
        $data['file_name'],
        $data['file_path'],
        isset($data['file_type']) ? $data['file_type'] : null,
        isset($data['file_size']) ? $data['file_size'] : null,
        $data['uploaded_by']
    ]);
    
    return $db->lastInsertId();
}

// 删除合同文件
function delete_contract_file($file_id) {
    global $db;
    
    // 先获取文件路径，以便删除实际文件
    $stmt = $db->prepare("SELECT file_path FROM contract_files WHERE id = ?");
    $stmt->execute([$file_id]);
    $file_path = $stmt->fetchColumn();
    
    // 删除数据库记录
    $stmt = $db->prepare("DELETE FROM contract_files WHERE id = ?");
    $result = $stmt->execute([$file_id]);
    
    // 如果数据库记录删除成功且文件存在，则删除实际文件
    if ($result && $file_path && file_exists($file_path)) {
        unlink($file_path);
    }
    
    return $result;
}

// 获取合同审批记录
function get_contract_approvals($contract_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT ca.*, u.real_name as approver_name 
                         FROM contract_approvals ca
                         LEFT JOIN users u ON ca.approver_id = u.id
                         WHERE ca.contract_id = ? 
                         ORDER BY ca.approval_level ASC, ca.created_at ASC");
    $stmt->execute([$contract_id]);
    return $stmt->fetchAll();
}

// 提交合同审批
function submit_contract_for_approval($contract_id, $user_id) {
    global $db;
    
    $db->beginTransaction();
    
    try {
        // 更新合同状态为待审批
        $stmt = $db->prepare("UPDATE contracts SET 
                             approval_status = 'PENDING'
                             WHERE id = ?");
        $stmt->execute([$contract_id]);
        
        // 获取审批人列表（这里简化处理，实际应根据审批流程配置获取）
        $approvers = get_contract_approvers();
        
        // 创建审批记录
        foreach ($approvers as $level => $approver_id) {
            $stmt = $db->prepare("INSERT INTO contract_approvals 
                                 (contract_id, approver_id, approval_level, status)
                                 VALUES (?, ?, ?, 'PENDING')");
            $stmt->execute([$contract_id, $approver_id, $level + 1]);
        }
        
        // 记录状态变更日志
        $stmt = $db->prepare("INSERT INTO contract_status_logs 
                             (contract_id, old_status, new_status, changed_by, comments)
                             VALUES (?, 'DRAFT', 'PENDING', ?, '提交审批')");
        $stmt->execute([$contract_id, $user_id]);
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

// 审批合同
function approve_contract($approval_id, $user_id, $status, $comments = null) {
    global $db;
    
    $db->beginTransaction();
    
    try {
        // 更新审批记录
        $stmt = $db->prepare("UPDATE contract_approvals SET 
                             status = ?,
                             comments = ?,
                             approved_at = CURRENT_TIMESTAMP
                             WHERE id = ? AND approver_id = ?");
        $stmt->execute([$status, $comments, $approval_id, $user_id]);
        
        // 获取审批记录信息
        $stmt = $db->prepare("SELECT contract_id, approval_level FROM contract_approvals WHERE id = ?");
        $stmt->execute([$approval_id]);
        $approval = $stmt->fetch();
        
        if ($status == 'REJECTED') {
            // 如果拒绝，直接更新合同状态为拒绝
            $stmt = $db->prepare("UPDATE contracts SET 
                                 approval_status = 'REJECTED'
                                 WHERE id = ?");
            $stmt->execute([$approval['contract_id']]);
            
            // 记录状态变更日志
            $stmt = $db->prepare("INSERT INTO contract_status_logs 
                                 (contract_id, old_status, new_status, changed_by, comments)
                                 VALUES (?, 'PENDING', 'REJECTED', ?, ?)");
            $stmt->execute([$approval['contract_id'], $user_id, $comments]);
        } else {
            // 检查是否所有审批都已通过
            $stmt = $db->prepare("SELECT COUNT(*) FROM contract_approvals 
                                 WHERE contract_id = ? AND status != 'APPROVED'");
            $stmt->execute([$approval['contract_id']]);
            $pending_count = $stmt->fetchColumn();
            
            if ($pending_count == 0) {
                // 所有审批都已通过，更新合同状态为已审批
                $stmt = $db->prepare("UPDATE contracts SET 
                                     approval_status = 'APPROVED'
                                     WHERE id = ?");
                $stmt->execute([$approval['contract_id']]);
                
                // 记录状态变更日志
                $stmt = $db->prepare("INSERT INTO contract_status_logs 
                                     (contract_id, old_status, new_status, changed_by, comments)
                                     VALUES (?, 'PENDING', 'APPROVED', ?, '审批通过')");
                $stmt->execute([$approval['contract_id'], $user_id]);
            }
        }
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

// 获取合同审批人列表（简化处理，实际应从配置或权限表获取）
function get_contract_approvers() {
    // 返回审批人ID列表，键为审批级别（从0开始），值为用户ID
    // 这里简化处理，实际应根据审批流程配置获取
    return [1]; // 假设ID为1的用户是审批人
}

// 获取有效合同(排除已删除的)
function get_active_contracts($search = '', $page = 1, $per_page = 10) {
    global $db;
    
    $offset = ($page - 1) * $per_page;
    $params = [];
    $where = "WHERE is_deleted = 0";
    
    if (!empty($search)) {
        $where .= " AND (contract_no LIKE ? OR client_name LIKE ?)";
        $params = ["%$search%", "%$search%"];
    }
    
    // 获取总数
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM contracts $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetchColumn();
    
    // 获取分页数据
    $stmt = $db->prepare("SELECT * FROM contracts $where 
                         ORDER BY created_at DESC 
                         LIMIT $per_page OFFSET $offset");
    $stmt->execute($params);
    
    return [
        'data' => $stmt->fetchAll(),
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page
    ];
}
// 获取合同里程碑列表
function get_contract_milestones($contract_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contract_milestones 
                         WHERE contract_id = ? 
                         ORDER BY completion_date ASC");
    $stmt->execute([$contract_id]);
    return $stmt->fetchAll();
}

// 获取合同服务确认列表
function get_contract_services($contract_id) {
    global $db;
    
    $stmt = $db->prepare("SELECT * FROM contract_services 
                         WHERE contract_id = ? 
                         ORDER BY service_start_date ASC");
    $stmt->execute([$contract_id]);
    return $stmt->fetchAll();
}

// 获取项目统计数据
function get_project_stats() {
    global $db;
    
    $stats = [
        'total' => 0,
        'active' => 0,
        'completed' => 0,
        'completion_rate' => 0
    ];
    
    // 获取总项目数
    $stmt = $db->query("SELECT COUNT(*) FROM projects");
    $stats['total'] = $stmt->fetchColumn();
    
    // 获取进行中项目数
    $stmt = $db->query("SELECT COUNT(*) FROM projects WHERE status = 'active'");
    $stats['active'] = $stmt->fetchColumn();
    
    // 获取已完成项目数
    $stmt = $db->query("SELECT COUNT(*) FROM projects WHERE status = 'completed'");
    $stats['completed'] = $stmt->fetchColumn();
    
    // 计算完成率
    if ($stats['total'] > 0) {
        $stats['completion_rate'] = $stats['completed'] / $stats['total'];
    }
    
    return $stats;
}

// 获取紧急生产指令
function get_urgent_orders() {
    global $db;
    
    $stmt = $db->query("SELECT * FROM production_orders 
                       WHERE priority = 'urgent' 
                       AND status = 'pending'
                       ORDER BY deadline ASC
                       LIMIT 5");
    return $stmt->fetchAll();
}

// 获取最近审批记录
function get_recent_approvals() {
    global $db;
    
    $stmt = $db->query("SELECT * FROM approval_records
                       ORDER BY approval_time DESC
                       LIMIT 5");
    return $stmt->fetchAll();
}

?>
