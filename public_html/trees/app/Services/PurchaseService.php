<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class PurchaseService
{
    public function getPending(): array
    {
        $pdo = Database::getInstance();

        $stmt = $pdo->query(
            'SELECT tp.id, tp.user_id, tp.wallet_id, tp.tree_type, tp.amount, tp.status, tp.created_at, u.name, u.email
             FROM tree_purchases tp
             INNER JOIN users u ON u.id = tp.user_id
             WHERE tp.status = "pending"
             ORDER BY tp.created_at ASC'
        );

        return $stmt->fetchAll();
    }

    public function changeStatus(int $purchaseId, string $status): void
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new RuntimeException('الحالة المطلوبة غير مدعومة.');
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $updatePurchase = $pdo->prepare(
                'UPDATE tree_purchases SET status = :status, updated_at = NOW() WHERE id = :id AND status = "pending"'
            );
            $updatePurchase->execute([
                'status' => $status,
                'id' => $purchaseId,
            ]);

            if ($updatePurchase->rowCount() === 0) {
                throw new RuntimeException('لم يتم العثور على عملية pending بهذا الرقم.');
            }

            $updateMember = $pdo->prepare(
                'UPDATE tree_members SET status = :status WHERE purchase_id = :purchase_id'
            );
            $updateMember->execute([
                'status' => $status,
                'purchase_id' => $purchaseId,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException('فشل تحديث الحالة: ' . $e->getMessage());
        }
    }
}
