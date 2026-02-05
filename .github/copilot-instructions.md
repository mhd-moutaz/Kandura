# Kandura - Custom Clothing E-Commerce Platform

## Architecture Overview

Laravel 12 API-first e-commerce platform for custom Kandura (traditional clothing) with multi-role access (User/Admin/SuperAdmin), dual authentication guards (API + Web), and Stripe payment integration.

### Core Data Flow
1. Users create **Designs** (custom clothing specifications with measurements, options, images)
2. Designs are added to **OrderItems** → aggregated into **Orders** (cart-like pending state)
3. Orders confirmed via payment (**wallet**, **card/Stripe**, or **cash**)
4. **Invoices** auto-generated post-payment, **Reviews** enabled after completion

### Key Directory Structure
```
app/Http/Controllers/{Users,Admins,SuperAdmin}/  # Role-segregated controllers
app/Http/Services/{Users,Admins,Global}/         # Business logic layer
app/Http/Requests/{Users,Admins,Global}/         # Form request validation
app/Http/Resources/                              # API response transformations
app/Policies/                                    # Authorization (owner-based)
app/Observers/                                   # Model event hooks (notifications)
```

## Development Commands

```bash
composer dev           # Starts server + queue worker + Vite concurrently
composer test          # Runs Pest tests (clears config first)
composer setup         # Full project setup (install, migrate, build)
php artisan route:list --path=users  # List user API routes
```

## Code Patterns

### Controller Pattern - Thin Controllers, Fat Services
Controllers delegate to Services and return standardized responses via `$this->success()`:
```php
// app/Http/Controllers/Users/OrderController.php
public function index() {
    $orders = $this->orderService->getUserOrders();
    return $this->success(OrderResource::collection($orders), 'Orders retrieved');
}
```

### Service Layer Pattern
All business logic in `app/Http/Services/`. Services handle DB transactions, validation, and cross-service coordination:
```php
// Services inject other services for complex operations
public function __construct(WalletService $walletService, CouponService $couponService, StripeService $stripeService)
```

### Exception Handling
Use `GeneralException` for API errors with HTTP codes:
```php
throw new GeneralException('Order is already confirmed', 400);
```

### Permission System (Spatie)
- **API guard**: User permissions (`view profile`, `create order`, etc.)
- **Web guard**: Admin/SuperAdmin permissions (`view all order`, `update status order`)
- Middleware: `->middleware('permission:view order')`
- Constants defined in `app/Constants/PermissionConstants.php`
- Full configuration in `config/role_permissions.php`

### Policy Authorization
Policies enforce owner-based access. Check with `Gate::authorize()`:
```php
Gate::authorize('update', $order);  // Uses OrderPolicy
```

### Translatable Fields
Designs support Arabic/English via array casting:
```php
'name' => ['ar' => 'قندورة', 'en' => 'Kandura']
// Validated as: 'name.ar' => 'required|string', 'name.en' => 'required|string'
```

### Model Scopes for Filtering
Models implement `scopeFilter()` for consistent query building:
```php
Design::filter($request->all())->paginate(15);
// Handles: search, state, price range, size, sorting
```

### Observers for Side Effects
`app/Observers/` handle notifications on model events:
- `OrderObserver`: Notifies admins + design creators on order creation/status change
- `DesignObserver`: Notifies admins on new designs, removes from carts when deactivated

## Enums & Constants

| Location | Purpose |
|----------|---------|
| `app/Enum/StatusOrderEnum.php` | Order states: `pending`, `confirmed`, `processing`, `completed`, `cancelled` |
| `app/Enum/UserRoleEnum.php` | Roles: `user`, `admin`, `super_admin` |
| `app/Enum/DesignOptionsTypeEnum.php` | Design customizations: `color`, `dome_type`, `sleeve_type`, `fabric_type` |

## Testing
- Framework: **Pest** with Laravel plugin
- DB: In-memory SQLite for tests
- Run: `composer test` or `php artisan test`

## External Integrations

| Service | Purpose | Config |
|---------|---------|--------|
| Stripe | Card payments + webhooks | `config/stripe.php`, webhook at `/api/stripe/webhook` |
| Firebase | FCM push notifications | `config/firebase.php`, `kreait/laravel-firebase` |
| Laravel Passport | API OAuth2 auth | API guard uses `auth:api` middleware |
