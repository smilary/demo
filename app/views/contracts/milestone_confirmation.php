<?php
// 里程碑确认页面
?>

<div class="page-container">
    <div class="page-header">
        <h2>里程碑确认</h2>
        <div class="page-description">管理和确认项目里程碑完成情况</div>
    </div>
    
    <div class="page-content">
        <div class="search-bar">
            <div class="search-form">
                <div class="form-group">
                    <label>合同编号</label>
                    <input type="text" class="form-control" placeholder="请输入合同编号">
                </div>
                <div class="form-group">
                    <label>项目名称</label>
                    <input type="text" class="form-control" placeholder="请输入项目名称">
                </div>
                <div class="form-group">
                    <label>状态</label>
                    <select class="form-control">
                        <option value="">全部</option>
                        <option value="pending">待确认</option>
                        <option value="confirmed">已确认</option>
                        <option value="rejected">已拒绝</option>
                    </select>
                </div>
                <button class="btn btn-primary">搜索</button>
                <button class="btn btn-default">重置</button>
            </div>
        </div>
        
        <div class="data-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>合同编号</th>
                        <th>项目名称</th>
                        <th>里程碑名称</th>
                        <th>计划完成日期</th>
                        <th>实际完成日期</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CT-2023-001</td>
                        <td>ERP系统升级项目</td>
                        <td>需求分析完成</td>
                        <td>2023-10-15</td>
                        <td>2023-10-18</td>
                        <td><span class="status-badge confirmed">已确认</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">查看</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CT-2023-001</td>
                        <td>ERP系统升级项目</td>
                        <td>系统设计完成</td>
                        <td>2023-11-20</td>
                        <td>2023-11-25</td>
                        <td><span class="status-badge confirmed">已确认</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">查看</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CT-2023-001</td>
                        <td>ERP系统升级项目</td>
                        <td>开发完成</td>
                        <td>2023-12-30</td>
                        <td>-</td>
                        <td><span class="status-badge pending">待确认</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary">确认</button>
                            <button class="btn btn-sm btn-danger">拒绝</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CT-2023-002</td>
                        <td>OA系统实施项目</td>
                        <td>需求调研</td>
                        <td>2023-11-10</td>
                        <td>2023-11-12</td>
                        <td><span class="status-badge confirmed">已确认</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">查看</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CT-2023-002</td>
                        <td>OA系统实施项目</td>
                        <td>系统配置</td>
                        <td>2023-12-15</td>
                        <td>-</td>
                        <td><span class="status-badge pending">待确认</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary">确认</button>
                            <button class="btn btn-sm btn-danger">拒绝</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <span>共 15 条记录</span>
            <ul class="page-list">
                <li class="disabled"><a href="#">上一页</a></li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">下一页</a></li>
            </ul>
        </div>
    </div>
</div>

<style>
    .page-container {
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 20px;
    }
    
    .page-header h2 {
        margin: 0 0 10px 0;
        font-size: 24px;
        color: #333;
    }
    
    .page-description {
        color: #666;
        font-size: 14px;
    }
    
    .search-bar {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    
    .search-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-control {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background-color: #1890ff;
        color: white;
    }
    
    .btn-default {
        background-color: #f0f0f0;
        color: #333;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    .btn-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .data-table {
        margin-bottom: 20px;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th, .table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    
    .table th {
        background-color: #f5f7f9;
        font-weight: bold;
        color: #333;
    }
    
    .status-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
    }
    
    .status-badge.confirmed {
        background-color: #52c41a;
        color: white;
    }
    
    .status-badge.pending {
        background-color: #faad14;
        color: white;
    }
    
    .status-badge.rejected {
        background-color: #f5222d;
        color: white;
    }
    
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-list {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .page-list li {
        margin: 0 5px;
    }
    
    .page-list li a {
        display: block;
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        text-decoration: none;
        color: #333;
    }
    
    .page-list li.active a {
        background-color: #1890ff;
        color: white;
        border-color: #1890ff;
    }
    
    .page-list li.disabled a {
        color: #ccc;
        cursor: not-allowed;
    }
</style>