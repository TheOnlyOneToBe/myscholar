# UserPolicy - Authorization Rules

## Overview

The `UserPolicy` class defines authorization rules for user-related actions in the MyScholar system. It implements Laravel's authorization gate/policy pattern to ensure granular control over what actions each user can perform.

**Location:** `modules/Auth/Policies/UserPolicy.php`  
**Registered in:** `modules/Auth/Providers/AuthServiceProvider.php`

---

## Authorization Rules

### 1. View Users

#### `viewAny(User $user): bool`
Determine if user can view any users list.

**Rule:**
- ✅ User has `auth.manage_users` permission

**Usage:**
```php
$this->authorize('viewAny', User::class);
```

**Examples:**
- ✅ Admin (has permission)
- ✅ Proviseur (if assigned permission)
- ❌ Teacher, Parent, Student

---

### 2. View Specific User

#### `view(User $user, User $model): bool`
Determine if user can view a specific user's profile.

**Rules:**
1. ✅ User viewing their own profile
2. ✅ Admin can view any user
3. ✅ Proviseur (director) can view any school user
4. ❌ Default deny

**Usage:**
```php
$this->authorize('view', $targetUser);
```

**Examples:**
```
Auth User: Teacher (ID: 5)
Target User: Student (ID: 10)
Result: ❌ DENIED

Auth User: Admin (ID: 1)
Target User: Teacher (ID: 5)
Result: ✅ ALLOWED

Auth User: Proviseur (ID: 2)
Target User: Teacher (ID: 5)
Result: ✅ ALLOWED

Auth User: Teacher (ID: 5)
Target User: Teacher (ID: 5)
Result: ✅ ALLOWED (self)
```

---

### 3. Create User

#### `create(User $user): bool`
Determine if user can create new users.

**Rule:**
- ✅ User has `auth.manage_users` permission

**Usage:**
```php
$this->authorize('create', User::class);
```

---

### 4. Update User

#### `update(User $user, User $model): bool`
Determine if user can update a user's profile.

**Rules:**
1. ✅ User can update their own profile
2. ✅ Admin can update any user
3. ✅ Proviseur can update school staff (except other Proviseur/Admin)
4. ❌ Default deny

**Usage:**
```php
$this->authorize('update', $targetUser);
```

**Examples:**
```
Auth User: Teacher (ID: 5)
Target User: Student (ID: 10)
Result: ❌ DENIED

Auth User: Proviseur (ID: 2)
Target User: Admin (ID: 1)
Result: ❌ DENIED (cannot update higher authority)

Auth User: Proviseur (ID: 2)
Target User: Teacher (ID: 5)
Result: ✅ ALLOWED

Auth User: Teacher (ID: 5)
Target User: Teacher (ID: 5)
Result: ✅ ALLOWED (self)
```

---

### 5. Delete User

#### `delete(User $user, User $model): bool`
Determine if user can delete a user.

**Rules:**
1. ❌ Cannot delete yourself
2. ✅ Only Admin can delete
3. ❌ Default deny

**Usage:**
```php
$this->authorize('delete', $targetUser);
```

**Notes:**
- This is a destructive action reserved for admins only
- No hierarchical deletion allowed

---

### 6. Restore User

#### `restore(User $user, User $model): bool`
Determine if user can restore a soft-deleted user.

**Rule:**
- ✅ Only Admin can restore

---

### 7. Force Delete User

#### `forceDelete(User $user, User $model): bool`
Determine if user can permanently delete a user.

**Rule:**
- ✅ Only Admin can force delete

---

### 8. Activate User

#### `activate(User $user, User $model): bool`
Determine if user can activate a deactivated user.

**Rules:**
1. ❌ Cannot activate yourself
2. ✅ Admin can activate any user
3. ✅ Proviseur can activate school staff (except other Proviseur/Admin)
4. ❌ Default deny

**Usage:**
```php
$this->authorize('activate', $targetUser);
```

---

### 9. Deactivate User

#### `deactivate(User $user, User $model): bool`
Determine if user can deactivate a user.

**Rules:**
1. ❌ Cannot deactivate yourself
2. ✅ Admin can deactivate any user
3. ✅ Proviseur can deactivate school staff (except other Proviseur/Admin)
4. ❌ Default deny

**Usage:**
```php
$this->authorize('deactivate', $targetUser);
```

---

### 10. Assign Role

#### `assignRole(User $user, User $model): bool`
Determine if user can assign roles to a user.

**Rules:**
1. ❌ Cannot assign roles to yourself
2. ✅ Admin can assign roles to any user
3. ✅ Proviseur can assign roles to school staff (cannot assign admin/proviseur roles)
4. ❌ Others cannot assign roles

**Usage:**
```php
$this->authorize('assignRole', $targetUser);
```

**Examples:**
```
Auth User: Proviseur (ID: 2)
Target User: Teacher (ID: 5)
Role: censeur
Result: ✅ ALLOWED

Auth User: Proviseur (ID: 2)
Target User: Teacher (ID: 5)
Role: proviseur
Result: ✅ ALLOWED (can assign, but cannot assign if target already has admin/proviseur)

Auth User: Proviseur (ID: 2)
Target User: Admin (ID: 1)
Role: teacher
Result: ❌ DENIED (cannot assign roles to admins)
```

---

### 11. Remove Role

#### `removeRole(User $user, User $model): bool`
Determine if user can remove roles from a user.

**Rules:**
1. ❌ Cannot remove roles from yourself
2. ✅ Admin can remove roles from any user
3. ✅ Proviseur can remove roles from school staff (cannot remove from admin/proviseur)
4. ❌ Others cannot remove roles

**Usage:**
```php
$this->authorize('removeRole', $targetUser);
```

---

### 12. Change Password

#### `changePassword(User $user, User $model): bool`
Determine if user can change a user's password.

**Rules:**
1. ✅ Users can change their own password
2. ✅ Admin can change any user's password
3. ❌ Default deny

**Usage:**
```php
$this->authorize('changePassword', $targetUser);
```

**Notes:**
- "Change" = user knows current password
- This is more secure than "reset"

---

### 13. Reset Password

#### `resetPassword(User $user, User $model): bool`
Determine if user can reset a user's password.

**Rules:**
1. ❌ Cannot reset your own password (use changePassword instead)
2. ✅ Only Admin can reset other users' passwords
3. ❌ Default deny

**Usage:**
```php
$this->authorize('resetPassword', $targetUser);
```

**Notes:**
- "Reset" = admin sets new password without knowing current
- More restrictive than "change"

---

### 14. Lock Account

#### `lockAccount(User $user, User $model): bool`
Determine if user can lock a user's account (disable login).

**Rules:**
1. ❌ Cannot lock yourself
2. ✅ Admin can lock any user
3. ✅ Proviseur can lock school staff (except other Proviseur/Admin)
4. ❌ Default deny

**Usage:**
```php
$this->authorize('lockAccount', $targetUser);
```

---

### 15. Unlock Account

#### `unlockAccount(User $user, User $model): bool`
Determine if user can unlock a locked account.

**Rules:**
1. ❌ Cannot unlock yourself
2. ✅ Admin can unlock any user
3. ✅ Proviseur can unlock school staff (except other Proviseur/Admin)
4. ❌ Default deny

**Usage:**
```php
$this->authorize('unlockAccount', $targetUser);
```

---

## Integration with Controllers

All user management actions in `UserController` use the policy:

```php
public function update(User $user, Request $request): JsonResponse
{
    $authUser = auth('sanctum')->user();
    $this->authorize('update', $user);  // ← Policy check
    
    // ... rest of method
}
```

---

## Role Hierarchy

The policy respects the role hierarchy:

| Role | Level | Can Manage | Can Assign Roles To |
|------|-------|-----------|-------------------|
| **admin** | 0 | Everyone | Everyone |
| **proviseur** | 1 | School staff | School staff (except admin/proviseur) |
| **censeur** | 2 | Limited | None |
| **prof_principal** | 3 | Limited | None |
| **chef_classe** | 3 | None | None |
| **enseignant** | 4 | None | None |
| **surveillant** | 5 | None | None |
| **parent** | 99 | None | None |
| **student** | 100 | None | None |

---

## Error Handling

When authorization fails, Laravel throws `AuthorizationException`:

```php
// In controllers using authorize()
try {
    $this->authorize('update', $user);
} catch (AuthorizationException $e) {
    // Returns 403 Forbidden response
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

The middleware in `modules/Auth/Routes/api.php` automatically catches these and returns proper HTTP 403 responses.

---

## Testing Authorization

Test that policies work correctly:

```php
public function test_teacher_cannot_update_student()
{
    $teacher = User::factory()->create();
    $teacher->giveRole('enseignant');
    
    $student = User::factory()->create();
    $student->giveRole('student');
    
    $this->actingAs($teacher)
        ->putJson("/api/auth/users/{$student->id}", ['email' => 'new@email.com'])
        ->assertForbidden();
}

public function test_proviseur_can_update_teacher()
{
    $proviseur = User::factory()->create();
    $proviseur->giveRole('proviseur');
    
    $teacher = User::factory()->create();
    $teacher->giveRole('enseignant');
    
    $this->actingAs($proviseur)
        ->putJson("/api/auth/users/{$teacher->id}", ['email' => 'new@email.com'])
        ->assertOk();
}
```

---

## Best Practices

1. **Always authorize before action:** Check authorization before modifying data
2. **Use policy over permissions:** Policies provide more granular control than simple permissions
3. **Prevent self-modification:** Block users from modifying their own critical attributes
4. **Respect hierarchy:** Higher roles cannot be modified by lower roles
5. **Log authorization failures:** Track who attempted unauthorized actions

---

## Related Files

- `modules/Auth/Policies/UserPolicy.php` - Policy definition
- `modules/Auth/Providers/AuthServiceProvider.php` - Policy registration
- `modules/Auth/Controllers/UserController.php` - Usage in controllers
- `modules/Auth/Routes/api.php` - Protected API routes

---

**Last Updated:** 2026-06-27  
**Version:** 1.0.0
