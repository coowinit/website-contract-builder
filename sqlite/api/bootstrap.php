<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Shanghai');

header_remove('X-Powered-By');

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('服务器未启用 PDO_SQLite 扩展。');
    }

    $databaseDir = dirname(__DIR__) . '/database';
    if (!is_dir($databaseDir) && !mkdir($databaseDir, 0750, true) && !is_dir($databaseDir)) {
        throw new RuntimeException('无法创建 database 目录。');
    }

    if (!is_writable($databaseDir)) {
        throw new RuntimeException('database 目录不可写，请检查目录权限。');
    }

    $databaseFile = $databaseDir . '/contract.sqlite';
    $pdo = new PDO('sqlite:' . $databaseFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // 当前工具只有一个合同记录，不需要复杂并发模型。
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS current_contract (
            id INTEGER PRIMARY KEY CHECK (id = 1),
            data_json TEXT NOT NULL,
            updated_at TEXT NOT NULL
        )'
    );

    return $pdo;
}
