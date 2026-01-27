# API Documentation - Invoices & Reviews

## 🔐 Authentication
All endpoints require authentication unless specified otherwise.
- **User endpoints**: Bearer token (Laravel Passport - API guard)
- **Admin endpoints**: Session-based (Web guard)

---

## 📄 Invoice Endpoints

### 1. Get Invoice for Order (User)

**Endpoint**: `GET /api/users/orders/{order}/invoice`

**Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Response** (200 OK):
```json
{
    "invoice_number": "INV-2026-00001",
    "total": "150.00",
    "download_url": "https://example.com/storage/invoices/invoice_INV-2026-00001.pdf?signature=...",
    "created_at": "2026-01-24 12:00:00"
}
```

**Error Responses**:
- `403 Forbidden`: Not your order
- `404 Not Found`: Invoice doesn't exist (order not completed yet)

**Notes**:
- Download URL is a signed URL valid for 30 minutes
- Only available for COMPLETED orders
- User can only access their own orders

---

### 2. Get Invoice for Order (Admin)

**Endpoint**: `GET /orders/{order}/invoice`

**Headers**:
```
Cookie: laravel_session={session_id}
Accept: application/json
```

**Permissions Required**: `view all orders`

**Response** (200 OK):
```json
{
    "invoice_number": "INV-2026-00001",
    "order_id": 1,
    "total": "150.00",
    "download_url": "https://example.com/storage/invoices/invoice_INV-2026-00001.pdf?signature=...",
    "created_at": "2026-01-24 12:00:00"
}
```

---

### 3. Regenerate Invoice (Admin)

**Endpoint**: `POST /orders/{order}/invoice/regenerate`

**Headers**:
```
Cookie: laravel_session={session_id}
Accept: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Permissions Required**: `change order status`

**Response** (200 OK):
```json
{
    "message": "Invoice regenerated successfully",
    "invoice_number": "INV-2026-00002"
}
```

**Notes**:
- Deletes old invoice and PDF
- Creates new invoice with new number
- Useful if invoice data was incorrect

---

## ⭐ Review Endpoints (User)

### 4. Get My Reviews

**Endpoint**: `GET /api/users/reviews`

**Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters**:
- `rating` (optional): Filter by rating (1-5)
- `per_page` (optional): Items per page (default: 15)

**Permissions Required**: `view review`

**Response** (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "rating": 5,
            "comment": "Excellent service!",
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
    ],
    "links": {
        "first": "http://example.com/api/users/reviews?page=1",
        "last": "http://example.com/api/users/reviews?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "per_page": 15,
        "to": 1,
        "total": 1
    }
}
```

---

### 5. Create Review for Order

**Endpoint**: `POST /api/users/reviews/orders/{order}`

**Headers**:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Permissions Required**: `create review`

**Request Body**:
```json
{
    "rating": 5,
    "comment": "Amazing quality and fast delivery!"
}
```

**Validation Rules**:
- `rating`: Required, integer, 1-5
- `comment`: Optional, string, max 1000 characters

**Response** (200 OK):
```json
{
    "data": {
        "id": 1,
        "rating": 5,
        "comment": "Amazing quality and fast delivery!",
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

**Error Responses**:

**400 Bad Request** - Order not completed:
```json
{
    "message": "يمكنك فقط تقييم الطلبات المكتملة / You can only review completed orders"
}
```

**403 Forbidden** - Not your order:
```json
{
    "message": "لا يمكنك تقييم طلب ليس خاص بك / You cannot review an order that is not yours"
}
```

**400 Bad Request** - Already reviewed:
```json
{
    "message": "لقد قمت بالفعل بتقييم هذا الطلب / You have already reviewed this order"
}
```

**422 Unprocessable Entity** - Validation error:
```json
{
    "message": "The rating field must be between 1 and 5.",
    "errors": {
        "rating": [
            "يجب أن يكون التقييم على الأقل 1 / Rating must be at least 1"
        ]
    }
}
```

---

### 6. Delete My Review

**Endpoint**: `DELETE /api/users/reviews/{review}`

**Headers**:
```
Authorization: Bearer {token}
Accept: application/json
```

**Permissions Required**: `view review`

**Response** (200 OK):
```json
{
    "message": "تم حذف التقييم بنجاح / Review deleted successfully"
}
```

**Error Responses**:
- `403 Forbidden`: Not your review
- `404 Not Found`: Review doesn't exist

---

## 👨‍💼 Review Endpoints (Admin)

### 7. Get All Reviews

**Endpoint**: `GET /reviews`

**Headers**:
```
Cookie: laravel_session={session_id}
Accept: application/json
```

**Query Parameters**:
- `rating` (optional): Filter by rating (1-5)
- `search` (optional): Search in comments or user names
- `sort_by` (optional): Sort field (default: created_at)
- `sort_dir` (optional): Sort direction (asc/desc, default: desc)
- `per_page` (optional): Items per page (default: 15)

**Permissions Required**: `view all reviews`

**Response** (200 OK):
```json
{
    "data": [
        {
            "id": 1,
            "rating": 5,
            "comment": "Excellent service!",
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
        },
        {
            "id": 2,
            "rating": 4,
            "comment": "Good quality",
            "created_at": "2026-01-23 10:30:00",
            "user": {
                "id": 2,
                "name": "Jane Smith",
                "email": "jane@example.com"
            },
            "order": {
                "id": 5,
                "status": "completed",
                "total": "200.00"
            }
        }
    ],
    "links": {...},
    "meta": {...}
}
```

---

### 8. Get Review Statistics

**Endpoint**: `GET /reviews/stats`

**Headers**:
```
Cookie: laravel_session={session_id}
Accept: application/json
```

**Permissions Required**: `view all reviews`

**Response** (200 OK):
```json
{
    "total_reviews": 127,
    "average_rating": 4.35,
    "rating_distribution": {
        "5": 65,
        "4": 40,
        "3": 15,
        "2": 5,
        "1": 2
    }
}
```

**Use Cases**:
- Dashboard display
- Customer satisfaction metrics
- Quality monitoring

---

### 9. Delete Review (Admin)

**Endpoint**: `DELETE /reviews/{review}`

**Headers**:
```
Cookie: laravel_session={session_id}
Accept: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Permissions Required**: `delete review`

**Response** (200 OK):
```json
{
    "message": "Review deleted successfully"
}
```

**Notes**:
- Admin can delete any review
- Used for moderation (inappropriate content, spam, etc.)

---

## 🔄 Automatic Processes

### Invoice Auto-Generation

**Trigger**: Order status changed to `completed`

**Process**:
1. Admin updates order status to "completed"
2. System automatically:
   - Generates unique invoice number (INV-2026-00001)
   - Creates PDF from template
   - Stores PDF in `storage/app/invoices/`
   - Saves invoice record in database
   - Sends email to customer with PDF attachment

**Invoice Number Format**: `INV-{YEAR}-{INCREMENT}`
- Year: Current year (2026)
- Increment: 5-digit sequential number per year (00001, 00002, etc.)

**Email Content**:
- Subject: "Invoice for Order #{order_id}"
- Bilingual (Arabic/English)
- Order summary
- Invoice number and total
- PDF attachment

---

## 📋 Business Rules

### Invoices
1. ✅ One invoice per order (hasOne relationship)
2. ✅ Only created for COMPLETED orders
3. ✅ Invoice number is unique across system
4. ✅ PDF stored privately (requires signed URL)
5. ✅ Signed URLs expire after 30 minutes
6. ✅ Email automatically sent on creation

### Reviews
1. ✅ One review per user per order (unique constraint)
2. ✅ Only COMPLETED orders can be reviewed
3. ✅ User must own the order
4. ✅ Rating must be 1-5
5. ✅ Auto-published (no approval workflow)
6. ✅ User can delete own review
7. ✅ Admin can delete any review

---

## 🗄️ Database Schema

### Invoices Table
```sql
CREATE TABLE invoices (
    id BIGINT PRIMARY KEY,
    invoice_number VARCHAR(255) UNIQUE,
    order_id BIGINT,
    total DECIMAL(10,2),
    pdf_url VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

### Reviews Table
```sql
CREATE TABLE reviews (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    order_id BIGINT,
    rating TINYINT,
    comment TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (user_id, order_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

---

## 🔐 Required Permissions

### User Permissions (API Guard)
- `view review` - View own reviews
- `create review` - Create reviews for completed orders
- `view order` - Required to access invoice

### Admin Permissions (Web Guard)
- `view all reviews` - View all reviews and statistics
- `delete review` - Delete any review
- `view all orders` - View order invoices
- `change order status` - Trigger invoice generation, regenerate invoices

---

## 📱 Example Usage

### Complete Flow Example

```javascript
// 1. User completes an order (order status changed to completed by admin)

// 2. User receives email with invoice

// 3. User downloads invoice
const response = await fetch('/api/users/orders/1/invoice', {
    headers: {
        'Authorization': 'Bearer ' + userToken,
        'Accept': 'application/json'
    }
});
const invoice = await response.json();
// User can click invoice.download_url to get PDF

// 4. User creates review
const reviewResponse = await fetch('/api/users/reviews/orders/1', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + userToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        rating: 5,
        comment: 'Excellent quality and fast delivery!'
    })
});

// 5. User views their reviews
const myReviews = await fetch('/api/users/reviews', {
    headers: {
        'Authorization': 'Bearer ' + userToken,
        'Accept': 'application/json'
    }
});
```

---

## ⚠️ Error Codes Summary

| Code | Meaning | Common Causes |
|------|---------|---------------|
| 200 | Success | Request completed successfully |
| 400 | Bad Request | Business logic violation (already reviewed, order not completed) |
| 401 | Unauthorized | Missing or invalid token |
| 403 | Forbidden | Permission denied (not your order, insufficient permissions) |
| 404 | Not Found | Resource doesn't exist (invoice not created, review not found) |
| 422 | Validation Error | Invalid input data (rating out of range, comment too long) |
| 500 | Server Error | System error (check logs) |

---

## 🎯 Quick Reference

**Create Review**: `POST /api/users/reviews/orders/{order}`
```json
{"rating": 5, "comment": "Great!"}
```

**Get Invoice**: `GET /api/users/orders/{order}/invoice`

**Admin Stats**: `GET /reviews/stats`

**Admin All Reviews**: `GET /reviews?rating=5&search=excellent`

---

Need help? Check [TESTING_GUIDE.md](TESTING_GUIDE.md) for detailed testing instructions! 🚀
