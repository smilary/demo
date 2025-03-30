<?php
// 设置页面标题
$page_title = '服务确认 - 项目管理系统';

// 设置激活菜单
$active_menu = '合同管理';

// 设置额外CSS文件
$extra_css = ['styles/function_pages.css', 'styles/confirmation_pages.css'];

// 设置内容视图
$content_view = __FILE__;

// 如果是直接访问此文件，则包含布局模板
if (!defined('INCLUDED_IN_LAYOUT')) {
    define('INCLUDED_IN_LAYOUT', true);
    require_once __DIR__.'/../templates/layout.php';
    exit;
}

// 引入合同管理库
require_once __DIR__.'/../../../lib/contract_manager.php';

// 获取合同ID
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;

// 获取服务ID和操作类型
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_service') {
        $data = [
            'contract_id' => $contract_id,
            'service_item' => $_POST['service_item'],
            'service_start_date' => $_POST['service_start_date'],
            'service_end_date' => $_POST['service_end_date'],
            'status' => $_POST['status'],
            'service_description' => isset($_POST['service_description']) ? $_POST['service_description'] : '',
            'confirmed_by' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1
        ];
        
        if (isset($_POST['service_id']) && $_POST['service_id'] > 0) {
            // 更新服务确认
            update_service_confirmation($_POST['service_id'], $data);
        } else {
            // 添加服务确认
            add_service_confirmation($data);
        }
        
        // 重定向到服务确认列表
        header("Location: main.php?view=service_confirmation&contract_id=$contract_id");
        exit;
    }
}

// 处理删除操作
if ($action === 'delete' && $service_id > 0) {
    delete_service_confirmation($service_id);
    header("Location: main.php?view=service_confirmation&contract_id=$contract_id");
    exit;
}

// 获取编辑的服务确认信息
$edit_service = null;
if ($action === 'edit' && $service_id > 0) {
    $edit_service = get_service_confirmation($service_id);
}

// 获取服务确认列表
$service_confirmations = get_service_confirmations($contract_id);
?>

<div class="confirmation-container">
    <div class="breadcrumb">
        <a href="main.php?view=contracts">合同管理</a> &gt; 
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=service">合同详情</a> &gt; 
        服务确认
    </div>

    <h2>服务确认列表</h2>
    
    <div class="confirmation-actions">
        <button class="btn-add" onclick="showAddServiceConfirmation()">添加服务确认</button>
        <button class="btn-secondary" onclick="backToContractDetails()">返回合同详情</button>
    </div>

    <div class="confirmation-list">
        <table class="confirmation-table">
            <thead>
                <tr>
                    <th>确认编号</th>
                    <th>服务项目</th>
                    <th>确认状态</th>
                    <th>确认时间</th>
                    <th>确认人</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($service_confirmations as $confirmation): ?>
                <tr>
                    <td><?= htmlspecialchars($confirmation['confirmation_no']) ?></td>
                    <td><?= htmlspecialchars($confirmation['service_item']) ?></td>
                    <td><?= htmlspecialchars($confirmation['status']) ?></td>
                    <td><?= htmlspecialchars($confirmation['confirm_time']) ?></td>
                    <td><?= htmlspecialchars($confirmation['confirmer']) ?></td>
                    <td>
                        <button class="btn-view" onclick="viewServiceConfirmation(<?= $confirmation['id'] ?>)">查看</button>
                        <?php if ($confirmation['status'] !== '已确认'): ?>
                        <button class="btn-edit" onclick="editServiceConfirmation(<?= $confirmation['id'] ?>)">编辑</button>
                        <button class="btn-delete" onclick="deleteServiceConfirmation(<?= $confirmation['id'] ?>)">删除</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 添加/编辑服务确认表单 -->
<div id="serviceForm" class="confirmation-form" style="display: <?= $edit_service ? 'block' : 'none' ?>">
    <h3><?= $edit_service ? '编辑服务确认' : '添加服务确认' ?></h3>
    <form method="post" action="main.php?view=service_confirmation">
        <input type="hidden" name="contract_id" value="<?= $contract_id ?>">
        <input type="hidden" name="action" value="save_service">
        <?php if ($edit_service): ?>
        <input type="hidden" name="service_id" value="<?= $edit_service['id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>服务项目:</label>
            <input type="text" name="service_item" value="<?= $edit_service ? htmlspecialchars($edit_service['service_item']) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>服务开始日期:</label>
            <input type="date" name="service_start_date" value="<?= $edit_service ? htmlspecialchars($edit_service['service_start_date']) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>服务结束日期:</label>
            <input type="date" name="service_end_date" value="<?= $edit_service ? htmlspecialchars($edit_service['service_end_date']) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>确认状态:</label>
            <select name="status" required>
                <option value="待确认" <?= $edit_service && $edit_service['status'] == '待确认' ? 'selected' : '' ?>>待确认</option>
                <option value="已确认" <?= $edit_service && $edit_service['status'] == '已确认' ? 'selected' : '' ?>>已确认</option>
                <option value="部分确认" <?= $edit_service && $edit_service['status'] == '部分确认' ? 'selected' : '' ?>>部分确认</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>服务内容描述:</label>
            <textarea name="service_description"><?= $edit_service ? htmlspecialchars($edit_service['service_description']) : '' ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">保存</button>
            <button type="button" class="btn-secondary" onclick="hideServiceForm()">取消</button>
        </div>
    </form>
</div>

<script src="../../../js/service_confirmation.js"></script>