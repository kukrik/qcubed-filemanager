<?php

ob_start();
try {
    require_once __DIR__ . '/../../../qcubed.inc.php';
    require_once __DIR__ . '/../../src/Control/DownloadResponse.php';
    ini_set('display_errors', '0');
    $token = $_GET['token'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/D', $token)) {
        throw new RuntimeException('Invalid download link.', 400);
    }
    $download = $_SESSION['vauuDownloads'][$token] ?? null;
    if (!is_array($download) || ($download['expires'] ?? 0) < time() || ($download['kind'] ?? '') !== 'zip') {
        throw new RuntimeException('The download link has expired. Start the download again.', 410);
    }
    $root = realpath(APP_UPLOADS_TEMP_DIR . '/_files/zip');
    $path = realpath($download['path']);
    if ($root === false || $path === false || !str_starts_with($path, rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) || !is_file($path)) {
        throw new RuntimeException('The download file is no longer available.', 404);
    }
    DownloadResponse::send($path, basename($path), 'application/zip', true, $token);
} catch (Throwable $error) {
    error_log('Download error: ' . $error->getMessage());
    while (ob_get_level() > 0) if (!@ob_end_clean()) break;
    $status = in_array($error->getCode(), [400, 404, 410], true) ? $error->getCode() : 500;
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $status === 500 ? 'The download could not be started. Please try again.' : $error->getMessage();
}
exit;
