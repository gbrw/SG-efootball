<?php
/**
 * PDO helper — يدعم MySQL (محلي) و PostgreSQL (Railway / Supabase)
 * إذا كان DATABASE_URL موجوداً يستخدم PostgreSQL، وإلا MySQL.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

    if ($dbUrl) {
        // PostgreSQL via DATABASE_URL (Railway / Supabase)
        $p    = parse_url($dbUrl);
        $host = $p['host'];
        $port = $p['port'] ?? 5432;
        $name = ltrim($p['path'], '/');
        $user = rawurldecode($p['user']);
        $pass = rawurldecode($p['pass'] ?? '');

        // Auto-rewrite direct Supabase URL → pgBouncer pooler URL
        // Direct:  db.{ref}.supabase.co:5432  / user: postgres
        // Pooler:  aws-0-{region}.pooler.supabase.com:6543  / user: postgres.{ref}
        if (preg_match('/^db\.([a-z0-9]+)\.supabase\.co$/', $host, $m)) {
            $ref  = $m[1];
            $host = 'aws-0-eu-central-1.pooler.supabase.com';
            $port = 6543;
            // prefix user with project ref if not already prefixed
            if ($user === 'postgres') {
                $user = 'postgres.' . $ref;
            }
        }

        // sslmode=require + emulate_prepares for pgBouncer transaction mode
        $dsn  = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
        $opts[PDO::ATTR_EMULATE_PREPARES] = true;  // required for pgBouncer transaction mode
    } else {
        // MySQL for local development
        $dsn  = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4;port=' . DB_PORT;
        $user = DB_USER;
        $pass = DB_PASS;
    }

    try {
        $pdo = new PDO($dsn, $user, $pass, $opts);
    } catch (PDOException $e) {
        throw new RuntimeException('DB connection failed: ' . $e->getMessage());
    }

    return $pdo;
}

/**
 * Returns true when connected to PostgreSQL (Railway/Supabase).
 */
function isPostgres(): bool
{
    return db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
}
