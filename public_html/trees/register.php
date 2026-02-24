<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Services\AuthService;

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service = new AuthService();
        $userId = $service->register($_POST);
        $success = "تم إنشاء الحساب بنجاح. رقم المستخدم: {$userId}";
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التسجيل</title>
</head>
<body>
    <h1>تسجيل مستخدم جديد</h1>

    <?php if ($error !== null): ?>
        <p style="color: red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <p style="color: green;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <div>
            <label for="name">الاسم</label>
            <input id="name" name="name" type="text" required>
        </div>

        <div>
            <label for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" required>
        </div>

        <div>
            <label for="password">كلمة المرور</label>
            <input id="password" name="password" type="password" required minlength="6">
        </div>

        <div>
            <label for="tree_type">نوع الشجرة</label>
            <input id="tree_type" name="tree_type" type="text" value="general" required>
        </div>

        <button type="submit">إنشاء الحساب</button>
    </form>

    <p><a href="/trees/admin/purchases.php">الانتقال إلى إدارة العمليات المعلقة</a></p>
</body>
</html>
