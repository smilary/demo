/**
 * 里程碑确认相关JavaScript功能
 */

// 显示添加里程碑确认表单
function showAddMilestoneConfirmation() {
    // 显示添加里程碑确认表单
    document.getElementById('milestoneForm').style.display = 'block';
}

// 隐藏里程碑确认表单
function hideMilestoneForm() {
    document.getElementById('milestoneForm').style.display = 'none';
}

// 查看里程碑确认详情
function viewMilestoneConfirmation(id) {
    // 跳转到里程碑确认详情页面
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=milestone_confirmation&contract_id=${contractId}&milestone_id=${id}&action=view`;
}

// 编辑里程碑确认
function editMilestoneConfirmation(id) {
    // 跳转到编辑里程碑确认页面
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=milestone_confirmation&contract_id=${contractId}&milestone_id=${id}&action=edit`;
}

// 删除里程碑确认
function deleteMilestoneConfirmation(id) {
    if (confirm('确定要删除此里程碑确认吗？')) {
        // 发送删除请求
        const contractId = new URLSearchParams(window.location.search).get('contract_id');
        window.location.href = `main.php?view=milestone_confirmation&contract_id=${contractId}&milestone_id=${id}&action=delete`;
    }
}

// 返回合同详情页面
function backToContractDetails() {
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=contract_details&contract_id=${contractId}&tab=milestone`;
}