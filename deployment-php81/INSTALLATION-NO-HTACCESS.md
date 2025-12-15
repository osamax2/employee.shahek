# 🚀 Installation OHNE Root .htaccess

## Problem: Mit .htaccess im Root funktioniert nichts

**Lösung:** Nutze URLs mit `/public/` prefix

---

## ✅ Installation (Vereinfacht)

### 1. Lade hoch und extrahiere:
- `employee-tracking-PHP81-FIXED_*.zip` → `/home/shahek/employee.shahek.org/`
- `vendor-php81-fixed.zip` → extrahiere in `/home/shahek/employee.shahek.org/vendor/`

### 2. Setze Permissions:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. LÖSCHE die Root .htaccess:
```bash
rm /home/shahek/employee.shahek.org/.htaccess
```

---

## 🌐 Zugriff auf die App:

### Dashboard:
```
https://employee.shahek.org/public/admin/dashboard
```

### API:
```
https://employee.shahek.org/public/api/auth/login
```

### Debug:
```
https://employee.shahek.org/public/check.php
```

---

## 🎯 Warum funktioniert das?

Laravel's `public/.htaccess` übernimmt das Routing:
- `/public/admin/dashboard` → Laravel Route
- `/public/api/*` → API Routes
- `/public/` → Enthält `index.php` (Laravel Entry Point)

**Die Root .htaccess ist NICHT notwendig!**

---

## 🔧 Alternative: cPanel Domain-Einstellung ändern

**Beste Lösung:**
1. **cPanel → Domains**
2. **Wähle:** employee.shahek.org
3. **Document Root ändern zu:** `/home/shahek/employee.shahek.org/public`
4. **Speichern**

Danach funktionieren die URLs OHNE `/public/` prefix:
```
https://employee.shahek.org/admin/dashboard
https://employee.shahek.org/api/auth/login
```

---

## ✅ URLs (mit /public/ prefix):

| Funktion | URL |
|----------|-----|
| Dashboard | https://employee.shahek.org/public/admin/dashboard |
| API Login | https://employee.shahek.org/public/api/auth/login |
| System Check | https://employee.shahek.org/public/check.php |
| Test View | https://employee.shahek.org/public/test-view.php |

---

## 🎉 Fertig!

**Login:**
- Email: `admin@company.com`
- Password: `admin123`
