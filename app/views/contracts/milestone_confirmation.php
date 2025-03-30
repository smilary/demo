<?php
// 设置页面标题
$page_title = '里程碑确认 - 项目管理系统';

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

// 获取里程碑确认列表
$milestone_confirmations = get_milestone_confirmations($contract_id);
?>

<div class="confirmation-container">
    <div class="breadcrumb">
        <a href="main.php?view=contracts">合同管理</a> &gt; 
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=milestone">合同详情</a> &gt; 
        里程碑确认
    </div>

    <h2>里程碑确认列表</h2>
    
    <div class="confirmation-actions">
        <button class="btn-add" onclick="showAddMilestoneConfirmation()">添加里程碑确认</button>
        <button class="btn-secondary" onclick="backToContractDetails()">返回合同详情</button>
    </div>

    <div class="confirmation-list">
        <table class="confirmation-table">
            <thead>
                <tr>
                    <th>确认编号</th>
                    <th>里程碑名称</th>
                    <th>计划完成时间</th>
                    <th>实际完成时间</th>
                    <th>确认状态</th>
                    <th>确认人</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($milestone_confirmations as $confirmation): ?>
                <tr>
                    <td><?= htmlspecialchars($confirmation['confirmation_no']) ?></td>
                    <td><?= htmlspecialchars($confirmation['milestone_name']) ?></td>
                    <td><?= htmlspecialchars($confirmation['planned_date']) ?></td>
                    <td><?= htmlspecialchars($confirmation['actual_date']) ?></td>
                    <td><?= htmlspecialchars($confirmation['status']) ?></td>
                    <td><?= htmlspecialchars($confirmation['confirmer']) ?></td>
                    <td>
                        <button class="btn-view" onclick="viewMilestoneConfirmation(<?= $confirmation['id'] ?>)">查看</button>
                        <?php if ($confirmation['status'] !== '已确认'): ?>
                        <button class="btn-edit" onclick="editMilestoneConfirmation(<?= $confirmation['id'] ?>)">编辑</button>
                        <button class="btn-delete" onclick="deleteMilestoneConfirmation(<?= $confirmation['id'] ?>)">删除</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 添加/编辑里程碑确认表单 -->
<div id="milestoneForm" class="confirmation-form" style="display: none;">
    <h3>添加里程碑确认</h3>
    <form method="post" action="main.php?view=milestone_confirmation">
        <input type="hidden" name="contract_id" value="<?= $contract_id ?>">
        <input type="hidden" name="action" value="save_milestone">
        
        <div class="form-group">
            <label>里程碑名称:</label>
            <input type="text" name="milestone_name" required>
        </div>
        
        <div class="form-group">
            <label>计划完成时间:</label>
            <input type="date" name="planned_date" required>
        </div>
        
        <div class="form-group">
            <label>实际完成时间:</label>
            <input type="date" name="actual_date" required>
        </div>
        
        <div class="form-group">
            <label>完成百分比:</label>
            <input type="number" name="completion_percentage" min="0" max="100" value="100" required>
        </div>
        
        <div class="form-group">
            <label>确认状态:</label>
            <select name="status" required>
                <option value="待确认">待确认</option>
                <option value="已确认">已确认</option>
                <option value="部分确认">部分确认</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>备注说明:</label>
            <textarea name="remarks"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">保存</button>
            <button type="button" class="btn-secondary" onclick="hideMilestoneForm()">取消</button>
        </div>
    </form>
</div>

<script src="../../../js/milestone_confirmation.js"></script>