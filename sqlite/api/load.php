<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'message' => 'Method Not Allowed'], 405);
}

try {
    $stmt = db()->query('SELECT data_json, updated_at FROM current_contract WHERE id = 1 LIMIT 1');
    $row = $stmt->fetch();

    if (!$row) {
        json_response([
            'success' => true,
            'data' => null,
            'updated_at' => null,
        ]);
    }

    $data = json_decode((string)$row['data_json'], true, 512, JSON_THROW_ON_ERROR);

    json_response([
        'success' => true,
        'data' => $data,
        'updated_at' => $row['updated_at'],
    ]);
} catch (Throwable $e) {
    error_log('[website-contract-builder] load failed: ' . $e->getMessage());
    json_response([
        'success' => false,
        'message' => '读取合同数据失败：' . $e->getMessage(),
    ], 500);
}
