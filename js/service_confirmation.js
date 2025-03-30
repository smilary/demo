/**
 * 服务确认相关JavaScript功能
 */

// 显示添加服务确认表单
function showAddServiceConfirmation() {
    // 显示添加服务确认表单
    document.getElementById('serviceForm').style.display = 'block';
}

// 隐藏服务确认表单
function hideServiceForm() {
    document.getElementById('serviceForm').style.display = 'none';
}

// 查看服务确认详情
function viewServiceConfirmation(id) {
    // 跳转到服务确认详情页面
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=service_confirmation&contract_id=${contractId}&service_id=${id}&action=view`;
}

// 编辑服务确认
function editServiceConfirmation(id) {
    // 跳转到编辑服务确认页面
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=service_confirmation&contract_id=${contractId}&service_id=${id}&action=edit`;
}

// 删除服务确认
function deleteServiceConfirmation(id) {
    if (confirm('确定要删除此服务确认吗？')) {
        // 发送删除请求
        const contractId = new URLSearchParams(window.location.search).get('contract_id');
        window.location.href = `main.php?view=service_confirmation&contract_id=${contractId}&service_id=${id}&action=delete`;
    }
}

// 返回合同详情页面
function backToContractDetails() {
    const contractId = new URLSearchParams(window.location.search).get('contract_id');
    window.location.href = `main.php?view=contract_details&contract_id=${contractId}&tab=service`;
}