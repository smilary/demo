-- 整合后的数据库表结构
-- 兼容MariaDB 5.5.47
-- 包含基础业务表、虚拟仓库表和财务表

-- =====================================================
-- 基础业务表
-- =====================================================

-- 用户账号表
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '用户ID，自增主键',
    username VARCHAR(50) NOT NULL UNIQUE COMMENT '登录用户名',
    password VARCHAR(255) NOT NULL COMMENT '密码(加密存储)',
    real_name VARCHAR(50) NOT NULL COMMENT '真实姓名',
    department VARCHAR(50) COMMENT '所属部门',
    is_active TINYINT DEFAULT 1 COMMENT '账号状态：1-启用 0-禁用',
    is_admin TINYINT DEFAULT 0 COMMENT '是否管理员：1-是 0-否',
    created_at TIMESTAMP NULL COMMENT '创建时间'
) COMMENT='系统用户表';

-- 合同主表：记录客户合同信息
CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '合同ID，自增主键',
    contract_no VARCHAR(50) NOT NULL COMMENT '合同编号，业务唯一标识',
    client_name VARCHAR(100) NOT NULL COMMENT '客户名称',
    amount DECIMAL(12,2) COMMENT '合同金额',
    sign_date DATE COMMENT '签订日期',
    file_path VARCHAR(255) COMMENT '合同附件存储路径',
    status TINYINT DEFAULT 1 COMMENT '状态：1-有效 0-作废',
    is_deleted TINYINT DEFAULT 0 COMMENT '软删除标记：1-已删除 0-未删除',
    deleted_at TIMESTAMP NULL COMMENT '删除时间',
    created_at TIMESTAMP NULL COMMENT '创建时间',
    INDEX idx_sign_date (sign_date)
) COMMENT='合同基本信息表';

-- 项目表：记录具体执行项目
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '项目ID，自增主键',
    project_name VARCHAR(100) NOT NULL COMMENT '项目名称',
    contract_id INT COMMENT '关联的合同ID',
    start_date DATE COMMENT '项目开始日期',
    end_date DATE COMMENT '项目结束日期',
    manager_id INT COMMENT '项目经理用户ID',
    status TINYINT DEFAULT 1 COMMENT '状态：1-进行中 2-已完成 0-已取消',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (manager_id) REFERENCES users(id)
) COMMENT='项目信息表';

-- 生产指令表：记录生产任务
CREATE TABLE production_orders (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '指令ID，自增主键',
    order_no VARCHAR(50) NOT NULL COMMENT '生产指令编号',
    project_id INT COMMENT '关联的项目ID（可为空表示紧急指令）',
    product_name VARCHAR(100) NOT NULL COMMENT '产品名称',
    quantity INT NOT NULL COMMENT '生产数量',
    urgent TINYINT DEFAULT 0 COMMENT '是否紧急指令：1-是 0-否',
    status TINYINT DEFAULT 1 COMMENT '状态：1-待生产 2-生产中 3-已完成',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (project_id) REFERENCES projects(id)
) COMMENT='生产指令表';

-- =====================================================
-- 虚拟仓库表
-- =====================================================

-- 物料基础信息表
CREATE TABLE materials (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '物料ID，自增主键',
    material_code VARCHAR(50) NOT NULL UNIQUE COMMENT '物料编码，业务唯一标识',
    material_name VARCHAR(100) NOT NULL COMMENT '物料名称',
    material_type VARCHAR(50) COMMENT '物料类型',
    specification VARCHAR(255) COMMENT '规格型号',
    execution_standard VARCHAR(255) COMMENT '执行标准',
    material_unit VARCHAR(20) NOT NULL COMMENT '计量单位',
    is_active TINYINT DEFAULT 1 COMMENT '是否启用：1-启用 0-禁用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间'
) COMMENT='物料基础信息表';

-- 采购订单表
CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '采购订单ID，自增主键',
    order_no VARCHAR(50) NOT NULL UNIQUE COMMENT '采购订单编号',
    supplier_id INT COMMENT '供应商ID',
    supplier_name VARCHAR(100) NOT NULL COMMENT '供应商名称',
    order_date DATE NOT NULL COMMENT '订单日期',
    expected_delivery_date DATE COMMENT '预计交付日期',
    status TINYINT DEFAULT 1 COMMENT '状态：1-待确认 2-已确认 3-部分入库 4-全部入库 5-已取消',
    created_by INT NOT NULL COMMENT '创建人用户ID',
    created_at TIMESTAMP NULL COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (created_by) REFERENCES users(id)
) COMMENT='采购订单表';

-- 虚拟仓库主表
CREATE TABLE virtual_warehouse (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '虚拟仓库ID，自增主键',
    material_id INT NOT NULL COMMENT '物料ID',
    material_code VARCHAR(50) NOT NULL COMMENT '物料编码',
    material_name VARCHAR(100) NOT NULL COMMENT '物料名称',
    batch_no VARCHAR(50) NOT NULL COMMENT '批次号',
    quantity DECIMAL(12,2) NOT NULL COMMENT '数量',
    unit VARCHAR(20) NOT NULL COMMENT '单位',
    status ENUM('IN_TRANSIT','PENDING_CHECK','QUALIFIED','REJECTED') NOT NULL COMMENT '状态：在途/待检/合格/拒收',
    source_type ENUM('PURCHASE','PRODUCTION') NOT NULL COMMENT '来源类型：采购/生产',
    source_id INT NOT NULL COMMENT '来源ID：采购入库ID/生产入库ID',
    check_result TEXT COMMENT '质检结果',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (material_id) REFERENCES materials(id)
) COMMENT='虚拟仓库主表';

-- =====================================================
-- 财务模块表
-- =====================================================

-- 财务单据主表
CREATE TABLE finance_documents (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '单据ID，自增主键',
    document_no VARCHAR(50) NOT NULL UNIQUE COMMENT '单据编号，业务唯一标识',
    document_type ENUM('RECEIPT', 'PAYMENT', 'INVOICE', 'REFUND') NOT NULL COMMENT '单据类型：收款/付款/发票/退款',
    related_type ENUM('CONTRACT', 'PROJECT', 'PURCHASE', 'OTHER') NOT NULL COMMENT '关联业务类型：合同/项目/采购/其他',
    related_id INT NOT NULL COMMENT '关联业务ID',
    amount DECIMAL(12,2) NOT NULL COMMENT '单据金额',
    direction TINYINT NOT NULL COMMENT '资金方向：1-收入 0-支出',
    document_date DATE NOT NULL COMMENT '单据日期',
    due_date DATE COMMENT '到期日期',
    status TINYINT DEFAULT 1 COMMENT '状态：1-待审核 2-已审核 3-已完成 4-已取消',
    created_by INT NOT NULL COMMENT '创建人用户ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (created_by) REFERENCES users(id)
) COMMENT='财务单据主表';

-- 收付款记录表
CREATE TABLE payment_records (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '记录ID，自增主键',
    finance_document_id INT NOT NULL COMMENT '关联的财务单据ID',
    payment_no VARCHAR(50) NOT NULL UNIQUE COMMENT '收付款编号',
    payment_type ENUM('CASH', 'BANK_TRANSFER', 'CHECK', 'ONLINE_PAYMENT') NOT NULL COMMENT '收付款方式：现金/银行转账/支票/在线支付',
    payment_amount DECIMAL(12,2) NOT NULL COMMENT '收付款金额',
    payment_date DATE NOT NULL COMMENT '收付款日期',
    account_info VARCHAR(255) COMMENT '账户信息',
    transaction_no VARCHAR(100) COMMENT '交易流水号',
    remark TEXT COMMENT '备注',
    created_by INT NOT NULL COMMENT '创建人用户ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (created_by) REFERENCES users(id)
) COMMENT='收付款记录表';

-- 合同收款计划表
CREATE TABLE contract_payment_plans (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '计划ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    payment_stage VARCHAR(50) NOT NULL COMMENT '收款阶段',
    plan_amount DECIMAL(12,2) NOT NULL COMMENT '计划收款金额',
    plan_date DATE NOT NULL COMMENT '计划收款日期',
    actual_amount DECIMAL(12,2) DEFAULT 0 COMMENT '实际收款金额',
    actual_date DATE COMMENT '实际收款日期',
    finance_document_id INT COMMENT '关联的财务单据ID',
    status TINYINT DEFAULT 1 COMMENT '状态：1-未收款 2-部分收款 3-已收款',
    remark TEXT COMMENT '备注',
    created_by INT NOT NULL COMMENT '创建人用户ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) COMMENT='合同收款计划表';

-- 合同明细表：记录合同中的具体物料项目
CREATE TABLE contract_items (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '明细ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    item_no INT NOT NULL COMMENT '项目序号',
    model_type VARCHAR(100) COMMENT '机型',
    material_name VARCHAR(100) NOT NULL COMMENT '物料名称',
    material_code VARCHAR(50) COMMENT '物料编号',
    specification VARCHAR(255) COMMENT '物料规格',
    execution_standard VARCHAR(255) COMMENT '执行标准',
    unit VARCHAR(20) NOT NULL COMMENT '物料单位',
    quantity INT NOT NULL COMMENT '物料数量',
    unit_price DECIMAL(12,2) COMMENT '产品单价',
    remark TEXT COMMENT '备注',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id)
) COMMENT='合同明细表';

-- 合同审批流程表
CREATE TABLE contract_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '审批ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    approver_id INT NOT NULL COMMENT '审批人用户ID',
    approval_level INT NOT NULL COMMENT '审批级别',
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING' COMMENT '审批状态',
    comments TEXT COMMENT '审批意见',
    approved_at TIMESTAMP NULL COMMENT '审批时间',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (approver_id) REFERENCES users(id)
) COMMENT='合同审批流程表';

-- 合同文件表
CREATE TABLE contract_files (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '文件ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    file_name VARCHAR(255) NOT NULL COMMENT '文件名称',
    file_path VARCHAR(255) NOT NULL COMMENT '文件存储路径',
    file_type VARCHAR(50) COMMENT '文件类型',
    file_size INT COMMENT '文件大小(KB)',
    uploaded_by INT NOT NULL COMMENT '上传人用户ID',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '上传时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
) COMMENT='合同文件表';

-- 合同状态变更日志表
CREATE TABLE contract_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '日志ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    old_status TINYINT COMMENT '旧状态',
    new_status TINYINT COMMENT '新状态',
    changed_by INT NOT NULL COMMENT '操作人用户ID',
    comments TEXT COMMENT '变更说明',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
) COMMENT='合同状态变更日志表';

-- 修改合同表，添加更多字段
ALTER TABLE contracts
ADD COLUMN contract_type ENUM('SALES', 'PURCHASE', 'SERVICE', 'OTHER') DEFAULT 'SALES' COMMENT '合同类型' AFTER contract_no,
ADD COLUMN signed_by INT COMMENT '签订人用户ID' AFTER sign_date,
ADD COLUMN effective_date DATE COMMENT '生效日期' AFTER sign_date,
ADD COLUMN expiry_date DATE COMMENT '到期日期' AFTER effective_date,
ADD COLUMN payment_terms TEXT COMMENT '付款条件' AFTER amount,
ADD COLUMN delivery_terms TEXT COMMENT '交付条件' AFTER payment_terms,
ADD COLUMN approval_status ENUM('DRAFT', 'PENDING', 'APPROVED', 'REJECTED') DEFAULT 'DRAFT' COMMENT '审批状态' AFTER status,
ADD COLUMN remark TEXT COMMENT '备注' AFTER file_path;

-- 权限配置表
CREATE TABLE permissions (
    user_id INT NOT NULL COMMENT '用户ID',
    module VARCHAR(50) NOT NULL COMMENT '模块名称',
    permission_type ENUM('view','edit','approve') NOT NULL COMMENT '权限类型：查看/编辑/审批',
    PRIMARY KEY (user_id, module, permission_type),
    FOREIGN KEY (user_id) REFERENCES users(id)
) COMMENT='用户权限配置表';

-- 插入系统管理员账户
INSERT INTO users (username, password, real_name, department, is_active, is_admin) 
VALUES ('admin', MD5('password'), '系统管理员', '信息技术部', 1, 1);

-- 为系统管理员添加所有模块的所有权限
INSERT INTO permissions (user_id, module, permission_type)
VALUES 
(1, 'contracts', 'view'),
(1, 'contracts', 'edit'),
(1, 'contracts', 'approve'),
(1, 'projects', 'view'),
(1, 'projects', 'edit'),
(1, 'projects', 'approve'),
(1, 'production', 'view'),
(1, 'production', 'edit'),
(1, 'production', 'approve'),
(1, 'purchase', 'view'),
(1, 'purchase', 'edit'),
(1, 'purchase', 'approve'),
(1, 'warehouse', 'view'),
(1, 'warehouse', 'edit'),
(1, 'warehouse', 'approve'),
(1, 'finance', 'view'),
(1, 'finance', 'edit'),
(1, 'finance', 'approve'),
(1, 'users', 'view'),
(1, 'users', 'edit'),
(1, 'users', 'approve');
