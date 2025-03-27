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
    if (isset($_POST['add_item'])) {
        // 添加合同明细项
        add_contract_item($contract_id, $_POST);
        header("Location: contract_details.php?contract_id=$contract_id");
        exit;
    } elseif (isset($_POST['update_item'])) {
        // 更新合同明细项
        update_contract_item($_POST['item_id'], $_POST);
        header("Location: contract_details.php?contract_id=$contract_id");
        exit;
    } elseif (isset($_POST['delete_item'])) {
        // 删除合同明细项
        delete_contract_item($_POST['item_id']);
        header("Location: contract_details.php?contract_id=$contract_id");
        exit;
    } elseif (isset($_POST['upload_file'])) {
        // 处理文件上传
        if (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] == 0) {
            $upload_dir = "../../../uploads/contracts/";
            
            // 确保上传目录存在
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = basename($_FILES["contract_file"]["name"]);
            $target_file = $upload_dir . time() . "_" . $file_name;
            
            if (move_uploaded_file($_FILES["contract_file"]["tmp_name"], $target_file)) {
                // 保存文件记录到数据库
                $file_data = [
                    'file_name' => $file_name,
                    'file_path' => $target_file,
                    'file_type' => $_FILES["contract_file"]["type"],
                    'file_size' => $_FILES["contract_file"]["size"] / 1024, // 转换为KB
                    'uploaded_by' => 1 // 假设当前用户ID为1，实际应从会话中获取
                ];
                
                add_contract_file($contract_id, $file_data);
                header("Location: contract_details.php?contract_id=$contract_id&tab=files");
                exit;
            }
        }
    }
}

// 获取合同明细列表
$items = get_contract_items($contract_id);

// 获取合同文件列表
$files = get_contract_files($contract_id);

// 当前激活的标签页
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'details';

// 获取编辑项目的数据
$edit_item = null;
if (isset($_GET['edit_item'])) {
    $edit_item = get_contract_item($_GET['edit_item']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>合同详情 - <?= htmlspecialchars($contract['contract_no']) ?></title>
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
        .tabs { display: flex; margin-bottom: 20px; border-bottom: 1px solid #ddd; }
        .tab { padding: 10px 15px; cursor: pointer; }
        .tab.active { background-color: #f2f2f2; border: 1px solid #ddd; border-bottom: none; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>合同详情</h1>
        
        <div class="contract-info">
            <h2>基本信息</h2>
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
                <tr>
                    <th>签订日期</th>
                    <td><?= htmlspecialchars($contract['sign_date']) ?></td>
                    <th>签订人</th>
                    <td><?= htmlspecialchars(isset($contract['signed_by']) ? $contract['signed_by'] : '-') ?></td>
                </tr>
                <tr>
                    <th>生效日期</th>
                    <td><?= htmlspecialchars(isset($contract['effective_date']) ? $contract['effective_date'] : '-') ?></td>
                    <th>到期日期</th>
                    <td><?= htmlspecialchars(isset($contract['expiry_date']) ? $contract['expiry_date'] : '-') ?></td>
                </tr>
                <tr>
                    <th>付款条件</th>
                    <td colspan="3"><?= nl2br(htmlspecialchars(isset($contract['payment_terms']) ? $contract['payment_terms'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>交付条件</th>
                    <td colspan="3"><?= nl2br(htmlspecialchars(isset($contract['delivery_terms']) ? $contract['delivery_terms'] : '-')) ?></td>
                </tr>
                <tr>
                    <th>审批状态</th>
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
                    <th>备注</th>
                    <td><?= nl2br(htmlspecialchars(isset($contract['remark']) ? $contract['remark'] : '-')) ?></td>
                </tr>
            </table>
            <a href="contracts.php" class="btn btn-secondary">返回合同列表</a>
        </div>
        
        <div class="tabs">
            <div class="tab <?= $active_tab == 'details' ? 'active' : '' ?>" onclick="location.href='contract_details.php?contract_id=<?= $contract_id ?>&tab=details'">合同明细</div>
            <div class="tab <?= $active_tab == 'files' ? 'active' : '' ?>" onclick="location.href='contract_details.php?contract_id=<?= $contract_id ?>&tab=files'">合同文件</div>
            <div class="tab <?= $active_tab == 'approvals' ? 'active' : '' ?>" onclick="location.href='contract_details.php?contract_id=<?= $contract_id ?>&tab=approvals'">审批记录</div>
        </div>
        
        <!-- 合同明细标签页 -->
        <div class="tab-content <?= $active_tab == 'details' ? 'active' : '' ?>">
            <h2>合同明细</h2>
            
            <!-- 添加/编辑明细表单 -->
            <form method="post">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="item_id" value="<?= $edit_item['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>项目序号:</label>
                    <input type="number" name="item_no" required 
                           value="<?= $edit_item ? $edit_item['item_no'] : (count($items) + 1) ?>">
                </div>
                
                <div class="form-group">
                    <label>机型:</label>
                    <input type="text" name="model_type"
                           value="<?= $edit_item ? $edit_item['model_type'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>物料名称:</label>
                    <input type="text" name="material_name" required
                           value="<?= $edit_item ? $edit_item['material_name'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>物料编号:</label>
                    <input type="text" name="material_code"
                           value="<?= $edit_item ? $edit_item['material_code'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>物料规格:</label>
                    <input type="text" name="specification"
                           value="<?= $edit_item ? $edit_item['specification'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>执行标准:</label>
                    <input type="text" name="execution_standard"
                           value="<?= $edit_item ? $edit_item['execution_standard'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>物料单位:</label>
                    <input type="text" name="unit" required
                           value="<?= $edit_item ? $edit_item['unit'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>物料数量:</label>
                    <input type="number" name="quantity" required
                           value="<?= $edit_item ? $edit_item['quantity'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>产品单价:</label>
                    <input type="number" step="0.01" name="unit_price"
                           value="<?= $edit_item ? $edit_item['unit_price'] : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>备注:</label>
                    <textarea name="remark"><?= $edit_item ? $edit_item['remark'] : '' ?></textarea>
                </div>
                
                <button type="submit" name="<?= $edit_item ? 'update_item' : 'add_item' ?>" class="btn">
                    <?= $edit_item ? '更新明细' : '添加明细' ?>
                </button>
                
                <?php if ($edit_item): ?>
                    <a href="contract_details.php?contract_id=<?= $contract_id ?>" class="btn btn-secondary">取消编辑</a>
                <?php endif; ?>
            </form>
            
            <!-- 明细列表 -->
            <h3>明细列表</h3>
            <table>
                <tr>
                    <th>序号</th>
                    <th>机型</th>
                    <th>物料名称</th>
                    <th>物料编号</th>
                    <th>规格</th>
                    <th>单位</th>
                    <th>数量</th>
                    <th>单价</th>
                    <th>金额</th>
                    <th>操作</th>
                </tr>
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="10" style="text-align: center;">暂无明细数据</td>
                </tr>
                <?php else: ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= $item['item_no'] ?></td>
                    <td><?= htmlspecialchars(isset($item['model_type']) ? $item['model_type'] : '') ?></td>
                    <td><?= htmlspecialchars($item['material_name']) ?></td>
                    <td><?= htmlspecialchars(isset($item['material_code']) ? $item['material_code'] : '') ?></td>
                    <td><?= htmlspecialchars(isset($item['specification']) ? $item['specification'] : '') ?></td>
                    <td><?= htmlspecialchars($item['unit']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= $item['unit_price'] ? number_format($item['unit_price'], 2) : '' ?></td>
                    <td><?= $item['unit_price'] ? number_format($item['unit_price'] * $item['quantity'], 2) : '' ?></td>
                    <td>
                        <a href="contract_details.php?contract_id=<?= $contract_id ?>&edit_item=<?= $item['id'] ?>">编辑</a>
                        <form method="post" style="display: inline;" onsubmit="return confirm('确定要删除此明细项吗？')">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="delete_item" class="btn btn-danger" style="padding: 2px 5px; font-size: 12px;">删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- 合同文件标签页 -->
        <div class="tab-content <?= $active_tab == 'files' ? 'active' : '' ?>">
            <h2>合同文件</h2>
            
            <!-- 文件上传表单 -->
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>选择文件:</label>
                    <input type="file" name="contract_file" required>
                </div>
                
                <button type="submit" name="upload_file" class="btn">上传文件</button>
            </form>
            
            <!-- 文件列表 -->
            <h3>文件列表</h3>
            <table>
                <tr>
                    <th>文件名</th>
                    <th>文件类型</th>
                    <th>文件大小</th>
                    <th>上传时间</th>
                    <th>操作</th>
                </tr>
                <?php if (empty($files)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">暂无文件</td>
                </tr>
                <?php else: ?>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= htmlspecialchars($file['file_name']) ?></td>
                    <td><?= htmlspecialchars($file['file_type']) ?></td>
                    <td><?= number_format($file['file_size'], 2) ?> KB</td>
                    <td><?= $file['uploaded_at'] ?></td>
                    <td>
                        <a href="<?= $file['file_path'] ?>" target="_blank">查看</a>
                        <form method="post" style="display: inline;" onsubmit="return confirm('确定要删除此文件吗？')">
                            <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                            <button type="submit" name="delete_file" class="btn btn-danger" style="padding: 2px 5px; font-size: 12px;">删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- 审批记录标签页 -->
        <div class="tab-content <?= $active_tab == 'approvals' ? 'active' : '' ?>">
            <h2>审批记录</h2>
            
            <?php if (isset($contract['approval_status']) && $contract['approval_status'] == 'DRAFT'): ?>
            <!-- 提交审批按钮 -->
            <form method="post">
                <button type="submit" name="submit_approval" class="btn">提交审批</button>
            </form>
            <?php endif; ?>
            
            <?php if (isset($contract['approval_status']) && $contract['approval_status'] == 'PENDING'): ?>
            <!-- 审批操作表单 -->
            <form method="post" class="approval-form" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9;">
                <h3>审批操作</h3>
                <input type="hidden" name="approval_id" value="<?= isset($pending_approval['id']) ? $pending_approval['id'] : '' ?>">
                
                <div class="form-group">
                    <label>审批意见:</label>
                    <textarea name="approval_comments" rows="3" style="width: 100%;"></textarea>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <button type="submit" name="approve" class="btn" style="background-color: #4CAF50;">批准</button>
                    <button type="submit" name="reject" class="btn" style="background-color: #f44336; margin-left: 10px;">拒绝</button>
                </div>
            </form>
            <?php endif; ?>
            
            <!-- 审批记录列表 -->
            <h3>审批历史</h3>
            <table>
                <tr>
                    <th>审批人</th>
                    <th>审批级别</th>
                    <th>审批状态</th>
                    <th>审批意见</th>
                    <th>审批时间</th>
                </tr>
                <?php 
                // 从数据库获取审批记录
                $approvals = get_contract_approvals($contract_id);
                ?>
                <?php if (empty($approvals)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">暂无审批记录</td>
                </tr>
                <?php else: ?>
                <?php foreach ($approvals as $approval): ?>
                <tr>
                    <td><?= htmlspecialchars($approval['approver_name']) ?></td>
                    <td><?= $approval['approval_level'] ?></td>
                    <td>
                        <?php 
                        $status_labels = [
                            'PENDING' => '<span style="color: #f90;">待审批</span>',
                            'APPROVED' => '<span style="color: #090;">已批准</span>',
                            'REJECTED' => '<span style="color: #f00;">已拒绝</span>'
                        ];
                        echo isset($status_labels[$approval['status']]) ? $status_labels[$approval['status']] : $approval['status'];
                        ?>
                    </td>
                    <td><?= htmlspecialchars(isset($approval['comments']) ? $approval['comments'] : '-') ?></td>
                    <td><?= $approval['approved_at'] ? date('Y-m-d H:i:s', strtotime($approval['approved_at'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
    </div>
    
    <script>
        // 简单的标签页切换功能
        document.addEventListener('DOMContentLoaded', function() {
            var tabs = document.querySelectorAll('.tab');
            var tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    // 移除所有活动类
                    tabs.forEach(function(t) { t.classList.remove('active'); });
                    tabContents.forEach(function(c) { c.classList.remove('active'); });
                    
                    // 添加活动类到当前标签
                    this.classList.add('active');
                    
                    // 显示对应的内容
                    var tabId = this.textContent.toLowerCase();
                    document.querySelector('.tab-content.' + tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>