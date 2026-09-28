<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method Not Allowed'], 405);
}

try {
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        json_response(['success' => false, 'message' => '保存内容为空。'], 400);
    }

    // 当前合同数据通常只有几十 KB；限制异常大请求，避免误写入。
    if (strlen($raw) > 2 * 1024 * 1024) {
        json_response(['success' => false, 'message' => '合同数据超过允许大小。'], 413);
    }

    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($data)) {
        json_response(['success' => false, 'message' => '合同数据格式无效。'], 400);
    }

    $normalized = json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
    );

    $updatedAt = date(DATE_ATOM);

    $sql = 'INSERT INTO current_contract (id, data_json, updated_at)
            VALUES (1, :data_json, :updated_at)
            ON CONFLICT(id) DO UPDATE SET
                data_json = excluded.data_json,
                updated_at = excluded.updated_at';

    $stmt = db()->prepare($sql);
    $stmt->execute([
        ':data_json' => $normalized,
        ':updated_at' => $updatedAt,
    ]);

    json_response([
        'success' => true,
        'updated_at' => $updatedAt,
    ]);
} catch (JsonException $e) {
    json_response(['success' => false, 'message' => 'JSON 数据格式错误。'], 400);
} catch (Throwable $e) {
    error_log('[website-contract-builder] save failed: ' . $e->getMessage());
    json_response([
        'success' => false,
        'message' => '保存合同数据失败：' . $e->getMessage(),
    ], 500);
}
