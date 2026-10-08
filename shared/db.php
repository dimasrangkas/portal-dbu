<?php
/* ============================================================
   Koneksi PDO tunggal + pembantu kueri ringkas
   ============================================================ */
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        $detail = APP_DEBUG ? htmlspecialchars($e->getMessage(), ENT_QUOTES) : '';
        exit(
            '<div style="font-family:system-ui;max-width:640px;margin:80px auto;padding:28px;'
            . 'border:1px solid #E1E6EB;border-radius:12px;">'
            . '<h2 style="margin:0 0 10px;color:#00396B">Koneksi basis data gagal</h2>'
            . '<p style="color:#3B4B5A;line-height:1.6">Pastikan MySQL/MariaDB berjalan dan basis data '
            . '<code>' . DB_NAME . '</code> sudah dibuat. Jalankan <code>database/schema.sql</code> lalu '
            . '<code>database/seed.sql</code>.</p>'
            . ($detail ? '<pre style="background:#F5F6F8;padding:12px;border-radius:8px;overflow:auto">' . $detail . '</pre>' : '')
            . '</div>'
        );
    }

    return $pdo;
}

/** Ambil banyak baris. */
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Ambil satu baris (atau null). */
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/** Ambil satu nilai skalar. */
function db_value(string $sql, array $params = [], $default = null)
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $val = $stmt->fetchColumn();
    return $val === false ? $default : $val;
}

/** Jalankan perintah tulis, kembalikan jumlah baris terpengaruh. */
function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/** INSERT dari array assoc, kembalikan id baru. */
function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql  = sprintf(
        'INSERT INTO `%s` (%s) VALUES (%s)',
        $table,
        implode(',', array_map(fn($c) => "`$c`", $cols)),
        implode(',', array_map(fn($c) => ":$c", $cols))
    );
    $stmt = db()->prepare($sql);
    $stmt->execute($data);
    return (int) db()->lastInsertId();
}

/** UPDATE berdasarkan id. */
function db_update(string $table, array $data, int $id): int
{
    $sets = implode(',', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
    $data['__id'] = $id;
    $stmt = db()->prepare("UPDATE `$table` SET $sets WHERE `id` = :__id");
    $stmt->execute($data);
    return $stmt->rowCount();
}

/** DELETE berdasarkan id. */
function db_delete(string $table, int $id): int
{
    $stmt = db()->prepare("DELETE FROM `$table` WHERE `id` = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount();
}
