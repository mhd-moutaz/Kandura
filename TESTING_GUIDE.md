# Testing Guide - Invoices & Reviews System

## 🧪 Manual Testing Steps

### Prerequisites
1. Make sure migrations have been run: `php artisan migrate`
2. Ensure mail configuration is set up in `.env`
3. Create a test order with CONFIRMED status

---

## 📋 Test Invoice Generation

### Test 1: Automatic Invoice Creation

**Steps**:
1. Log in as admin
2. Navigate to an order with status "confirmed" or "processing"
3. Change the order status to "completed"
4. Check the response

**Expected Results**:
- ✅ Invoice record created in `invoices` table
- ✅ PDF file created in `storage/app/invoices/invoice_INV-2026-00001.pdf`
- ✅ Email sent to customer with PDF attachment
- ✅ Invoice has unique invoice number

**Verify in Database**:
```sql
SELECT * FROM invoices WHERE order_id = [ORDER_ID];
```

**Verify File Created**:
```bash
ls storage/app/invoices/
```

### Test 2: Invoice Download (User)

**API Endpoint**: ``

**Headers**:
```
Authorization: Bearer {USER_TOKEN}
Accept: application/json
```

**Expected Response** (200):
```json
{
    "invoice_number": "INV-2026-00001",
    "total": "150.00",
    "download_url": "https://example.com/storage/invoices/...?signature=...",
    "created_at": "2026-01-24 12:00:00"
}
```

**Test Cases**:
- ✅ User can access their own order's invoice
- ❌ User cannot access other user's invoice (403)
- ❌ Invoice not found for pending orders (404)

### Test 3: Invoice Regeneration (Admin)

**API Endpoint**: `POST /orders/{order}/invoice/regenerate`

**Expected**:
- ✅ Old invoice deleted
- ✅ New invoice created with new PDF
- ✅ New invoice number generated

---

## ⭐ Test Review System

### Test 4: Create Review (Success)

**API Endpoint**: `POST /api/users/reviews/orders/{order}`

**Headers**:
```
Authorization: Bearer {USER_TOKEN}
Content-Type: application/json
Accept: application/json
```

**Body**:
```json
{
    "rating": 5,
    "comment": "Excellent service and quality!"
}
```

**Expected Response** (200):
```json
{
    "data": {
        "id": 1,
        "rating": 5,
        "comment": "Excellent service and quality!",
        "created_at": "2026-01-24 12:00:00",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "order": {
            "id": 1,
            "status": "completed",
            "total": "150.00"
        }
    }
}
```

**Requirements**:
- ✅ Order must be COMPLETED
- ✅ User must own the order
- ✅ User hasn't reviewed this order before

### Test 5: Review Validation Tests

**Test Case 5.1**: Review on Pending Order
```json
{
    "rating": 5,
    "comment": "Great!"
}
```
**Expected**: 400 Error - "You can only review completed orders"

**Test Case 5.2**: Invalid Rating (>5)
```json
{
    "rating": 6,
    "comment": "Great!"
}
```
**Expected**: 422 Validation Error - "Rating must be at most 5"

**Test Case 5.3**: Invalid Rating (<1)
```json
{
    "rating": 0,
    "comment": "Poor service"
}
```
**Expected**: 422 Validation Error - "Rating must be at least 1"

**Test Case 5.4**: Duplicate Review
- Create review for order #1
- Try creating another review for same order
**Expected**: 400 Error - "You have already reviewed this order"

**Test Case 5.5**: Review Other User's Order
- User A tries to review User B's order
**Expected**: 403 Error - "You cannot review an order that is not yours"

### Test 6: Get User Reviews

**API Endpoint**: `GET /api/users/reviews`

**Query Parameters**:
- `rating` (optional): Filter by rating (1-5)
- `per_page` (optional): Items per page

**Expected Response** (200):
```json
{
    "data": [
        {
            "id": 1,
            "rating": 5,
            "comment": "Excellent!",
            "created_at": "2026-01-24 12:00:00",
            "user": {...},
            "order": {...}
        }
    ],
    "links": {...},
    "meta": {...}
}
```

### Test 7: Admin Review Management

**Test 7.1**: Get All Reviews
**Endpoint**: `GET /reviews`
**Expected**: List of all reviews from all users

**Test 7.2**: Get Review Statistics
**Endpoint**: `GET /reviews/stats`
**Expected Response**:
```json
{
    "total_reviews": 50,
    "average_rating": 4.35,
    "rating_distribution": {
        "5": 25,
        "4": 15,
        "3": 5,
        "2": 3,
        "1": 2
    }
}
```

**Test 7.3**: Delete Review
**Endpoint**: `DELETE /reviews/{review}`
**Expected**: 200 - "Review deleted successfully"

---

## 📧 Test Email Notification

### Test 8: Invoice Email

**Trigger**: Change order status to COMPLETED

**Verify**:
1. Check mail logs or mail trap (Mailtrap.io, MailHog, etc.)
2. Confirm email contains:
   - ✅ Correct recipient (order user's email)
   - ✅ Subject: "Invoice for Order #X"
   - ✅ Invoice number and order details
   - ✅ PDF attachment
   - ✅ Bilingual content (Arabic/English)

**Test in Local Dev**:
```env
MAIL_MAILER=log
```
Then check `storage/logs/laravel.log` for email content

---

## 🔐 Authorization Tests

### Test 9: Permission-Based Access

**User Permissions** (API Guard):
- ✅ `view review` - Can view own reviews
- ✅ `create review` - Can create reviews

**Admin Permissions** (Web Guard):
- ✅ `view all reviews` - Can view all reviews
- ✅ `delete review` - Can delete any review
- ✅ `view all orders` - Can view invoices
- ✅ `change order status` - Can regenerate invoices

**Test Cases**:
1. User without `create review` permission tries to create review → 403
2. Admin without `view all reviews` tries to access reviews → 403
3. User tries to access admin review endpoints → 401/403

---

## 🗄️ Database Constraint Tests

### Test 10: Unique Constraints

**Test 10.1**: Duplicate Invoice
- Try manually creating two invoices for same order
**Expected**: Database error (unique constraint violation)

**Test 10.2**: Duplicate Review
- Try manually inserting duplicate review (same user_id, order_id)
**Expected**: Database error (unique constraint violation)

---

## 📱 Integration Tests

### Test 11: Complete Workflow

**Scenario**: New order to completion with invoice and review

1. User creates order (status: PENDING)
2. User confirms payment (status: CONFIRMED)
3. Admin updates to PROCESSING
4. Admin updates to COMPLETED
   - ✅ Invoice auto-generated
   - ✅ Email sent with PDF
5. User receives email with invoice
6. User downloads invoice via API
7. User submits review
   - ✅ Review saved
   - ✅ Only one review allowed

**Full Flow Verification**:
```sql
-- Check invoice
SELECT * FROM invoices WHERE order_id = [ORDER_ID];

-- Check review
SELECT * FROM reviews WHERE order_id = [ORDER_ID];

-- Verify one-to-one relationships
SELECT o.id, o.status, i.invoice_number, r.rating
FROM orders o
LEFT JOIN invoices i ON o.id = i.order_id
LEFT JOIN reviews r ON o.id = r.order_id
WHERE o.id = [ORDER_ID];
```

---

## 🐛 Error Handling Tests

### Test 12: Edge Cases

**Test 12.1**: Invoice for Cancelled Order
- Change order to CANCELLED, then to COMPLETED
**Expected**: Invoice created normally

**Test 12.2**: Missing Order Relations
- Try generating invoice for order without user
**Expected**: Should handle gracefully

**Test 12.3**: Storage Permissions
- Remove write permissions from `storage/app/invoices/`
**Expected**: Error logged, proper error response

**Test 12.4**: Invalid PDF Generation
- Test with order that has special characters in items
**Expected**: PDF generated successfully with proper escaping

---

## 🔧 Configuration Verification

### Test 13: Environment Setup

**Check `.env` Settings**:
```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@kandura.com"
MAIL_FROM_NAME="${APP_NAME}"

# Filesystem
FILESYSTEM_DISK=local
```

**Verify Storage Link**:
```bash
php artisan storage:link
```

**Check Permissions**:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## ✅ Checklist

### Database
- [ ] `invoices` table created
- [ ] `reviews` table created
- [ ] Unique constraints working
- [ ] Foreign keys properly set

### Models & Relationships
- [ ] Invoice model exists
- [ ] Review model exists
- [ ] Order→Invoice relationship
- [ ] Order→Review relationship
- [ ] User→Reviews relationship

### Services
- [ ] InvoiceService generates PDFs
- [ ] ReviewService validates properly
- [ ] Invoice numbers unique and sequential

### API Endpoints
- [ ] User can create review
- [ ] User can view own reviews
- [ ] User can download invoice
- [ ] Admin can view all reviews
- [ ] Admin can view statistics
- [ ] Admin can delete reviews

### Automation
- [ ] Invoice auto-generates on COMPLETED
- [ ] Email sent with attachment
- [ ] Only one invoice per order

### Security
- [ ] Users can't access other users' data
- [ ] Permissions enforced
- [ ] Signed URLs expire correctly
- [ ] Private storage working

### Email
- [ ] Email template renders correctly
- [ ] PDF attachment included
- [ ] Bilingual content displays

---

## 📊 Sample Test Data

### Create Test Order (via Tinker)
```php
php artisan tinker

$user = User::find(1);
$address = $user->addresses->first();

$order = Order::create([
    'user_id' => $user->id,
    'address_id' => $address->id,
    'status' => 'confirmed',
    'total' => 150.00,
    'payment_method' => 'card'
]);

// Add order items
$design = Design::first();
$measurement = Measurement::first();

$order->orderItems()->create([
    'design_id' => $design->id,
    'measurement_id' => $measurement->id,
    'quantity' => 2,
    'unit_price' => 75.00,
    'total_price' => 150.00
]);

// Update to completed (triggers invoice generation)
$order->update(['status' => 'completed']);
```

---

## 🎯 Success Criteria

All tests should pass with:
- ✅ No database errors
- ✅ Proper authorization enforcement
- ✅ Valid PDF generation
- ✅ Emails sent successfully
- ✅ Unique constraints enforced
- ✅ API returns correct status codes
- ✅ Relationships work properly

Happy Testing! 🚀
