<?php
require_once '../../../lib/contract_manager.php';

// 获取合同ID
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
if (!$contract_id) {
    header("Location: contracts.php");
    exit;
}

// 获取合同基本信息
$contract = get_contract($contract_id);
if (!$contract) {
    header("Location: contracts.php");
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_milestone'])) {
        // 这里添加里程碑确认的处理逻辑
        // 实际项目中需要实现相应的函数
        
        // 示例：更新合同状态为里程碑已确认
        // confirm_contract_milestone($contract_id, $_POST);
        
        // 重定向回详情页
        header("Location: contract_details.php?contract_id=$contract_id&tab=milestone");
        exit;
    }
}

// 设置页面标题
$page_title = '里程碑确认 - ' . htmlspecialchars($contract['contract_no']);

// 设置激活菜单
$active_menu = '合同管理';

// 设置额外CSS文件
$extra_css = ['styles/tabs.css', 'styles/function_pages.css'];

// 设置内容视图
$content_view = __FILE__;

// 如果是通过菜单直接访问的页面，包含布局模板
if (!defined('INCLUDED_IN_LAYOUT')) {
    define('INCLUDED_IN_LAYOUT', true);
    require_once __DIR__.'/../../views/templates/layout.php';
    exit;
}
?>
<div class="page-container">
    <div class="breadcrumb">
        <a href="contracts.php">合同管理</a> &gt; 
        <a href="contract_details.php?contract_id=<?= $contract_id ?>">合同详情</a> &gt; 
        里程碑确认
    </div>
    
    <div class="page-header">
        <h2>里程碑确认</h2>
        <div class="page-description">管理和确认项目里程碑完成情况</div>
    </div>
    
    <div class="contract-info">
        <h2>合同基本信息</h2>
        <table class="table">
            <tr>
                <th>合同编号</th>
                <td><?= htmlspecialchars($contract['contract_no']) ?></td>
                <th>合同名称</th>
                <td><?= htmlspecialchars(isset($contract['contract_name']) ? $contract['contract_name'] : '-') ?></td>
            </tr>
            <tr>
                <th>客户名称</th>
                <td colspan="3"><?= htmlspecialchars($contract['client_name']) ?></td>
            </tr>
            <tr>
                <th>合同类型</th>
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
                <th>合同金额</th>
                <td><?= number_format($contract['amount'], 2) ?></td>
            </tr>
        </table>
    </div>
    
    <div class="search-bar">
        <h2>里程碑确认表单</h2>
        <form method="post" class="search-form">
            <div class="form-group">
                <label>里程碑名称</label>
                <input type="text" name="milestone_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>里程碑描述</label>
                <textarea name="milestone_description" class="form-control" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>计划完成日期</label>
                <input type="date" name="planned_completion_date" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>实际完成日期</label>
                <input type="date" name="actual_completion_date" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>完成比例 (%)</label>
                <input type="number" name="completion_percentage" class="form-control" min="0" max="100" required>
            </div>
            
            <div class="form-group">
                <label>确认人</label>
                <input type="text" name="confirmed_by" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>确认日期</label>
                <input type="date" name="confirmation_date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="form-group">
                <label>备注</label>
                <textarea name="remarks" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" name="confirm_milestone" class="btn btn-primary">确认里程碑</button>
            <a href="contract_details.php?contract_id=<?= $contract_id ?>" class="btn btn-default">返回</a>
        </form>
    </div>
</div>