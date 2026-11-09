<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Country;
use App\Models\Applicant;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\User;
use App\Models\Venue;
use App\Models\Course;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Models\Role;
use App\Traits\SocialSmartTechConstants;

class ApplicantController extends Controller
{
    use SocialSmartTechConstants;
    public function index(Request $request)
    {

        if (!$this->hasPermission('List applicants')) {
            return redirect('permission-denied');
        }

        $search = $request->get('search', '');
        $branch = session('branch');
        $applicants = ($branch instanceof Branch) ? Applicant::where('branch_id', $branch->id)->latest()->get() : Applicant::latest()->get();

        return view('app.applicants.index', compact('applicants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!$this->hasPermission('Register applicants')) {
            return redirect('permission-denied');
        }

        $countries = Country::pluck('name', 'id');
        $courses = Course::pluck('title', 'id');
        $branch = session('branch');
        $venues = (!($branch instanceof Branch)) ? Venue::pluck('name', 'id') : Venue::where('branch_id', $branch->id)->pluck('name', 'id');

        return view('app.applicants.create', compact('countries', 'courses', 'venues'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request): RedirectResponse
    {
        if (!$this->hasPermission('Register applicants')) {
            return redirect('permission-denied');
        }

        $settings = \App\Models\Setting::first();
        $check_upload_receipt = $settings->enable_upload_receipt;

        $validated = $request->validate([
            'full_name' => ['required', 'max:255', 'string'],
            'home_address' => ['nullable', 'max:255', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'office_of_work' => ['nullable', 'max:255', 'string'],
            'occupation' => ['nullable', 'max:255', 'string'],
            'mobile' => ['required', 'max:255', 'string', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone_2' => ['nullable', 'max:255', 'string'],
            'name_of_sponsor' => ['nullable', 'max:255', 'string'],
            'education_level' => ['required', 'max:255', 'string'],
            'is_staff_student' => ['nullable', 'in:0,1'],
            'parent_staff_id' => [$request->input('is_staff_student') == '1' ? 'required' : 'nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:4096'],
            'discount_attachment' => [$request->input('apply_discount') == '1' ? 'required' : 'nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:4096'],

            'amount' => ['required', 'numeric'],
            'installment_type' => ['required', 'in:quarter,half,full'],
            'date' => ['required', 'date'],
            'receipt' => [$check_upload_receipt ? 'required' : 'nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:4096'],
            'receipt_number' => ['required','string','max:255'],
        ]);



        //check if apply_discount == 1 then make sure discount_attachment is required
        $apply_discount = $request->apply_discount;
        if ($apply_discount == '1' && !$request->hasFile('discount_attachment')) {
            return back()->with('error', 'Discount Attachment is required if you choose discount !');

        }



        // staff student validation: require parent_staff_id when selected
        $is_staff_student = $request->input('is_staff_student');
        if ($is_staff_student == '1' && !$request->hasFile('parent_staff_id')) {
            return back()->with('error', 'Parent Staff ID is required for Staff Students.');
        }

        // validate mobile number
        $phone = validatePhoneNumber($validated['mobile']);
        if (!empty($request->phone_2)) {
            $phone_2 = validatePhoneNumber($validated['phone_2']);
            $validated['phone_2'] = $phone_2;
        }

        $enrollmentData = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'session' => ['required', 'in:morning,afternoon'],
            'venue_id' => ['required', 'exists:venues,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'fee' => ['required', 'numeric'],
            'duration' => ['nullable', 'max:255', 'string'],
        ]);

        $userData = [
            'name' => $validated['full_name'],
            'email' => strtolower($validated['email']),
            'user_type' => 'student',
            'phone' => $phone,
            'address' => $validated['home_address'],
            'password' => Hash::make('password'),
        ];



        DB::transaction(function () use ($validated, $userData, $enrollmentData, $phone, $request) {
            $user = User::create($userData);

            $user->assignRole(Role::findByName('student'));
            allocatePermission($user->davinci_role_id,$user->id);
            $message = 'Dear ' . $user->name . ', Welcome to ' . config('app.name') . '. Your Admission has successfully been created. your login credentials are as follows: Email: ' . $user->email . ' Password: password, Remember to change your password after login.';
            $data = [
                'sender_id' => auth()->user()->id,
                'receiver_id' => $user->id,
                'body' => $message,
                'opened' => 0,
                'status' => 0,
            ];
            $message = \App\Models\Message::create($data);
            $validated['user_id'] = $user->id;
            $validated['mobile'] = $phone;
            $validated['name'] = $validated['full_name'];
            $validated['email'] = strtolower($validated['email']);

            $discount_reason = $request->discount_reason;
            if (!empty ($discount_reason) && strlen($discount_reason) > 5) {

                switch ($discount_reason) {
                    case 'staff_child':
                        $validated['is_staff_family_member'] = true;
                        break;
                    case 'staff_member':
                        $validated['is_staff'] = true;
                        break;
                    case 'out_student':
                        $validated['is_out_student'] = true;
                        break;
                    default:
                        $validated['is_staff_family_member'] = false;
                        $validated['is_staff'] = false;
                        $validated['is_out_student'] = false;
                        break;
                }
            }
            $tutor = \App\Models\Tutor::where('user_id', auth()->user()->id)->first();

            $venue = Venue::find($enrollmentData['venue_id']);
            $validated['branch_id'] = $venue->branch_id;
            // if(auth()->user()->hasRole('tutor') && !empty($tutor)){
            //     $validated['branch_id'] = $tutor->branch_id;
            // }
            // mark staff student flags on applicant payload
            if ($request->input('is_staff_student') == '1') {
                $validated['is_staff_student'] = true;
            }

            $applicant = Applicant::create($validated);

            // handle parent staff ID upload and attach to applicant
            if ($request->input('is_staff_student') == '1' && $request->hasFile('parent_staff_id')) {
                $parentIdFile = $request->file('parent_staff_id');
                $allowedExtensions = ['pdf', 'jpeg', 'png', 'jpg'];
                $extension = strtolower($parentIdFile->guessExtension() ?: $parentIdFile->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions, true)) {
                    throw new \InvalidArgumentException('Invalid file type for Parent Staff ID.');
                }
                $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($user->name));
                $parentIdName = $cleanName . '_' . $applicant->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_parent_staff_id.' . $safeExt;
                $targetDir = storage_path('app/public/parent_staff_ids');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $parentIdFile->move($targetDir, $parentIdName);
                $applicant->parent_staff_id = $parentIdName;
                $applicant->save();
            }

            $enrollmentData['applicant_id'] = $applicant->id;

            $enrollment = Enrollment::create($enrollmentData);
            $invoicePrefix = generateInvoicePrefix();
            $apply_discount = $request->apply_discount;
            //check if course has discount
            $course = Course::find($enrollment->course_id);
            $basicTitle = 'Basic computer Application';
            $isBasicCourse = (strcasecmp(trim($course->title), $basicTitle) === 0);
            $invoiceData = [
                'applicant_id' => $applicant->id,
                'enrollment_id' => $enrollment->id,
                'balance' => $enrollment->fee,
                'paid_amount' => 0,
                'description' => '',
                'invoice_prefix' => $invoicePrefix,
                'date' => now(),
                'status' => '0',
            ];

            // Apply 50% discount for staff students ONLY if course is Basic computer Application
            if ($request->input('is_staff_student') == '1' && $isBasicCourse) {
                $invoiceData += [
                    'has_discount' => true,
                    'discount_amount' => ($course->fee * 0.5),
                    'discount_reason' => 'Staff Student 50%'
                ];
            }
            if ($request->input('is_staff_student') != '1' && $course->has_discount && $apply_discount == '1' && (strlen($request->discount_reason) > 5)) {
                $invoiceData += [
                    'has_discount' => true,
                    'discount_amount' => $course->discount,
                    'discount_reason' => ucwords(str_replace('_', ' ', trim($request->discount_reason))),
                ];
                if ($request->hasFile('discount_attachment')) {
                    $discount_attachment = $request->file('discount_attachment');
                    $allowedExtensions = ['pdf', 'jpeg', 'png', 'jpg'];
                    $extension = strtolower($discount_attachment->guessExtension() ?: $discount_attachment->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions, true)) {
                        throw new \InvalidArgumentException('Invalid file type for Discount Attachment.');
                    }
                    $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
                    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($user->name));
                    $discount_attachmentName = $cleanName . '_' . $applicant->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_discount_attachment.' . $safeExt;
                    $targetDir = storage_path('app/public/discount_attachments');
                    if (!file_exists($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }
                    $discount_attachment->move($targetDir, $discount_attachmentName);
                    $invoiceData['discount_file'] = $discount_attachmentName;
                }
            }
            $invoice = Invoice::create($invoiceData);

            if ($request->hasFile('receipt')) {
                $receipt = $request->file('receipt');
                $allowedExtensions = ['pdf', 'jpeg', 'png', 'jpg'];
                $extension = strtolower($receipt->guessExtension() ?: $receipt->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions, true)) {
                    throw new \InvalidArgumentException('Invalid file type for Receipt.');
                }
                $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
                $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($user->name));
                $receiptName = $cleanName . '_' . $applicant->id . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '_receipt_reference.' . $safeExt;
                $targetDir = storage_path('app/public/receipts');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $receipt->move($targetDir, $receiptName);
                $validated['receipt'] = $receiptName;
            }
            $valid_amount = $course->fee;
            if (!empty($valid_amount) && (int) $valid_amount > 0) {
                // Staff students pay 50% and are considered fully paid ONLY for the Basic course
                if ($request->input('is_staff_student') == '1' && $isBasicCourse) {
                    $base = $invoice->has_discount ? ($valid_amount - $invoice->discount_amount) : $valid_amount;
                    $amount = $base; // base already 50% due to discount above
                    $validated['payment_status'] = 0; // full
                } else {
                    $installment = $request->input('installment_type');
                    if ($installment === 'quarter') {
                        $base = $invoice->has_discount ? ($valid_amount - $invoice->discount_amount) : $valid_amount;
                        $amount = ($base * 0.25);
                        $validated['payment_status'] = 1; // partial
                    } elseif ($installment === 'half') {
                        $base = $invoice->has_discount ? ($valid_amount - $invoice->discount_amount) : $valid_amount;
                        $amount = ($base * 0.5);
                        $validated['payment_status'] = 1; // partial
                    } else { // full
                        $amount = $invoice->has_discount ? ($valid_amount - $invoice->discount_amount) : $valid_amount;
                        $validated['payment_status'] = 0; // full
                    }
                }
            }

            $payment = $invoice->payments()->create([
                'applicant_id' => $applicant->id,
                'course_id' => $enrollment->course_id,
                'amount' => $amount,
                'date' => $validated['date'],
                'payment_status' => $validated['payment_status'],
                'receipt' => $validated['receipt'] ?? null,
                'receipt_number' => $validated['receipt_number'],
                'installment_type' => $request->input('installment_type'),
                'user_id' => $user->id,
            ]);
            //after done payment update invoice status
            if ($request->input('is_staff_student') == '1' && $isBasicCourse) {
                $invoice_status = '1';
            } else {
                if ($request->input('installment_type') === 'full') {
                    $invoice_status = '1';
                } else {
                    $invoice_status = '2';
                }
            }

            if ($invoice->has_discount) {
                $valid_amount = ($valid_amount - $invoice->discount_amount);
            }

            // dd($invoice_status,$amount,$payment->amount,$valid_amount,$invoice->discount_amount);
            $invoice->update([
                'status' => $invoice_status,
                'paid_amount' => $payment->amount,
                'balance' => $invoice_status === '1' ? 0 : ($valid_amount - $payment->amount),
            ]);


            if ($invoice && $payment && $applicant && $enrollment && $user) {
                //send sms to applicant
                $message = 'Dear ' . $user->name . ',  Welcome to ' . config('app.name') . '. You are successfull Enrolled to ' . $course->title . ' Course. Please login to your account to view your invoice and payments and Course Teaching Schedules. Your login credentials are as follows: Email: ' . $user->email . ' Password: password, Remember to change your password after login. Use this link to login: ' . route('login');
                $data = [
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $user->id,
                    'body' => $message,
                    'opened' => 0,
                    'status' => 0,
                ];
                $message = Message::create($data);
                //send payment sms to applicant with remained balance and total paid amount
                $message = 'Dear ' . $user->name . ',  Your payment of ' . $payment->amount . ' has been received. Your balance is ' . $invoice->balance . '. Thank you for Enrolling course with us.';
                $data = [
                    'sender_id' => auth()->user()->id,
                    'receiver_id' => $user->id,
                    'body' => $message,
                    'opened' => 0,
                    'status' => 0,
                ];
                $message = Message::create($data);

                //send sms to user with role system-admin
                $system_admins = User::whereHas('role_',function($query){
                    $query->where('name','system-admin');
                })->get();
                foreach ($system_admins as $super_admin) {
                    $message = 'Dear ' . $super_admin->name . ',  ' . $user->name . ' has successfully been enrolled to ' . $course->title . ' Course. Please login to your account to view the applicant invoice and payments and Course Teaching Schedules. Use this link to login: ' . route('login');
                    $data = [
                        'sender_id' => auth()->user()->id,
                        'receiver_id' => $super_admin->id,
                        'body' => $message,
                        'opened' => 0,
                        'status' => 0,
                    ];
                    $message = Message::create($data);
                }

                return to_route('applicants.index')
                    ->with('success', $user->name . ' Registered Successfull !');
            } else {
                return to_route('applicants.index')
                    ->with('error', 'Something went wrong !');
            }


        });

        return to_route('applicants.index')
            ->withSuccess('Applicant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Applicant $applicant)
    {
        if (!$this->hasPermission('View applicants')) {
            return redirect('permission-denied');
        }

        return view('app.applicants.show', compact('applicant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Applicant $applicant)
    {
        if (!$this->hasPermission('Update applicants')) {
            return redirect('permission-denied');
        }

        $countries = Country::pluck('name', 'id');
        $roles = Role::pluck('name', 'id');

        return view('app.applicants.edit', compact('applicant', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        Applicant $applicant
    ): RedirectResponse {
        if (!$this->hasPermission('Update applicants')) {
            return redirect('permission-denied');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'max:255', 'string'],
            'home_address' => ['nullable', 'max:255', 'string'],
            'country_id' => ['required', 'exists:countries,id'],
            'office_of_work' => ['nullable', 'max:255', 'string'],
            'occupation' => ['nullable', 'max:255', 'string'],
            'mobile' => ['required', 'max:255', 'string'],
            'email' => ['nullable', 'email'],
            'phone_2' => ['nullable', 'max:255', 'string'],
            'name_of_sponsor' => ['nullable', 'max:255', 'string'],
            'education_level' => ['required', 'max:255', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $applicant->update($validated);
        $applicant->user->update([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => validatePhoneNumber($validated['mobile']),
            'address' => $validated['home_address'],
        ]);

        if (!$applicant->user->hasRole('student')) {
            $applicant->user->syncRoles(Role::findByName('student'));
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowedExtensions = ['jpeg', 'png', 'jpg'];
            $extension = strtolower($image->guessExtension() ?: 'jpg');
            if (!in_array($extension, $allowedExtensions, true)) {
                return back()->with('error', 'Invalid image format. Allowed: JPG, PNG.');
            }
            $safeExt = $extension === 'jpeg' ? 'jpg' : $extension;
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $safeExt;
            $manager = new \Intervention\Image\ImageManager(['driver' => extension_loaded('imagick') ? 'imagick' : 'gd']);
            $image_resize = $manager->make($image->getRealPath());
            $image_resize->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image_resize->encode($safeExt, 90);
            $targetDir = storage_path('app/public/profiles');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            if ($applicant->user && $applicant->user->image) {
                Storage::delete('public/profiles/' . $applicant->user->image);
            }
            $image_resize->save($targetDir . '/' . $filename);

            $applicant->user->image = $filename;
            $applicant->user->save();
        }

        return redirect()
            ->route('applicants.index',)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Applicant $applicant
    ) {
        if (!$this->hasPermission('Delete applicants')) {
            return redirect('permission-denied');
        }

        $applicant->delete();
        echo 1;
    }
}
