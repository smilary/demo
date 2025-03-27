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
    if (isset($_POST['confirm_service'])) {
        // 这里添加服务确认的处理逻辑
        // 实际项目中需要实现相应的函数
        
        // 示例：更新合同状态为服务已确认
        // confirm_contract_service($contract_id, $_POST);
        
        // 重定向回详情页
        header("Location: contract_details.php?contract_id=$contract_id&tab=service");
        exit;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>服务确认 - <?= htmlspecialchars($contract['contract_no']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1, h2, h3 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { padding: 8px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn-danger { background-color: #f44336; }
        .btn-secondary { background-color: #555; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { text-decoration: none; color: #0275d8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="contracts.php">合同管理</a> &gt; 
            <a href="contract_details.php?contract_id=<?= $contract_id ?>">合同详情</a> &gt; 
            服务确认
        </div>
        
        <h1>服务确认</h1>
        
        <div class="contract-info">
            <h2>合同基本信息</h2>
            <table>
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
        
        <div class="service-confirmation">
            <h2>服务确认表单</h2>
            <form method="post">
                <div class="form-group">
                    <label>服务项目:</label>
                    <input type="text" name="service_item" required>
                </div>
                
                <div class="form-group">
                    <label>服务内容:</label>
                    <textarea name="service_content" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>服务开始日期:</label>
                    <input type="date" name="service_start_date" required>
                </div>
                
                <div class="form-group">
                    <label>服务结束日期:</label>
                    <input type="date" name="service_end_date" required>
                </div>
                
                <div class="form-group">
                    <label>服务确认人:</label>
                    <input type="text" name="confirmed_by" required>
                </div>
                
                <div class="form-group">
                    <label>确认日期:</label>
                    <input type="date" name="confirmation_date" required value="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label>备注:</label>
                    <textarea name="remarks" rows="3"></textarea>
                </div>
                
                <button type="submit" name="confirm_service" class="btn">确认服务</button>
                <a href="contract_details.php?contract_id=<?= $contract_id ?>" class="btn btn-secondary">返回</a>
            </form>
        </div>
    </div>
</body>
</html>