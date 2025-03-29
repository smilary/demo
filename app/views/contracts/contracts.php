<?php
// 设置页面标题
$page_title = '合同管理 - 项目管理系统';

// 设置激活菜单
$active_menu = '合同管理';

// 设置额外CSS文件
$extra_css = ['styles/function_pages.css'];

// 设置内容视图
$content_view = __FILE__;

// 如果是直接访问此文件，则包含布局模板
if (!defined('INCLUDED_IN_LAYOUT')) {
    define('INCLUDED_IN_LAYOUT', true);
    require_once __DIR__.'/../../views/templates/layout.php';
    exit;
}

// 引入合同管理库
require_once __DIR__.'/../../../lib/contract_manager.php';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        add_contract($_POST);
        header("Location: #/contracts");
        exit;
    } elseif (isset($_POST['update'])) {
        update_contract($_POST['id'], $_POST);
        header("Location: #/contracts");
        exit;
    }
}

// 处理删除请求
if (isset($_GET['delete'])) {
    delete_contract($_GET['id']);
    header("Location: #/contracts");
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

<div class="page-container">
    <div class="page-header">
        <h2>合同管理</h2>
        <div class="page-description">管理所有合同信息，包括合同创建、编辑、查询和删除</div>
    </div>
    
    <div class="function-area">
        <!-- 搜索区域 -->
        <div class="search-area">
            <form method="get" class="search-form">
                <div class="form-group">
                    <input type="text" name="search" placeholder="搜索合同编号或客户名称" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">搜索</button>
                    <a href="#/contracts" class="btn btn-secondary">重置</a>
                </div>
            </form>
        </div>
        
        <!-- 操作区域 -->
        <div class="action-area">
            <button type="button" class="btn btn-primary" onclick="showAddForm()">新增合同</button>
            <?php if (!isset($_GET['show_deleted'])): ?>
                <a href="?show_deleted=1" class="btn btn-secondary">查看回收站</a>
            <?php else: ?>
                <a href="#/contracts" class="btn btn-secondary">返回合同列表</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 合同表单 -->
    <div id="contractForm" class="form-container" style="display: <?= $edit_contract ? 'block' : 'none' ?>">
        <h3><?= $edit_contract ? '编辑合同' : '新增合同' ?></h3>
        <form method="post">
            <?php if ($edit_contract): ?>
                <input type="hidden" name="id" value="<?= $edit_contract['id'] ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>合同编号:</label>
                    <input type="text" name="contract_no" required 
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['contract_no']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>合同名称:</label>
                    <input type="text" name="contract_name" required 
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['contract_name']) : '' ?>">
                </div>
            </div>
            
            <div class="form-row">
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
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['client_name']) : '' ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>合同金额:</label>
                    <input type="number" step="0.01" name="amount"
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['amount']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>签订日期:</label>
                    <input type="date" name="sign_date" required
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['sign_date']) : date('Y-m-d') ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>生效日期:</label>
                    <input type="date" name="effective_date"
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['effective_date']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label>到期日期:</label>
                    <input type="date" name="expiry_date"
                           value="<?= $edit_contract ? htmlspecialchars($edit_contract['expiry_date']) : '' ?>">
                </div>
            </div>
            
            <div class="form-group full-width">
                <label>付款条件:</label>
                <textarea name="payment_terms"><?= $edit_contract ? htmlspecialchars($edit_contract['payment_terms']) : '' ?></textarea>
            </div>
            
            <div class="form-group full-width">
                <label>交付条件:</label>
                <textarea name="delivery_terms"><?= $edit_contract ? htmlspecialchars($edit_contract['delivery_terms']) : '' ?></textarea>
            </div>
            
            <div class="form-group full-width">
                <label>备注:</label>
                <textarea name="remark"><?= $edit_contract ? htmlspecialchars($edit_contract['remark']) : '' ?></textarea>
            </div>
            
            <div class="form-actions">
                <?php if ($edit_contract): ?>
                    <button type="submit" name="update" class="btn btn-primary">更新合同</button>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary">保存合同</button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" onclick="hideForm()">取消</button>
            </div>
        </form>
    </div>
    
    <!-- 合同列表 -->
    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>合同编号</th>
                    <th>合同名称</th>
                    <th>客户名称</th>
                    <th>合同类型</th>
                    <th>合同金额</th>
                    <th>签订日期</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contracts)): ?>
                    <tr>
                        <td colspan="8" class="text-center">暂无合同数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contracts as $contract): ?>
                        <tr>
                            <td><?= htmlspecialchars($contract['contract_no']) ?></td>
                            <td><?= htmlspecialchars($contract['contract_name']) ?></td>
                            <td><?= htmlspecialchars($contract['client_name']) ?></td>
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
                            <td><?= number_format($contract['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($contract['sign_date']) ?></td>
                            <td>
                                <?php 
                                $now = time();
                                $expiry = strtotime($contract['expiry_date']);
                                if (empty($contract['expiry_date'])) {
                                    echo '<span class="status-active">有效</span>';
                                } elseif ($expiry < $now) {
                                    echo '<span class="status-expired">已过期</span>';
                                } elseif ($expiry - $now < 30 * 24 * 3600) {
                                    echo '<span class="status-warning">即将到期</span>';
                                } else {
                                    echo '<span class="status-active">有效</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="#/contract_details?contract_id=<?= $contract['id'] ?>" class="btn btn-sm btn-info">详情</a>
                                    <a href="#/contracts?edit=1&id=<?= $contract['id'] ?>" class="btn btn-sm btn-primary">编辑</a>
                                    <a href="#/contracts?delete=1&id=<?= $contract['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除此合同吗？')">删除</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- 分页 -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="#/contracts?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-link">&laquo; 上一页</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="#/contracts?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
            <a href="#/contracts?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-link">下一页 &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function showAddForm() {
    document.getElementById('contractForm').style.display = 'block';
}

function hideForm() {
    document.getElementById('contractForm').style.display = 'none';
    window.location.href = '#/contracts';
}
</script>
