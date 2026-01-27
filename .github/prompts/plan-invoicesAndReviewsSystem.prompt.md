# Plan: Invoices & Reviews System Implementation

This plan adds automatic invoice generation when orders are completed, plus a reviews system allowing users to rate completed orders. The implementation will follow your existing service layer architecture and leverage the Order status change point in [OrderService](app/Http/Services/Admin/OrderService.php#L15).

## Steps

1. **Create migrations and models** for `invoices` table (invoice_number, order_id, total, pdf_url) and `reviews` table (user_id, order_id, rating, comment), adding relationships to [Order](app/Models/Order.php), [User](app/Models/User.php) models with hasOne/belongsTo constraints

2. **Install PDF generation** via `composer require barryvdh/laravel-dompdf`, create InvoiceService with `generateInvoice()` method for unique invoice numbers and PDF creation, design invoice blade template with order details

3. **Implement automatic invoice creation** by modifying [OrderService::updateStatus()](app/Http/Services/Admin/OrderService.php#L23) to detect when status becomes "completed" and trigger InvoiceService to generate invoice, store PDF in storage/app/invoices/, save record with pdf_url

4. **Create Review system endpoints** with ReviewService for creating/validating reviews (1-5 rating, one per order per user), add API routes for users to submit reviews on completed orders, add admin routes leveraging existing review permissions from [PermissionSeeder](database/seeders/PermissionSeeder.php)

5. **Add validation and policies** ensuring users can only review their own completed orders once, create ReviewPolicy for authorization, add eager loading for `invoice` and `review` relationships in Order queries

## Further Considerations

1. **PDF storage location**: Store in `storage/app/invoices/` (private) or `storage/app/public/invoices/` (publicly accessible)? Private requires signed URLs for download.
answer : private with signed URLs.

2. **Invoice number format**: What pattern? Suggestions: `INV-{year}-{increment}` (e.g., INV-2026-00001) or `INV-{order_id}-{timestamp}`?
answer : INV-{year}-{increment}
3. **Review approval workflow**: Should reviews require admin approval before being visible, or auto-publish? The seeder includes approve/reject permissions suggesting moderation.
answer : auto-publish

4. **Email notification**: Should users receive an email with the invoice PDF when order is completed?
answer : yes
