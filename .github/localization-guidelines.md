# Localization Guidelines - Kandura Platform

## Overview

This document defines the rules and best practices for implementing Arabic/English language switching using `spatie/laravel-translatable` for model data and Laravel's built-in localization for UI strings.

---

## 1. Static UI Text (Fixed Words)

### Rule: Always use `__()` helper or `@lang()` directive

For all static text in Blade templates, use Laravel's translation helpers:

```blade
{{-- ✅ Correct --}}
<h1>{{ __('messages.dashboard') }}</h1>
<label>{{ __('messages.name') }}</label>
<button>{{ __('messages.save') }}</button>

{{-- ❌ Incorrect --}}
<h1>Dashboard</h1>
<label>Name</label>
<button>Save</button>
```

### Translation File Structure

```
lang/
├── en/
│   └── messages.php
└── ar/
    └── messages.php
```

### Adding New Translations

1. Add key to **both** `lang/en/messages.php` and `lang/ar/messages.php`
2. Use snake_case for keys
3. Group related translations with comments

```php
// lang/en/messages.php
return [
    // Navigation
    'dashboard' => 'Dashboard',
    'users' => 'Users',
    'orders' => 'Orders',
    
    // Actions
    'save' => 'Save',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
];

// lang/ar/messages.php
return [
    // Navigation
    'dashboard' => 'لوحة التحكم',
    'users' => 'المستخدمون',
    'orders' => 'الطلبات',
    
    // Actions
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'delete' => 'حذف',
];
```

### Placeholders in Translations

```php
// lang/en/messages.php
'welcome_user' => 'Welcome, :name!',
'items_count' => ':count items found',

// Usage in Blade
{{ __('messages.welcome_user', ['name' => $user->name]) }}
{{ __('messages.items_count', ['count' => $total]) }}
```

---

## 2. Dynamic Model Data (Database Content)

### Rule: Use `getTranslation()` method, NOT array access

Models using `Spatie\Translatable\HasTranslations` trait:

```php
{{-- ✅ Correct --}}
{{ $city->getTranslation('name', 'en') }}
{{ $city->getTranslation('name', 'ar') }}
{{ $city->getTranslation('name', app()->getLocale()) }}

{{-- ❌ Incorrect - causes "Cannot access offset of type string on string" --}}
{{ $city->name['en'] }}
{{ $city->name['ar'] }}
```

### Model Setup

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public $translatable = ['name'];

    // ⚠️ DO NOT add 'name' => 'array' to $casts
    // The HasTranslations trait handles JSON encoding/decoding
}
```

### Display Based on Current Locale

```blade
{{-- Auto-detect current locale --}}
{{ $city->getTranslation('name', app()->getLocale()) }}

{{-- Or use the magic property (returns current locale) --}}
{{ $city->name }}

{{-- Show both languages --}}
{{ $city->getTranslation('name', 'en') }} / {{ $city->getTranslation('name', 'ar') }}
```

### Null-Safe Access

```blade
{{-- Handle potentially null relationships --}}
{{ $address->city?->getTranslation('name', 'en') ?? 'N/A' }}
```

---

## 3. Blade Template Patterns

### Page Titles

```blade
@section('title', __('messages.users'))
```

### Form Labels

```blade
<label for="name">{{ __('messages.name') }}</label>
<input type="text" id="name" name="name" placeholder="{{ __('messages.enter_name') }}">
```

### Table Headers

```blade
<thead>
    <tr>
        <th>{{ __('messages.name') }}</th>
        <th>{{ __('messages.email') }}</th>
        <th>{{ __('messages.status') }}</th>
        <th>{{ __('messages.actions') }}</th>
    </tr>
</thead>
```

### Buttons and Links

```blade
<button type="submit" class="btn btn-primary">
    <i class="fas fa-save"></i> {{ __('messages.save') }}
</button>

<a href="{{ route('users.index') }}" class="btn btn-secondary">
    {{ __('messages.cancel') }}
</a>
```

### Status Badges

```blade
@if($order->status === 'pending')
    <span class="badge badge-warning">{{ __('messages.pending') }}</span>
@elseif($order->status === 'completed')
    <span class="badge badge-success">{{ __('messages.completed') }}</span>
@endif
```

### Confirmation Messages

```blade
<form onsubmit="return confirm('{{ __('messages.delete_confirm') }}');">
```

### Empty States

```blade
@forelse($users as $user)
    {{-- User row --}}
@empty
    <p>{{ __('messages.no_data') }}</p>
@endforelse
```

---

## 4. JavaScript Translations

### Pass translations to JS via data attributes

```blade
<button 
    data-confirm="{{ __('messages.delete_confirm') }}"
    onclick="confirmDelete(this)">
    {{ __('messages.delete') }}
</button>

<script>
function confirmDelete(btn) {
    return confirm(btn.dataset.confirm);
}
</script>
```

### Or use a global translations object

```blade
<script>
    window.translations = {
        delete_confirm: "{{ __('messages.delete_confirm') }}",
        loading: "{{ __('messages.loading') }}",
        success: "{{ __('messages.success') }}",
        error: "{{ __('messages.error') }}"
    };
</script>
```

---

## 5. RTL Support

### Layout Detection

```blade
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
```

### Conditional CSS

```blade
@if(app()->getLocale() === 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
@else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endif
```

### CSS RTL Selectors

```css
/* LTR (English) */
.sidebar {
    left: 0;
    border-right: 1px solid #e2e8f0;
}

/* RTL (Arabic) */
[dir="rtl"] .sidebar {
    right: 0;
    left: auto;
    border-right: none;
    border-left: 1px solid #e2e8f0;
}
```

---

## 6. API Responses

### For API Resources, include locale-aware translations

```php
// app/Http/Resources/CityResource.php
public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->getTranslation('name', app()->getLocale()),
        'name_en' => $this->getTranslation('name', 'en'),
        'name_ar' => $this->getTranslation('name', 'ar'),
    ];
}
```

---

## 7. Validation Messages

### Use Laravel's built-in validation translation

Create `lang/en/validation.php` and `lang/ar/validation.php` for custom validation messages.

```php
// lang/ar/validation.php
return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => 'يجب أن يكون :attribute بريدًا إلكترونيًا صالحًا.',
    'attributes' => [
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'name' => 'الاسم',
    ],
];
```

---

## 8. Common Mistakes to Avoid

| ❌ Don't | ✅ Do |
|----------|-------|
| `$model->name['en']` | `$model->getTranslation('name', 'en')` |
| `{{ 'Dashboard' }}` | `{{ __('messages.dashboard') }}` |
| `$casts = ['name' => 'array']` with HasTranslations | Remove the cast, trait handles it |
| Hardcode text in JS | Pass via data attributes or global object |
| Forget null checks | Use `?->` operator: `$address->city?->getTranslation(...)` |

---

## 9. Quick Reference

```blade
{{-- Static text --}}
{{ __('messages.key') }}

{{-- With placeholder --}}
{{ __('messages.key', ['name' => $value]) }}

{{-- Model translation (specific locale) --}}
{{ $model->getTranslation('field', 'en') }}

{{-- Model translation (current locale) --}}
{{ $model->getTranslation('field', app()->getLocale()) }}

{{-- Current locale --}}
{{ app()->getLocale() }}

{{-- Check locale --}}
@if(app()->getLocale() === 'ar')

{{-- Language switch URL --}}
{{ route('language.switch', 'ar') }}
```

---

## 10. Checklist for New Pages

- [ ] Page title uses `__('messages.xxx')`
- [ ] All labels use `__('messages.xxx')`
- [ ] All buttons use `__('messages.xxx')`
- [ ] Table headers use `__('messages.xxx')`
- [ ] Status badges use `__('messages.xxx')`
- [ ] Empty states use `__('messages.xxx')`
- [ ] Confirmation dialogs use `__('messages.xxx')`
- [ ] Model data uses `getTranslation()` method
- [ ] New translation keys added to both `en` and `ar` files
