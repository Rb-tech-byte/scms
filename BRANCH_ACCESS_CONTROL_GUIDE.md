# 🔐 Branch-Based Access Control System - Implementation Guide

## 📋 Overview

This system implements **role-based and branch-based access control** where:

- **System Admin** → Full access to all branches and all operations
- **Branch Admin** → Access only to their assigned branch data (full CRUD)
- **Tutor** → Read-only access to their assigned branch data
- **Student** → Access only to their own data

---

## 🏗️ Architecture Components

### 1. **Middleware** (`CheckBranchAccess`)
- Automatically detects user's role
- Sets user's branch in session
- Blocks unauthorized access

### 2. **Trait** (`BranchScoped`)
- Automatically filters database queries by branch
- Applied to models that have `branch_id`
- Respects user roles

### 3. **Service** (`PermissionService`)
- Centralized permission checking
- Branch access validation
- CRUD operation authorization

---

## 🚀 Implementation Steps

### Step 1: Register Middleware

**File:** `app/Http/Kernel.php`

Add to `$routeMiddleware` array:

```php
protected $routeMiddleware = [
    // ... existing middleware
    'branch.access' => \App\Http\Middleware\CheckBranchAccess::class,
];
```

---

### Step 2: Apply Middleware to Routes

**File:** `routes/web.php`

Wrap your protected routes:

```php
Route::middleware(['auth', 'branch.access'])->group(function () {
    // All your existing routes here
    Route::resource('applicants', ApplicantController::class);
    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('invoices', InvoiceController::class);
    // ... etc
});
```

---

### Step 3: Add Trait to Models

Add `BranchScoped` trait to models that have `branch_id`:

**Models to update:**
- `Applicant`
- `Venue`
- `Tutor`
- Any other model with `branch_id`

**Example:**

```php
use App\Traits\BranchScoped;

class Applicant extends Model
{
    use BranchScoped;
    
    // ... rest of model
}
```

---

### Step 4: Update Controllers

Use `PermissionService` in controllers for authorization:

**Example Controller Pattern:**

```php
use App\Services\PermissionService;

class ApplicantController extends Controller
{
    public function index()
    {
        // Check permission
        if (!PermissionService::can('list-applicants')) {
            abort(403, 'Unauthorized');
        }

        // Data is automatically filtered by BranchScoped trait
        $applicants = Applicant::with(['branch', 'user'])->paginate(20);

        return view('applicants.index', compact('applicants'));
    }

    public function create()
    {
        if (!PermissionService::canCreate('register-applicants')) {
            abort(403, 'Unauthorized');
        }

        // Branch admins can only create in their branch
        $branches = $this->getAccessibleBranches();

        return view('applicants.create', compact('branches'));
    }

    public function store(Request $request)
    {
        if (!PermissionService::canCreate('register-applicants')) {
            abort(403, 'Unauthorized');
        }

        // Validate branch access
        if (!PermissionService::canAccessBranch($request->branch_id)) {
            abort(403, 'Cannot create applicant in this branch');
        }

        // Create applicant
        $applicant = Applicant::create($request->all());

        return redirect()->route('applicants.index')
            ->with('success', 'Applicant created successfully');
    }

    public function edit(Applicant $applicant)
    {
        if (!PermissionService::canUpdate('update-applicants', $applicant)) {
            abort(403, 'Unauthorized');
        }

        $branches = $this->getAccessibleBranches();

        return view('applicants.edit', compact('applicant', 'branches'));
    }

    public function update(Request $request, Applicant $applicant)
    {
        if (!PermissionService::canUpdate('update-applicants', $applicant)) {
            abort(403, 'Unauthorized');
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

    /**
     * Get branches accessible to current user
     */
    private function getAccessibleBranches()
    {
        if (PermissionService::isSystemAdmin()) {
            return Branch::all();
        }

        if (PermissionService::isBranchAdmin()) {
            $branchId = session('user_branch_id');
            return Branch::where('id', $branchId)->get();
        }

        return collect();
    }
}
```

---

### Step 5: Update Views

**Hide/Show buttons based on permissions:**

```blade
{{-- In your Blade templates --}}

@if(App\Services\PermissionService::can('register-applicants'))
    <a href="{{ route('applicants.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Applicant
    </a>
@endif

@if(App\Services\PermissionService::canUpdate('update-applicants', $applicant))
    <a href="{{ route('applicants.edit', $applicant) }}" class="btn btn-sm btn-info">
        <i class="fas fa-edit"></i> Edit
    </a>
@endif

@if(App\Services\PermissionService::canDelete('delete-applicants', $applicant))
    <form action="{{ route('applicants.destroy', $applicant) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
@endif
```

---

## 📊 Permission Matrix

| Permission | System Admin | Branch Admin | Tutor | Student |
|------------|--------------|--------------|-------|---------|
| **View All Branches** | ✅ | ❌ | ❌ | ❌ |
| **View Own Branch** | ✅ | ✅ | ✅ | ❌ |
| **Create in Any Branch** | ✅ | ❌ | ❌ | ❌ |
| **Create in Own Branch** | ✅ | ✅ | ❌ | ❌ |
| **Update Any Branch** | ✅ | ❌ | ❌ | ❌ |
| **Update Own Branch** | ✅ | ✅ | ❌ | ❌ |
| **Delete Any Branch** | ✅ | ❌ | ❌ | ❌ |
| **Delete Own Branch** | ✅ | ✅ | ❌ | ❌ |
| **View Own Data** | ✅ | ✅ | ✅ | ✅ |

---

## 🔧 Helper Methods

### In Controllers:

```php
// Check if user can access specific branch
if (!PermissionService::canAccessBranch($branchId)) {
    abort(403);
}

// Get accessible branch IDs
$branchIds = PermissionService::getAccessibleBranchIds();

// Check role
if (PermissionService::isSystemAdmin()) {
    // System admin logic
}

if (PermissionService::isBranchAdmin()) {
    // Branch admin logic
}
```

### In Models:

```php
// Check if user can access this record
if (!$applicant->canAccess()) {
    abort(403);
}

// Query only user's branch
$applicants = Applicant::forUserBranch()->get();

// Query specific branch
$applicants = Applicant::forBranch($branchId)->get();
```

---

## 🎯 Usage Examples

### Example 1: Filtering Applicants

```php
// System Admin sees all
$applicants = Applicant::all();

// Branch Admin sees only their branch (automatic)
$applicants = Applicant::all(); // Automatically filtered

// Tutor sees only their branch (automatic)
$applicants = Applicant::all(); // Automatically filtered

// Student sees only their own record (automatic)
$applicants = Applicant::all(); // Automatically filtered
```

### Example 2: Creating Records

```php
public function store(Request $request)
{
    // Validate permission
    if (!PermissionService::canCreate('register-applicants')) {
        return back()->with('error', 'Unauthorized');
    }

    // For branch admin, force their branch
    if (PermissionService::isBranchAdmin()) {
        $request->merge(['branch_id' => session('user_branch_id')]);
    }

    // Validate branch access
    if (!PermissionService::canAccessBranch($request->branch_id)) {
        return back()->with('error', 'Cannot create in this branch');
    }

    $applicant = Applicant::create($request->all());

    return redirect()->route('applicants.index')
        ->with('success', 'Created successfully');
}
```

### Example 3: Branch Dropdown in Forms

```blade
<select name="branch_id" class="form-control" required>
    @if(App\Services\PermissionService::isSystemAdmin())
        {{-- System admin sees all branches --}}
        @foreach(\App\Models\Branch::all() as $branch)
            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
        @endforeach
    @else
        {{-- Branch admin sees only their branch --}}
        @php
            $userBranch = session('user_branch');
        @endphp
        @if($userBranch)
            <option value="{{ $userBranch->id }}" selected>{{ $userBranch->name }}</option>
        @endif
    @endif
</select>
```

---

## 🔍 Testing

### Test Branch Access:

1. **Login as System Admin**
   - Should see all branches
   - Can create/edit/delete in any branch

2. **Login as Branch Admin**
   - Should see only their branch data
   - Can create/edit/delete in their branch only
   - Cannot access other branches

3. **Login as Tutor**
   - Should see only their branch data
   - Cannot create/edit/delete (read-only)

4. **Login as Student**
   - Should see only their own records
   - Cannot see other students' data

---

## 📝 Models to Update

Add `BranchScoped` trait to these models:

- ✅ `Applicant` (already done)
- ⏳ `Venue`
- ⏳ `Tutor`
- ⏳ `CourseAssignment`
- ⏳ Any custom models with `branch_id`

---

## ⚠️ Important Notes

1. **Session Management**: Branch ID is stored in session for performance
2. **Global Scopes**: Automatically applied to all queries
3. **Override Scopes**: Use `withoutGlobalScope('branch')` if needed
4. **Performance**: Indexes on `branch_id` columns recommended

---

## 🐛 Troubleshooting

### Issue: Branch admin sees no data
**Solution:** Check if user has `branch_id` set or has tutor/applicant record

### Issue: Global scope not working
**Solution:** Make sure trait is added to model and model has `branch_id` column

### Issue: Permission denied errors
**Solution:** Check user's role and permissions in `davinci_user_permissions` table

---

## 🚀 Next Steps

1. Register middleware in `Kernel.php`
2. Apply middleware to routes
3. Add trait to all models with `branch_id`
4. Update controllers with permission checks
5. Update views to hide/show based on permissions
6. Test with different user roles

---

**Your system is now secured with branch-based access control!** 🎉
