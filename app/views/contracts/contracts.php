<?php
require_once '../../../lib/contract_manager.php';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        add_contract($_POST);
        header("Location: contracts.php");
        exit;
    } elseif (isset($_POST['update'])) {
        update_contract($_POST['id'], $_POST);
        header("Location: contracts.php");
        exit;
    }
}

// 处理删除请求
if (isset($_GET['delete'])) {
    delete_contract($_GET['id']);
    header("Location: contracts.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
$per_page = 10;

// 获取有效合同列表
$result = get_active_contracts($search, $page, $per_page);
$contracts = $result['data'];
$total_pages = ceil($result['total'] / $per_page);

// 获取已删除合同(回收站)
if (isset($_GET['show_deleted'])) {
    $deleted_result = get_contracts($search, $page, $per_page);
    $deleted_contracts = $deleted_result['data'];
}
$edit_contract = isset($_GET['edit']) ? get_contract($_GET['id']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>合同管理系统</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .form-group { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>合同管理</h1>
    
    <!-- 合同表单 -->
    <form method="post">
        <?php if ($edit_contract): ?>
            <input type="hidden" name="id" value="<?= $edit_contract['id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>合同编号:</label>
            <input type="text" name="contract_no" required 
                   value="<?= $edit_contract ? $edit_contract['contract_no'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>合同名称:</label>
            <input type="text" name="contract_name" required 
                   value="<?= $edit_contract ? $edit_contract['contract_name'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>合同类型:</label>
            <select name="contract_type">
                <option value="SALES" <?= $edit_contract && $edit_contract['contract_type'] == 'SALES' ? 'selected' : '' ?>>销售合同</option>
                <option value="PURCHASE" <?= $edit_contract && $edit_contract['contract_type'] == 'PURCHASE' ? 'selected' : '' ?>>采购合同</option>
                <option value="SERVICE" <?= $edit_contract && $edit_contract['contract_type'] == 'SERVICE' ? 'selected' : '' ?>>服务合同</option>
                <option value="OTHER" <?= $edit_contract && $edit_contract['contract_type'] == 'OTHER' ? 'selected' : '' ?>>其他合同</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>客户名称:</label>
            <input type="text" name="client_name" required
                   value="<?= $edit_contract ? $edit_contract['client_name'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>合同金额:</label>
            <input type="number" step="0.01" name="amount"
                   value="<?= $edit_contract ? $edit_contract['amount'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>付款条件:</label>
            <textarea name="payment_terms"><?= $edit_contract ? $edit_contract['payment_terms'] : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label>交付条件:</label>
            <textarea name="delivery_terms"><?= $edit_contract ? $edit_contract['delivery_terms'] : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label>签订日期:</label>
            <input type="date" name="sign_date"
                   value="<?= $edit_contract ? $edit_contract['sign_date'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>签订人:</label>
            <input type="text" name="signed_by"
                   value="<?= $edit_contract ? $edit_contract['signed_by'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>生效日期:</label>
            <input type="date" name="effective_date"
                   value="<?= $edit_contract ? $edit_contract['effective_date'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>到期日期:</label>
            <input type="date" name="expiry_date"
                   value="<?= $edit_contract ? $edit_contract['expiry_date'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>附件路径:</label>
            <input type="text" name="file_path"
                   value="<?= $edit_contract ? $edit_contract['file_path'] : '' ?>">
        </div>
        
        <div class="form-group">
            <label>备注:</label>
            <textarea name="remark"><?= $edit_contract ? $edit_contract['remark'] : '' ?></textarea>
        </div>
        
        <button type="submit" name="<?= $edit_contract ? 'update' : 'add' ?>">
            <?= $edit_contract ? '更新合同' : '添加合同' ?>
        </button>
        
        <?php if ($edit_contract): ?>
            <a href="contracts.php">取消编辑</a>
        <?php endif; ?>
    </form>
    
    <!-- 搜索框 -->
    <form method="get" style="margin: 20px 0;">
        <input type="text" name="search" placeholder="搜索合同编号或客户名称" 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">搜索</button>
        <a href="contracts.php">重置</a>
    </form>

    <!-- 合同列表 -->
    <h2>合同列表</h2>
    <table>
        <tr>
            <th>合同编号</th>
            <th>合同名称</th>
            <th>合同类型</th>
            <th>客户名称</th>
            <th>合同金额</th>
            <th>签订日期</th>
            <th>生效日期</th>
            <th>到期日期</th>
            <th>审批状态</th>
            <th>操作</th>
        </tr>
        <?php foreach ($contracts as $contract): ?>
        <tr>
            <td><?= $contract['contract_no'] ?></td>
            <td><?= isset($contract['contract_name']) ? $contract['contract_name'] : '-' ?></td>
            <td>
                <?php 
                $contract_types = [
                    'SALES' => '销售合同',
                    'PURCHASE' => '采购合同',
                    'SERVICE' => '服务合同',
                    'OTHER' => '其他合同'
                ];
                echo isset($contract_types[$contract['contract_type']]) ? $contract_types[$contract['contract_type']] : $contract['contract_type'];
                ?>
            </td>
            <td><?= $contract['client_name'] ?></td>
            <td><?= number_format($contract['amount'], 2) ?></td>
            <td><?= $contract['sign_date'] ?></td>
            <td><?= isset($contract['effective_date']) ? $contract['effective_date'] : '-' ?></td>
            <td><?= isset($contract['expiry_date']) ? $contract['expiry_date'] : '-' ?></td>
            <td>
                <?php 
                $status_labels = [
                    'DRAFT' => '<span style="color: #888;">草稿</span>',
                    'PENDING' => '<span style="color: #f90;">审批中</span>',
                    'APPROVED' => '<span style="color: #090;">已审批</span>',
                    'REJECTED' => '<span style="color: #f00;">已拒绝</span>'
                ];
                echo isset($status_labels[$contract['approval_status']]) ? $status_labels[$contract['approval_status']] : '草稿';
                ?>
            </td>
            <td>
                <a href="contract_details.php?contract_id=<?= $contract['id'] ?>">详情</a>
                <a href="contracts.php?edit=1&id=<?= $contract['id'] ?>">编辑</a>
                <a href="contracts.php?delete=1&id=<?= $contract['id'] ?>" 
                   onclick="return confirm('确定要作废此合同吗？')">作废</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- 分页控件 -->
    <div style="margin-top: 20px;">
        <?php if ($page > 1): ?>
            <a href="contracts.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">上一页</a>
        <?php endif; ?>
        
        <span>第 <?= $page ?> 页/共 <?= $total_pages ?> 页</span>
        
        <?php if ($page < $total_pages): ?>
            <a href="contracts.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">下一页</a>
        <?php endif; ?>
    </div>
</body>
</html>
