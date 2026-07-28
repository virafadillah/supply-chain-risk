<?php
/**
 * Auto-extraction helper script for vendor.zip on Rumahweb hosting.
 * Called automatically by GitHub Actions workflow during deployment.
 */

// Secret key for authorization
$expectedKey = 'vira_secret_extract_key_2026';

$providedKey = $_GET['key'] ?? $_POST['key'] ?? '';

if (empty($providedKey) || !hash_equals($expectedKey, $providedKey)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Forbidden: Invalid authorization key.'
    ]);
    exit;
}

$baseDir = dirname(__DIR__); // Project root directory
$zipPath = $baseDir . '/vendor.zip';

// Fallback check if vendor.zip is inside public directory
if (!file_exists($zipPath)) {
    $zipPath = __DIR__ . '/vendor.zip';
}

if (!file_exists($zipPath)) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'vendor.zip file not found on server.'
    ]);
    exit;
}

if (!class_exists('ZipArchive')) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'PHP ZipArchive extension is not enabled on this server.'
    ]);
    exit;
}

$zip = new ZipArchive();
$res = $zip->open($zipPath);

if ($res === TRUE) {
    // Extract vendor archive to project root directory
    $zip->extractTo($baseDir);
    $zip->close();

    // Remove vendor.zip file after successful extraction
    @unlink($zipPath);

    // Clear Laravel caches if artisan exists
    $artisan = $baseDir . '/artisan';
    $cacheMessage = '';
    if (file_exists($artisan)) {
        @exec("php " . escapeshellarg($artisan) . " config:clear 2>&1");
        @exec("php " . escapeshellarg($artisan) . " view:clear 2>&1");
        $cacheMessage = ' Laravel cache cleared.';
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => 'vendor.zip extracted and deleted successfully.' . $cacheMessage
    ]);
} else {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to open vendor.zip. Code: ' . $res
    ]);
}
