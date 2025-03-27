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