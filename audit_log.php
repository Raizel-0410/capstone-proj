<?php
require 'db_connect.php';

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // in case of proxy, take first IP
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }
}

function log_admin_action($pdo, $user_id, $action) {
    $ip = get_client_ip();
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $pdo->prepare("INSERT INTO admin_audit_logs (user_id, action, ip_address, user_agent) 
                           VALUES (:user_id, :action, :ip, :agent)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':action'  => $action,
        ':ip'      => $ip,
        ':agent'   => $agent
    ]);
}

function log_landing_action($pdo, $user_id, $action) {
    $ip = get_client_ip();
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $pdo->prepare("INSERT INTO landing_audit_logs (user_id, action, ip_address, user_agent) 
                           VALUES (:user_id, :action, :ip, :agent)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':action'  => $action,
        ':ip'      => $ip,
        ':agent'   => $agent
    ]);
}
?>


