<?php
include 'config.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$request_id = intval($_GET['id']);
$user_id = get_user_id();

$sql = "SELECT * FROM service_requests WHERE id = ? AND (user_id = ? OR ? = (SELECT id FROM users WHERE user_type = 'admin' LIMIT 1))";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $request_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $request = $result->fetch_assoc();
    echo json_encode(['success' => true, 'request' => $request]);
} else {
    echo json_encode(['success' => false, 'message' => 'Request not found']);
}
?>