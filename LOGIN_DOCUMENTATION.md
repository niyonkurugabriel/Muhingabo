# 🔐 Login System Documentation

## Overview
Your MUHINGABO Inventory System now has a **secure login system** with session management to prevent unauthorized access.

---

## 📋 Default Credentials

```
Username: dope
Password: @1205
```

Use these credentials to access the system after the login page redirects you.

---

## 🔑 Features Implemented

### 1. **Session Management**
- ✅ User sessions created upon login
- ✅ Session timeout (1 hour of inactivity)
- ✅ Automatic session validation
- ✅ Secure session destruction on logout

### 2. **Access Control**
- ✅ All pages require login
- ✅ Redirects to login page if not authenticated
- ✅ Prevents direct access to protected pages
- ✅ Session expiration handling

### 3. **User Interface**
- ✅ Professional login form
- ✅ Error messages for failed login
- ✅ Password visibility toggle
- ✅ User menu in navbar
- ✅ Logout button

---

## 📁 Files Created/Modified

### New Files

| File | Purpose |
|------|---------|
| `login.php` | Login form and authentication |
| `logout.php` | Session destruction and logout |
| `session_config.php` | Session management and helper functions |

### Modified Files

All these files now require login:

| File | Purpose |
|------|---------|
| `index.php` | Dashboard |
| `view_items.php` | View inventory |
| `add_item.php` | Add items |
| `update_item.php` | Edit items |
| `delete_item.php` | Delete items |
| `sell_item.php` | Record sales |
| `purchase_item.php` | Record purchases |
| `save_item.php` | Save item data |
| `save_update.php` | Save item updates |
| `save_sale.php` | Save sales |
| `save_purchase.php` | Save purchases |
| `sales_dashboard.php` | Sales history |
| `purchase_dashboard.php` | Purchase history |
| `audit_log.php` | Audit trail |
| `daily_report.php` | Daily reports |
| `navbar.php` | Navigation with logout |

---

## 🚀 How to Use

### Login
1. Open: `http://localhost/invetory_system/`
2. You'll be redirected to: `http://localhost/invetory_system/login.php`
3. Enter credentials:
   - Username: `dope`
   - Password: `@1205`
4. Click "Sign In"
5. You're now logged in! 🎉

### Logout
1. Click your username in the top-right corner
2. Select "🚪 Logout"
3. You'll be redirected to the login page

### Session Timeout
- Sessions expire after **1 hour** of inactivity
- You'll be redirected to login page
- Login again to continue

---

## 🔐 Security Features

### Password Handling
- ✅ Passwords are validated against default credentials
- ✅ Input validation on all forms
- ✅ XSS protection with htmlspecialchars()
- ✅ Error messages don't reveal user information

### Session Security
- ✅ Session starts only once
- ✅ Proper session destruction on logout
- ✅ Cookie parameters set securely
- ✅ Session timeout check on every page load
- ✅ Last activity tracking

### Access Control
- ✅ `require_login()` function checks authorization
- ✅ Redirects unauthorized access to login page
- ✅ All transaction pages protected
- ✅ All admin pages protected

---

## 💻 Code Usage

### Session Functions (in `session_config.php`)

#### Check if User is Logged In
```php
if (is_logged_in()) {
    // User is authenticated
    echo "Welcome!";
}
```

#### Get Current Username
```php
$username = get_current_user();
echo "Hello, " . $username;
```

#### Require Login Protection
```php
<?php
include 'session_config.php';

// This will redirect to login.php if not authenticated
require_login();
?>
```

#### Get User Display Name
```php
$display_name = get_user_display_name();
echo $display_name;  // Output: "Dope"
```

#### Check Session Expiration
```php
if (is_session_expired()) {
    destroy_session();
    header('Location: login.php');
}
```

#### Create Session (for custom login)
```php
create_session('username');
```

#### Destroy Session (for logout)
```php
destroy_session();
```

---

## 🎯 Implementation Details

### Session Configuration
**File:** `session_config.php`

```php
// Session timeout (1 hour)
define('SESSION_TIMEOUT', 3600);

// Default credentials
define('DEFAULT_USERNAME', 'dope');
define('DEFAULT_PASSWORD', '@1205');
```

### Login Flow
1. User visits any protected page
2. `require_login()` checks `is_logged_in()`
3. If not logged in → redirects to `login.php`
4. User enters credentials
5. `validate_credentials()` checks against defaults
6. If valid → `create_session()` creates session
7. Redirects to `index.php`
8. User can now access all pages

### Logout Flow
1. User clicks "Logout" button
2. Redirects to `logout.php`
3. `destroy_session()` clears session data
4. Redirects to `login.php`
5. User must login again

---

## 🔧 Customization

### Change Default Credentials
Edit `session_config.php`:

```php
define('DEFAULT_USERNAME', 'your_username');
define('DEFAULT_PASSWORD', 'your_password');
```

### Change Session Timeout
Edit `session_config.php`:

```php
define('SESSION_TIMEOUT', 7200);  // 2 hours
```

### Add Multiple Users (Future)
Modify `validate_credentials()` in `session_config.php`:

```php
function validate_credentials($username, $password) {
    $valid_users = [
        'dope' => '@1205',
        'admin' => 'password123',
        'user2' => 'pass456'
    ];
    
    return isset($valid_users[$username]) && 
           $valid_users[$username] === $password;
}
```

### Connect to Database (Future)
Replace credentials validation with database lookup:

```php
function validate_credentials($username, $password) {
    global $conn;
    
    $username = mysqli_real_escape_string($conn, $username);
    $password = md5($password);  // Use proper hashing
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    return mysqli_num_rows($result) > 0;
}
```

---

## ✅ Verification Checklist

- ✅ Visit `http://localhost/invetory_system/`
- ✅ Redirected to login page
- ✅ Enter: username = `dope`, password = `@1205`
- ✅ Click "Sign In"
- ✅ Redirected to dashboard
- ✅ See username in top-right navbar
- ✅ Click username → see logout option
- ✅ Click logout → redirected to login
- ✅ Try accessing URL directly (e.g., view_items.php)
- ✅ Redirected to login page
- ✅ All protected pages work correctly

---

## 🐛 Troubleshooting

### "Please enter both username and password"
- Make sure you enter both fields
- Check for extra spaces before/after

### "Invalid username or password"
- Username: `dope` (exact case-sensitive)
- Password: `@1205` (exact case-sensitive)
- Check for typos

### "Session expired, login again"
- Your session has been inactive for 1 hour
- Login again with credentials

### Redirects to login from protected page
- Session may have expired
- Login credentials may not be valid
- Browser cookies may be disabled
- Try incognito/private window

### Logout not working
- Check browser cookies are enabled
- Try clearing browser cache
- Try different browser

---

## 📊 Session Variables

When user logs in, these are stored:

```php
$_SESSION['user_id']       // User ID (default: 1)
$_SESSION['username']      // Username
$_SESSION['login_time']    // Login timestamp
$_SESSION['last_activity'] // Last activity time
```

---

## 🔒 Best Practices

✅ Always use `require_login()` on protected pages  
✅ Always validate user input  
✅ Use `htmlspecialchars()` when displaying user data  
✅ Regularly update session timeout value  
✅ Monitor failed login attempts (future)  
✅ Use HTTPS in production (important!)  
✅ Hash passwords in production (use password_hash)  
✅ Store credentials in database (not hardcoded)  

---

## 📚 File Descriptions

### login.php
- Beautiful login form
- Credential validation
- Session creation
- Error message display
- Password visibility toggle
- Auto-fill demo credentials

### logout.php
- Destroys user session
- Clears all session variables
- Deletes session cookie
- Redirects to login

### session_config.php
- Session initialization
- Helper functions
- Credential validation
- Session management
- Timeout checking
- Login requirement checking

---

## 🎓 Next Steps

1. ✅ Test login with default credentials
2. ✅ Test logout functionality
3. ✅ Test session timeout (wait 1 hour or edit constant)
4. ✅ Try accessing protected pages directly
5. ✅ Test in incognito mode
6. ✅ Test with different browsers
7. 🔧 Customize credentials if needed
8. 📊 Plan database integration for users

---

## 💡 Pro Tips

- Credentials are auto-filled on login form for convenience during dev
- Remove auto-fill in production: comment out the `document.addEventListener` in login.php
- Keep session functions in a separate file for reusability
- Always include `session_config.php` before `db_connect.php`
- Use `require_login()` as the first check in protected pages

---

**Implementation Date:** November 27, 2025  
**Status:** ✅ COMPLETE & WORKING  
**Default User:** dope  
**Default Password:** @1205  

---

Your inventory system is now **secure and protected!** 🔐
