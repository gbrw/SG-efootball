<?php
// مؤقت للتشخيص — احذفه بعد حل المشكلة
require_once __DIR__ . '/includes/config.php';

$dbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null;

if (!$dbUrl) {
    die('DATABASE_URL غير موجود');
}

$p    = parse_url($dbUrl);
$host = $p['host'] ?? '?';
$port = $p['port'] ?? '?';
$user = rawurldecode($p['user'] ?? '?');
$name = ltrim($p['path'] ?? '', '/');

// نفس منطق db.php
if (preg_match('/^db\.([a-z0-9]+)\.supabase\.co$/', $host, $m)) {
    $ref  = $m[1];
    $host = 'aws-0-eu-central-1.pooler.supabase.com';
    $port = 6543;
    if ($user === 'postgres') {
        $user = 'postgres.' . $ref;
    }
    echo "✅ تم تحويل URL إلى Pooler<br>";
    echo "REF: $ref<br>";
} else {
    echo "ℹ️ لم يتم التحويل (URL ليس direct Supabase)<br>";
}

echo "HOST: $host<br>";
echo "PORT: $port<br>";
echo "USER: $user<br>";
echo "DB:   $name<br><br>";

$dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";

try {
    $pdo = new PDO($dsn, $user, rawurldecode($p['pass'] ?? ''), [
        PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);
    echo "✅ الاتصال بقاعدة البيانات نجح!";
} catch (PDOException $e) {
    echo "❌ فشل: " . $e->getMessage();
}
