-- 添加合同名称字段到合同表
ALTER TABLE contracts
ADD COLUMN contract_name VARCHAR(255) COMMENT '合同名称' AFTER contract_no;