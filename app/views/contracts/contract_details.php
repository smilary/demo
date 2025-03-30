<?php
// 设置页面标题
$page_title = '合同详情 - 项目管理系统';

// 设置激活菜单
$active_menu = '合同管理';

// 设置额外CSS文件
$extra_css = ['styles/function_pages.css', 'styles/tabs.css'];

// 设置内容视图
$content_view = __FILE__;

// 如果是直接访问此文件，则包含布局模板
if (!defined('INCLUDED_IN_LAYOUT')) {
    define('INCLUDED_IN_LAYOUT', true);
    $is_secondary_page = true; // 标记为二级功能页
    require_once __DIR__.'/../../views/templates/layout.php';
    exit;
}

// 引入合同管理库
require_once __DIR__.'/../../../lib/contract_manager.php';

// 获取合同ID
$contract_id = isset($_GET['contract_id']) ? intval($_GET['contract_id']) : 0;
if (!$contract_id) {
    echo '<div class="alert alert-danger">请提供有效的合同ID参数</div>';
    exit;
}

// 获取合同基本信息
$contract = get_contract($contract_id);
if (!$contract) {
    echo '<div class="alert alert-danger">未找到指定的合同信息</div>';
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // 添加合同明细项
        add_contract_item($contract_id, $_POST);
        header("Location: main.php?view=contract_details&contract_id=$contract_id");
        exit;
    } elseif (isset($_POST['update_item'])) {
        // 更新合同明细项
        update_contract_item($_POST['item_id'], $_POST);
        header("Location: main.php?view=contract_details&contract_id=$contract_id");
        exit;
    } elseif (isset($_POST['delete_item'])) {
        // 删除合同明细项
        delete_contract_item($_POST['item_id']);
        header("Location: main.php?view=contract_details&contract_id=$contract_id");
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
                    'uploaded_by' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1
                ];
                
                add_contract_file($contract_id, $file_data);
                header("Location: main.php?view=contract_details&contract_id=$contract_id&tab=files");
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

<div class="page-container">
    <div class="breadcrumb">
        <a href="#/contracts">合同管理</a> &gt; 
        合同详情
    </div>
    
    <div class="page-header">
        <h2>合同详情 - <?= htmlspecialchars($contract['contract_no']) ?></h2>
        <div class="page-description"><?= htmlspecialchars($contract['contract_name']) ?></div>
    </div>
    
    <!-- 合同基本信息 -->
    <div class="contract-info">
        <table class="info-table">
            <tr>
                <th>合同编号</th>
                <td><?= htmlspecialchars($contract['contract_no']) ?></td>
                <th>合同名称</th>
                <td><?= htmlspecialchars($contract['contract_name']) ?></td>
            </tr>
            <tr>
                <th>客户名称</th>
                <td><?= htmlspecialchars($contract['client_name']) ?></td>
                <th>合同类型</th>
                <td>
                    <?php 
                    $type_map = [
                        'SALES' => '销售合同',
                        'PURCHASE' => '采购合同',
                        'SERVICE' => '服务合同',
                        'OTHER' => '其他合同'
                    ];
                    echo isset($type_map[$contract['contract_type']]) ? $type_map[$contract['contract_type']] : $contract['contract_type'];
                    ?>
                </td>
            </tr>
            <tr>
                <th>合同金额</th>
                <td><?= number_format($contract['amount'], 2) ?></td>
                <th>签订日期</th>
                <td><?= htmlspecialchars($contract['sign_date']) ?></td>
            </tr>
            <tr>
                <th>生效日期</th>
                <td><?= htmlspecialchars($contract['effective_date']) ?></td>
                <th>到期日期</th>
                <td><?= htmlspecialchars($contract['expiry_date']) ?></td>
            </tr>
        </table>
    </div>
    
    <!-- 标签页导航 -->
    <div class="detail-tabs">
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=details" class="tab <?= $active_tab == 'details' ? 'active' : '' ?>">基本信息</a>
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=items" class="tab <?= $active_tab == 'items' ? 'active' : '' ?>">合同明细</a>
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=files" class="tab <?= $active_tab == 'files' ? 'active' : '' ?>">合同附件</a>
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=milestone" class="tab <?= $active_tab == 'milestone' ? 'active' : '' ?>">里程碑</a>
        <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=service" class="tab <?= $active_tab == 'service' ? 'active' : '' ?>">服务确认</a>
    </div>
    
    <!-- 标签页内容 -->
    <div class="tab-content">
        <?php if ($active_tab == 'details'): ?>
            <!-- 基本信息标签页 -->
            <div class="tab-pane active">
                <h3>合同详细信息</h3>
                <table class="info-table full-width">
                    <tr>
                        <th>付款条件</th>
                        <td><?= nl2br(htmlspecialchars($contract['payment_terms'])) ?></td>
                    </tr>
                    <tr>
                        <th>交付条件</th>
                        <td><?= nl2br(htmlspecialchars($contract['delivery_terms'])) ?></td>
                    </tr>
                    <tr>
                        <th>备注</th>
                        <td><?= nl2br(htmlspecialchars($contract['remark'])) ?></td>
                    </tr>
                </table>
                
                <div class="action-buttons">
                    <a href="main.php?view=contracts&edit=1&id=<?= $contract_id ?>" class="btn btn-primary">编辑合同</a>
                    <a href="main.php?view=contracts" class="btn btn-secondary">返回列表</a>
                </div>
            </div>
        <?php elseif ($active_tab == 'items'): ?>
            <!-- 合同明细标签页 -->
            <div class="tab-pane active">
                <div class="action-buttons">
                    <button type="button" class="btn btn-primary" onclick="showAddItemForm()">添加明细项</button>
                </div>
                
                <!-- 添加/编辑明细项表单 -->
                <div id="itemForm" class="form-container" style="display: <?= $edit_item ? 'block' : 'none' ?>">
                    <h3><?= $edit_item ? '编辑明细项' : '添加明细项' ?></h3>
                    <form method="post">
                        <?php if ($edit_item): ?>
                            <input type="hidden" name="item_id" value="<?= $edit_item['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>项目名称:</label>
                                <input type="text" name="item_name" required 
                                       value="<?= $edit_item ? htmlspecialchars($edit_item['item_name']) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>数量:</label>
                                <input type="number" step="0.01" name="quantity" required
                                       value="<?= $edit_item ? htmlspecialchars($edit_item['quantity']) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>单价:</label>
                                <input type="number" step="0.01" name="unit_price" required
                                       value="<?= $edit_item ? htmlspecialchars($edit_item['unit_price']) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>单位:</label>
                                <input type="text" name="unit" 
                                       value="<?= $edit_item ? htmlspecialchars($edit_item['unit']) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>描述:</label>
                            <textarea name="description"><?= $edit_item ? htmlspecialchars($edit_item['description']) : '' ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <?php if ($edit_item): ?>
                                <button type="submit" name="update_item" class="btn btn-primary">更新明细项</button>
                            <?php else: ?>
                                <button type="submit" name="add_item" class="btn btn-primary">保存明细项</button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-secondary" onclick="hideItemForm()">取消</button>
                        </div>
                    </form>
                </div>
                
                <!-- 明细项列表 -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>项目名称</th>
                            <th>数量</th>
                            <th>单位</th>
                            <th>单价</th>
                            <th>金额</th>
                            <th>描述</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="7" class="text-center">暂无明细数据</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td><?= htmlspecialchars($item['unit']) ?></td>
                                    <td><?= number_format($item['unit_price'], 2) ?></td>
                                    <td><?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($item['description']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=items&edit_item=<?= $item['id'] ?>" class="btn btn-sm btn-primary">编辑</a>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('确定要删除此明细项吗？')">
                                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                <button type="submit" name="delete_item" class="btn btn-sm btn-danger">删除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($active_tab == 'files'): ?>
            <!-- 合同附件标签页 -->
            <div class="tab-pane active">
                <div class="action-buttons">
                    <button type="button" class="btn btn-primary" onclick="showUploadForm()">上传附件</button>
                </div>
                
                <!-- 文件上传表单 -->
                <div id="uploadForm" class="form-container" style="display: none;">
                    <h3>上传合同附件</h3>
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>选择文件:</label>
                            <input type="file" name="contract_file" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="upload_file" class="btn btn-primary">上传文件</button>
                            <button type="button" class="btn btn-secondary" onclick="hideUploadForm()">取消</button>
                        </div>
                    </form>
                </div>
                
                <!-- 文件列表 -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>文件名</th>
                            <th>文件类型</th>
                            <th>文件大小</th>
                            <th>上传时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($files)): ?>
                            <tr>
                                <td colspan="5" class="text-center">暂无附件</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($files as $file): ?>
                                <tr>
                                    <td><?= htmlspecialchars($file['file_name']) ?></td>
                                    <td><?= htmlspecialchars($file['file_type']) ?></td>
                                    <td><?= number_format($file['file_size'], 2) ?> KB</td>
                                    <td><?= htmlspecialchars($file['uploaded_at']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="<?= htmlspecialchars($file['file_path']) ?>" class="btn btn-sm btn-info" target="_blank">查看</a>
                                            <a href="<?= htmlspecialchars($file['file_path']) ?>" class="btn btn-sm btn-primary" download>下载</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($active_tab == 'milestone'): ?>
            <!-- 里程碑标签页 -->
            <div class="tab-pane active">
                <div class="action-buttons">
                    <a href="main.php?view=milestone_confirmation&contract_id=<?= $contract_id ?>" class="btn btn-primary">添加里程碑确认</a>
                </div>
                
                <!-- 里程碑列表 -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>里程碑名称</th>
                            <th>完成日期</th>
                            <th>完成百分比</th>
                            <th>确认人</th>
                            <th>确认时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $milestones = get_contract_milestones($contract_id);
                        if (empty($milestones)): 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">暂无里程碑数据</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($milestones as $milestone): ?>
                                <tr>
                                    <td><?= htmlspecialchars($milestone['milestone_name']) ?></td>
                                    <td><?= htmlspecialchars($milestone['completion_date']) ?></td>
                                    <td><?= htmlspecialchars($milestone['completion_percentage']) ?>%</td>
                                    <td><?= htmlspecialchars($milestone['confirmed_by']) ?></td>
                                    <td><?= htmlspecialchars($milestone['confirmed_at']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="main.php?view=milestone_confirmation&contract_id=<?= $contract_id ?>&milestone_id=<?= $milestone['id'] ?>" class="btn btn-sm btn-info">查看详情</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($active_tab == 'service'): ?>
            <!-- 服务确认标签页 -->
            <div class="tab-pane active">
                <div class="action-buttons">
                    <a href="main.php?view=service_confirmation&contract_id=<?= $contract_id ?>" class="btn btn-primary">添加服务确认</a>
                </div>
                
                <!-- 服务确认列表 -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>服务项目</th>
                            <th>服务开始日期</th>
                            <th>服务结束日期</th>
                            <th>确认人</th>
                            <th>确认时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $services = get_contract_services($contract_id);
                        if (empty($services)): 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">暂无服务确认数据</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= htmlspecialchars($service['service_item']) ?></td>
                                    <td><?= htmlspecialchars($service['service_start_date']) ?></td>
                                    <td><?= htmlspecialchars($service['service_end_date']) ?></td>
                                    <td><?= htmlspecialchars($service['confirmed_by']) ?></td>
                                    <td><?= htmlspecialchars($service['confirmed_at']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="main.php?view=service_confirmation&contract_id=<?= $contract_id ?>&service_id=<?= $service['id'] ?>" class="btn btn-sm btn-info">查看详情</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showAddItemForm() {
    document.getElementById('itemForm').style.display = 'block';
}

function hideItemForm() {
    document.getElementById('itemForm').style.display = 'none';
    window.location.href = 'main.php?view=contract_details&contract_id=<?= $contract_id ?>&tab=items';
}

function showUploadForm() {
    document.getElementById('uploadForm').style.display = 'block';
}

function hideUploadForm() {
    document.getElementById('uploadForm').style.display = 'none';
}
</script>