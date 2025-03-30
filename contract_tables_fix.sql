-- 添加缺失的合同服务和里程碑确认表

-- 合同里程碑表
CREATE TABLE IF NOT EXISTS contract_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '里程碑ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    milestone_name VARCHAR(100) NOT NULL COMMENT '里程碑名称',
    planned_date DATE NOT NULL COMMENT '计划完成日期',
    completion_percentage INT DEFAULT 0 COMMENT '完成百分比',
    status ENUM('未开始', '进行中', '已完成') DEFAULT '未开始' COMMENT '里程碑状态',
    description TEXT COMMENT '里程碑描述',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    updated_at TIMESTAMP NULL COMMENT '更新时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id)
) COMMENT='合同里程碑表';

-- 里程碑确认表
CREATE TABLE IF NOT EXISTS milestone_confirmations (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '确认ID，自增主键',
    milestone_id INT NOT NULL COMMENT '关联的里程碑ID',
    confirmation_no VARCHAR(50) NOT NULL COMMENT '确认编号',
    actual_date DATE COMMENT '实际完成日期',
    completion_percentage INT DEFAULT 100 COMMENT '完成百分比',
    status ENUM('待确认', '已确认', '部分确认') DEFAULT '待确认' COMMENT '确认状态',
    remarks TEXT COMMENT '备注说明',
    confirmed_by INT COMMENT '确认人用户ID',
    confirmed_at TIMESTAMP NULL COMMENT '确认时间',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (milestone_id) REFERENCES contract_milestones(id),
    FOREIGN KEY (confirmed_by) REFERENCES users(id)
) COMMENT='里程碑确认表';

-- 合同服务项目表
CREATE TABLE IF NOT EXISTS contract_services (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '服务ID，自增主键',
    contract_id INT NOT NULL COMMENT '关联的合同ID',
    confirmation_no VARCHAR(50) NOT NULL COMMENT '确认编号',
    service_item VARCHAR(100) NOT NULL COMMENT '服务项目名称',
    service_start_date DATE NOT NULL COMMENT '服务开始日期',
    service_end_date DATE NOT NULL COMMENT '服务结束日期',
    status ENUM('待确认', '已确认', '部分确认') DEFAULT '待确认' COMMENT '确认状态',
    remarks TEXT COMMENT '备注说明',
    confirmed_by INT COMMENT '确认人用户ID',
    confirmed_at TIMESTAMP NULL COMMENT '确认时间',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    FOREIGN KEY (contract_id) REFERENCES contracts(id),
    FOREIGN KEY (confirmed_by) REFERENCES users(id)
) COMMENT='合同服务项目表';