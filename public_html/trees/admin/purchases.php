<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Services\PurchaseService;

$service = new PurchaseService();
$error = null;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchaseId = (int) ($_POST['purchase_id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    $status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : '');

    if ($purchaseId <= 0 || $status === '') {
        $error = 'بيانات العملية غير صالحة.';
    } else {
        try {
            $service->changeStatus($purchaseId, $status);
            $message = $status === 'approved'
                ? 'تمت الموافقة على العملية بنجاح.'
                : 'تم رفض العملية بنجاح.';
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}

$pendingPurchases = $service->getPending();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشتريات المعلقة</title>
</head>
<body>
    <h1>لوحة الإدارة - العمليات المعلقة</h1>

    <?php if ($error !== null): ?>
        <p style="color: red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($message !== null): ?>
        <p style="color: green;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($pendingPurchases === []): ?>
        <p>لا توجد عمليات معلقة حاليًا.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>البريد</th>
                    <th>نوع الشجرة</th>
                    <th>المبلغ</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingPurchases as $purchase): ?>
                    <tr>
                        <td><?= (int) $purchase['id'] ?></td>
                        <td><?= htmlspecialchars((string) $purchase['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $purchase['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $purchase['tree_type'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $purchase['amount'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $purchase['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form method="post" action="" style="display: inline-block;">
                                <input type="hidden" name="purchase_id" value="<?= (int) $purchase['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit">Approve</button>
                            </form>
                            <form method="post" action="" style="display: inline-block;">
                                <input type="hidden" name="purchase_id" value="<?= (int) $purchase['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="/trees/register.php">العودة إلى صفحة التسجيل</a></p>
</body>
</html>
