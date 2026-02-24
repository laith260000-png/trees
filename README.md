# trees

نظام أولي لإدارة تسجيل المستخدمين وطلبات شراء الأشجار باستخدام `PDO`.

## الميزات المنفذة
- تسجيل مستخدم جديد.
- إنشاء `wallet` تلقائيًا عند التسجيل.
- إنشاء عملية `tree_purchase` بحالة `pending`.
- إنشاء سجل `tree_member` مرتبط بعملية الشراء.
- لوحة إدارة لعرض العمليات `pending` مع أزرار `approve / reject`.

## الصفحات
- `/trees/register.php` صفحة التسجيل.
- `/trees/admin/purchases.php` لوحة متابعة العمليات المعلقة.
- `/trees/index.php` روابط سريعة.

## قاعدة البيانات
- ملف السكيمة الجاهز: `public_html/trees/config/schema.sql`
- إعداد الاتصال: `public_html/trees/config/database.php`
