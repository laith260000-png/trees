<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

class AuthService
{
    public function register(array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $treeType = trim((string) ($input['tree_type'] ?? 'general'));

        if ($name === '' || $email === '' || $password === '') {
            throw new RuntimeException('جميع الحقول مطلوبة.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('صيغة البريد الإلكتروني غير صحيحة.');
        }

        if (mb_strlen($password) < 6) {
            throw new RuntimeException('كلمة المرور يجب ألا تقل عن 6 أحرف.');
        }

        $pdo = Database::getInstance();

        $existsStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $existsStmt->execute(['email' => $email]);

        if ($existsStmt->fetch()) {
            throw new RuntimeException('البريد الإلكتروني مسجل مسبقًا.');
        }

        $pdo->beginTransaction();

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $userStmt = $pdo->prepare(
                'INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, NOW())'
            );
            $userStmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'user',
            ]);
            $userId = (int) $pdo->lastInsertId();

            $walletStmt = $pdo->prepare(
                'INSERT INTO wallets (user_id, balance, created_at) VALUES (:user_id, :balance, NOW())'
            );
            $walletStmt->execute([
                'user_id' => $userId,
                'balance' => 0,
            ]);
            $walletId = (int) $pdo->lastInsertId();

            $purchaseStmt = $pdo->prepare(
                'INSERT INTO tree_purchases (user_id, wallet_id, tree_type, amount, status, created_at) VALUES (:user_id, :wallet_id, :tree_type, :amount, :status, NOW())'
            );
            $purchaseStmt->execute([
                'user_id' => $userId,
                'wallet_id' => $walletId,
                'tree_type' => $treeType,
                'amount' => 0,
                'status' => 'pending',
            ]);
            $purchaseId = (int) $pdo->lastInsertId();

            $memberStmt = $pdo->prepare(
                'INSERT INTO tree_members (user_id, purchase_id, status, joined_at) VALUES (:user_id, :purchase_id, :status, NOW())'
            );
            $memberStmt->execute([
                'user_id' => $userId,
                'purchase_id' => $purchaseId,
                'status' => 'pending',
            ]);

            $pdo->commit();

            return $userId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException('فشل التسجيل: ' . $e->getMessage());
        }
    }
}
