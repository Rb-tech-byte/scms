# 🚀 Quick Setup - Branch Access Control

## ✅ **Step 1: Register Middleware**

**File:** `app/Http/Kernel.php`

Find the `$routeMiddleware` array and add:

```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    // ... other middleware ...
    
    // ADD THIS LINE:
    'branch.access' => \App\Http\Middleware\CheckBranchAccess::class,
];
```

---

## ✅ **Step 2: Update Routes**

**File:** `routes/web.php`

Find your authenticated routes and wrap them with the middleware:

**BEFORE:**
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ... other routes
});
```

**AFTER:**
```php
Route::middleware(['auth', 'branch.access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    // ... other routes
});
```

---

## ✅ **Step 3: Add Trait to Models**

Add `BranchScoped` trait to models with `branch_id`:

### Applicant Model (Already Done ✅)
```php
use App\Traits\BranchScoped;

class Applicant extends Model
{
    use BranchScoped;
}
```

### Venue Model
**File:** `app/Models/Venue.php`

```php
use App\Traits\BranchScoped;

class Venue extends Model
{
    use BranchScoped;
    // ... rest of model
}
```

### Tutor Model
**File:** `app/Models/Tutor.php`

```php
use App\Traits\BranchScoped;

class Tutor extends Model
{
    use BranchScoped;
    // ... rest of model
}
```

---

## ✅ **Step 4: Update User Model**

**File:** `app/Models/User.php`

Add method to get user's branch:

```php
/**
 * Get user's assigned branch
 */
public function branch()
{
    // Check if user has tutor record
    if ($this->tutor) {
        return $this->tutor->branch;
    }

    // Check if user has applicant record
    $applicant = $this->applicants()->first();
    if ($applicant) {
        return $applicant->branch;
    }

    return null;
}

/**
 * Get user's branch ID
 */
public function getBranchIdAttribute()
{
    $branch = $this->branch();
    return $branch ? $branch->id : null;
}
```

---

## ✅ **Step 5: Update Controllers (Example)**

**File:** `app/Http/Controllers/ApplicantController.php`

Add at the top:
```php
use App\Services\PermissionService;
```

Update methods:

```php
public function index()
{
    // Check permission
    if (!PermissionService::can('list-applicants')) {
        abort(403, 'Unauthorized');
    }

    // Data automatically filtered by branch
    $applicants = Applicant::with(['branch', 'user', 'country'])
        ->paginate(20);

    return view('applicants.index', compact('applicants'));
}

public function create()
{
    if (!PermissionService::canCreate('register-applicants')) {
        abort(403, 'Unauthorized');
    }

    // Get accessible branches
    if (PermissionService::isSystemAdmin()) {
        $branches = Branch::all();
    } else {
        $branchId = session('user_branch_id');
        $branches = Branch::where('id', $branchId)->get();
    }

    $countries = Country::all();

    return view('applicants.create', compact('branches', 'countries'));
}

public function store(Request $request)
{
    if (!PermissionService::canCreate('register-applicants')) {
        abort(403, 'Unauthorized');
    }

    // For branch admin, force their branch
    if (PermissionService::isBranchAdmin()) {
        $request->merge(['branch_id' => session('user_branch_id')]);
    }

    // Validate branch access
    if (!PermissionService::canAccessBranch($request->branch_id)) {
        return back()->with('error', 'Cannot create applicant in this branch');
    }

    $applicant = Applicant::create($request->all());

    return redirect()->route('applicants.index')
        ->with('success', 'Applicant created successfully');
}

public function edit(Applicant $applicant)
{
    if (!PermissionService::canUpdate('update-applicants', $applicant)) {
        abort(403, 'Unauthorized');
    }

    // Get accessible branches
    if (PermissionService::isSystemAdmin()) {
        $branches = Branch::all();
    } else {
        $branches = Branch::where('id', $applicant->branch_id)->get();
    }

    $countries = Country::all();

    return view('applicants.edit', compact('applicant', 'branches', 'countries'));
}

public function update(Request $request, Applicant $applicant)
{
    if (!PermissionService::canUpdate('update-applicants', $applicant)) {
        abort(403, 'Unauthorized');
    }

    // Branch admin cannot change branch
    if (PermissionService::isBranchAdmin()) {
        $request->merge(['branch_id' => $applicant->branch_id]);
    }

    $applicant->update($request->all());

    return redirect()->route('applicants.index')
        ->with('success', 'Applicant updated successfully');
}

public function destroy(Applicant $applicant)
{
    if (!PermissionService::canDelete('delete-applicants', $applicant)) {
        abort(403, 'Unauthorized');
    }

    $applicant->delete();

    return redirect()->route('applicants.index')
        ->with('success', 'Applicant deleted successfully');
}
```

---

## ✅ **Step 6: Update Views (Example)**

**File:** `resources/views/applicants/index.blade.php`

```blade
{{-- Show create button only if user has permission --}}
@if(App\Services\PermissionService::canCreate('register-applicants'))
    <a href="{{ route('applicants.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Applicant
    </a>
@endif

{{-- In table, show edit/delete buttons based on permissions --}}
@foreach($applicants as $applicant)
    <tr>
        <td>{{ $applicant->full_name }}</td>
        <td>{{ $applicant->branch->name }}</td>
        <td>
            @if(App\Services\PermissionService::canUpdate('update-applicants', $applicant))
                <a href="{{ route('applicants.edit', $applicant) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-edit"></i>
                </a>
            @endif

            @if(App\Services\PermissionService::canDelete('delete-applicants', $applicant))
                <form action="{{ route('applicants.destroy', $applicant) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </td>
    </tr>
@endforeach
```

**File:** `resources/views/applicants/create.blade.php` or `edit.blade.php`

```blade
{{-- Branch selection --}}
<div class="form-group">
    <label>Branch <span class="text-danger">*</span></label>
    <select name="branch_id" class="form-control" required 
            {{ App\Services\PermissionService::isBranchAdmin() ? 'readonly' : '' }}>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" 
                    {{ old('branch_id', $applicant->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
</div>
```

---

## ✅ **Step 7: Test the System**

### Test as System Admin:
1. Login as system admin
2. Should see all branches in dropdowns
3. Can create/edit/delete in any branch
4. Can view all applicants from all branches

### Test as Branch Admin:
1. Login as branch admin
2. Should see only their branch in dropdowns
3. Can create/edit/delete only in their branch
4. Can view only applicants from their branch
5. Cannot access other branches' data

### Test as Tutor:
1. Login as tutor
2. Should see only their branch data
3. Cannot create/edit/delete (read-only)
4. Can view applicants from their branch

### Test as Student:
1. Login as student
2. Should see only their own data
3. Cannot see other students' data

---

## 🔧 **Quick Commands**

```bash
# Clear cache after changes
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart server
php artisan serve
```

---

## 📝 **Checklist**

- [ ] Middleware registered in `Kernel.php`
- [ ] Middleware applied to routes in `web.php`
- [ ] `BranchScoped` trait added to `Applicant` model
- [ ] `BranchScoped` trait added to `Venue` model
- [ ] `BranchScoped` trait added to `Tutor` model
- [ ] `branch()` method added to `User` model
- [ ] Controllers updated with `PermissionService` checks
- [ ] Views updated with permission-based buttons
- [ ] Tested with all user roles
- [ ] Cache cleared

---

## ⚡ **Common Issues & Solutions**

### Issue: "Class 'App\Traits\BranchScoped' not found"
**Solution:** Make sure the trait file exists at `app/Traits/BranchScoped.php`

### Issue: Branch admin sees all data
**Solution:** Check if middleware is applied to routes and trait is added to models

### Issue: "Trying to get property of non-object"
**Solution:** Make sure user has a branch assigned (tutor or applicant record)

### Issue: Permission denied for valid user
**Solution:** Check permissions in `davinci_user_permissions` table

---

## 🎯 **Files Created**

1. ✅ `app/Http/Middleware/CheckBranchAccess.php`
2. ✅ `app/Traits/BranchScoped.php`
3. ✅ `app/Services/PermissionService.php`
4. ✅ `BRANCH_ACCESS_CONTROL_GUIDE.md`
5. ✅ `QUICK_SETUP.md` (this file)

---

**You're ready to implement branch-based access control!** 🚀

Follow the steps above in order, test thoroughly, and your system will be secured with proper branch isolation.
