<?php

$connection = mysqli_connect("localhost", "root", "", "kalam_test");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$requestData = $_REQUEST;

$columns = array(
    array('db' => 'Lead_ID', 'dt' => 0),
    array('db' => 'Name', 'dt' => 1),
    array('db' => 'Mobile', 'dt' => 2),
    array('db' => 'State', 'dt' => 3),
    array('db' => 'Source', 'dt' => 4),
    array('db' => 'DOR', 'dt' => 5),
    array('db' => 'Summary_DOR', 'dt' => 6),
    array('db' => 'Caller', 'dt' => 7),
    array('db' => 'Lead_ID', 'dt' => 8)
);

$defaultLimit = isset($requestData['length']) ? intval($requestData['length']) : 10;

$page = isset($requestData['start']) ? floor($requestData['start'] / $defaultLimit) + 1 : 1;
$offset = ($page - 1) * $defaultLimit;

$sql = "SELECT Lead_ID, Name, 
CONCAT(Mobile, '/', Alternate_Mobile, '/', Whatsapp) AS Mobile,
CONCAT(State, '/', City) AS State,
Source, Status,DOR, 
(SELECT MAX(DOR) FROM crm_calling_status WHERE Lead_ID = crm_lead_master_data.Lead_ID) AS Summary_DOR,
(SELECT name FROM CRM_Admin WHERE Name = crm_lead_master_data.Caller) AS Caller
FROM crm_lead_master_data";

if (!empty($requestData['search']['value'])) {
    $sql .= " WHERE (Lead_ID LIKE '%" . $requestData['search']['value'] . "%'
    OR Name LIKE '%" . $requestData['search']['value'] . "%'
    OR Mobile LIKE '%" . $requestData['search']['value'] . "%' 
    OR State LIKE '%" . $requestData['search']['value'] . "%' 
    OR Source LIKE '%" . $requestData['search']['value'] . "%' 
    OR DOR LIKE '%" . $requestData['search']['value'] . "%' 
    OR Status LIKE '%" . $requestData['search']['value'] . "%'
    OR Caller LIKE '%" . $requestData['search']['value'] . "%'
    )";
}

if (!empty($requestData['order'])) {
    $orderByColumnIndex = $requestData['order'][0]['column'];
    $orderByColumnName = $columns[$orderByColumnIndex]['db'];
    $orderDir = $requestData['order'][0]['dir'];
    $sql .= " ORDER BY $orderByColumnName $orderDir";
}

$sql .= " LIMIT " . $defaultLimit . " OFFSET " . $offset;

$query = mysqli_query($connection, $sql);

if (!$query) {
    $output = array(
        "error" => "Query failed: " . mysqli_error($connection),
        "sql" => $sql 
    );

    header('Content-Type: application/json');
    echo json_encode($output);
    exit;
}

$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $row['Option'] = "<button onclick='editLeadStatus(" . $row['Lead_ID'] . ")'>Edit</button>";
    $data[] = $row;
}

$totalData = mysqli_num_rows(mysqli_query($connection, "SELECT * FROM crm_lead_master_data"));

if (!empty($requestData['search']['value'])) {
    $totalFiltered = mysqli_num_rows(mysqli_query($connection, $sql));
} else {
    $totalFiltered = $totalData;
}

$output = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

mysqli_close($connection);

header('Content-Type: application/json');
echo json_encode($output);
?>
