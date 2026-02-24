<?php

declare(strict_types=1);

session_start();

spl_autoload_register(function ($class) {

    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    // تحقق أن الكلاس يبدأ بـ App\
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    // إزالة App\
    $relative_class = substr($class, strlen($prefix));

    // تحويل namespace إلى مسار
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
