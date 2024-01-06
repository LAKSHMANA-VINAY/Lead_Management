<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connection = mysqli_connect("localhost", "root", "", "kalam_test");

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $leadID = $_POST['leadID'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE crm_lead_master_data SET Status = '$status' WHERE Lead_ID = $leadID";
    $updateResult = mysqli_query($connection, $updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update lead status']);
    }

    mysqli_close($connection);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
