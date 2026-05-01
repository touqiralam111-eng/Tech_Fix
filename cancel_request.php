<?php
include 'config.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$request_id = intval($_POST['id']);
$user_id = get_user_id();

// Verify ownership
$check_sql = "SELECT id FROM service_requests WHERE id = ? AND user_id = ? AND status = 'pending'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $request_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 1) {
    $update_sql = "UPDATE service_requests SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $request_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error cancelling request']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Request not found or cannot be cancelled']);
}
?>