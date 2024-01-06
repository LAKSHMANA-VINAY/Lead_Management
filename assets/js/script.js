$(document).ready(function() {
    var table = $('#crmLeadTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "server.php",
            "type": "POST"
        },
        "columns": [
            { "data": "Lead_ID"},
            { "data": "Name" },
            { "data": "Mobile" },
            { "data": "State" },
            { "data": "Source" },
            { "data": "Status" },
            { "data": "DOR" },
            { "data": "Summary_DOR" },
            { "data": "Caller" },
            { "data": "Option" }
        ],
        "lengthMenu": [10, 25, 50, 100],  
        "pageLength": 10, 
        "order": [[0, 'asc']],
        "error": function(xhr, error, thrown) {
            console.log('DataTables error:', error, thrown);
            alert('DataTables error: ' + error);
        }
    });

    $('#crmLeadTable tbody').on('click', 'button', function() {
        var data = table.row($(this).parents('tr')).data();
        openStatusModal(data.Lead_ID);
    });
});

function openStatusModal(leadID) {
    $('#statusModal').data('leadID', leadID);
    $('#statusModal').modal('show');
}

function updateLeadStatus() {
    var leadID = $('#statusModal').data('leadID');
    var selectedStatus = $('#statusSelect').val();

    $.ajax({
        type: 'POST',
        url: 'update_lead_status.php', 
        data: { leadID: leadID, status: selectedStatus },
        success: function(response) {
            console.log(response);
            $('#statusModal').modal('hide');
            location.reload(true);
        },
        error: function(error) {
            console.error(error);
        }
    });
}
