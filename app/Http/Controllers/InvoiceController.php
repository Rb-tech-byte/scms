<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use App\Models\Applicant;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use \Spatie\Activitylog\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\Traits\SocialSmartTechConstants;
use App\Services\PermissionService;



class InvoiceController extends Controller
{
    use SocialSmartTechConstants;
    protected $data = [];

    public function __construct()
    {
        $this->data['site_settings'] =  getSiteSettings();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!PermissionService::can('List invoices')) {
            abort(403, 'Unauthorized');
        }

        $this->data['invoices'] = Invoice::latest()->get();
        return view('app.invoices.index', $this->data);
    }

    public function partial_invoice(Request $request)
    {
        if (!PermissionService::can('List invoices')) {
            abort(403, 'Unauthorized');
        }

        $this->data['invoices'] = Invoice::where(function ($query) {
            $query->where('status', '2')
                  ->orWhere('status', '0');
        })->latest()->get();

        return view('app.invoices.index', $this->data);
    }

    public function paid_invoice(Request $request)
    {
        if (!PermissionService::can('List invoices')) {
            abort(403, 'Unauthorized');
        }

        $this->data['invoices'] = Invoice::where('status', '1')->latest()->get();
        return view('app.invoices.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): RedirectResponse
    {
        if (!PermissionService::canCreate('Add invoices')) {
            abort(403, 'Unauthorized');
        }
        activity()
                ->causedBy(auth()->user())
                ->event('Create new Invoice')
                ->createdAt(now())
                ->log('Tried Create Invoice');


        return to_route('invoices.index')->with('warning', 'Invoice is generated Automatically During course Enrollment , Contact Support for further assistance.');
        $this->data['applicants'] = Applicant::pluck('full_name', 'id');
        $this->data['enrollments'] = Enrollment::pluck('start_date', 'id');



        return view(
            'app.invoices.create',
            $this->data
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!PermissionService::canCreate('Add invoices')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'balance' => ['required', 'numeric'],
            'paid_amount' => ['required', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
            'date' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2'],
        ]);

        $validated['status'] = '0';
        $this->data['invoice'] = Invoice::create($validated);

        return redirect()
            ->route('invoices.edit', $this->data)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Invoice $invoice)
    {
        if (!PermissionService::canView('View invoices', $invoice)) {
            abort(403, 'Unauthorized');
        }
        $this->data['invoice'] = $invoice;
        return view('app.invoices.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Invoice $invoice): RedirectResponse
    {
         if (!PermissionService::canUpdate('Update invoices', $invoice)) {
            abort(403, 'Unauthorized');
        }

        activity()
            ->causedBy(auth()->user())
            ->event('Edit Invoice')
            ->createdAt(now())
            ->log('Tried To Edit Invoice');

        return to_route('invoices.index')->with('warning', 'You cannot edit this invoice, Contact Support for further assistance.');

        $this->data['applicants'] = Applicant::pluck('full_name', 'id');
        $this->data['enrollments'] = Enrollment::pluck('start_date', 'id');


        return view(
            'app.invoices.edit',
            $this->data
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
         if (!PermissionService::canUpdate('Update invoices', $invoice)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'applicant_id' => ['required', 'exists:applicants,id'],
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'balance' => ['required', 'numeric'],
            'paid_amount' => ['required', 'numeric'],
            'description' => ['nullable', 'max:255', 'string'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:0,1,2'],
        ]);
        $beforeUpdate = $invoice->toJson(JSON_PRETTY_PRINT);

        $invoice->update($validated);
        $this->data['invoice'] = $invoice;

        activity()
            ->causedBy(auth()->user())
            ->event('Tried Payment Edit')
            ->createdAt(now())
            ->withProperties(['attributes' => $invoice->toJson(JSON_PRETTY_PRINT)])
            ->log($beforeUpdate);

        return redirect()
            ->route('invoices.edit', $this->data)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Invoice $invoice
    ) {
        if (!PermissionService::canDelete('Delete invoices', $invoice)) {
            abort(403, 'Unauthorized');
        }

        //check if invoice has payments then if it have payments returns warning to delete payments first
        if ($invoice->payments()->count() > 0) {
            return redirect()->back()
                ->with('warning', 'You can not delete this invoice because it has payments');
        }

        $invoice->delete();
        echo 1;

        // return redirect()
        //     ->route('invoices.index')
        //     ->withSuccess(__('crud.common.removed'));
    }
    /**
     * Generate invoice pdf using dompdf
     * The method accept invoice Id and find invoice details which will be used to generate pdf
     */

    public function generate_invoice_pdf($id){

        $invoice = Invoice::findOrFail($id);
        
        if (!PermissionService::canView('View invoices', $invoice)) {
            abort(403, 'Unauthorized');
        }

        $status = $invoice->status;
        $status_color = '52, 71, 209';
        $text_color = '52, 71, 209';
        if ($status == '0') {
            $status_color = '212, 83, 74';
            $text_color = '212, 83, 74';
            $message = 'Unpaid';
        } elseif ($status == '1') {
            $status_color = '21, 214, 92';
            $text_color = '21, 214, 92';
            $message = 'Paid';
        } elseif ($status == '2') {
            $status_color = '230, 206, 30';
            $text_color = '230, 206, 30';
            $message = 'Partially Paid';
        }
        qrcode($invoice->id, 'png');
        list($r, $g, $b) = explode(', ', $status_color);
        $pdf = new Fpdf('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        // Invoice Logo
        $pdf->Image(public_path('mini.png'), 10, 10, 40);

        // Invoice Status
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 123, 255);
        $pdf->Cell(40, 10, '', 0);
        $pdf->Cell(40, 10, '', 0);
        $pdf->Cell(50, 10, '#' . $invoice->invoice_prefix, 1, 0, 'C', 1);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor($r, $g, $b);
        $pdf->Cell(50, 10, $message, 1, 1, 'C', 1);

        // Billing Date
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(130);
        $pdf->Cell(40, 10, 'Billing Date: ' . $invoice->date->format('d-m-Y'), 0, 1);

        // Invoice Details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(20);
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->Cell(50, 0, '', 0,0,'R');
        $pdf->Ln();
        $pdf->Cell(50, 7, 'Institute of Education and', 0,0,'L');
        $pdf->Cell(80, 7, '', 0);
        $pdf->Cell(50, 7, 'Invoice To: ' . $invoice->applicant->full_name, 0,0,'R');
        $pdf->Ln();
        $pdf->Cell(50, 7, 'Management Technologies (IEMT)', 0, 0, 'L');
        $pdf->Cell(80, 7, '', 0);
        $pdf->Cell(50, 7, 'Enrollment Date: ' . $invoice->date->format('d-m-Y'), 0, 0, 'R');
        $pdf->Ln();
        $pdf->Cell(50, 7, 'P.O.BOX 23409, Kinondoni,', 0, 0, 'L');
        $pdf->Cell(80, 7, '', 0);
        $pdf->Cell(50, 7, 'Invoice Number: ' . $invoice->invoice_prefix, 0, 0, 'R');
        $pdf->Ln();
        $pdf->Cell(50, 7, 'Dar es Salaam, Tanzania', 0, 0, 'L');
        $pdf->Cell(80, 7, '', 0);
        // Receipt Numbers and Installment from DB
        $receiptNumbers = $invoice->payments->pluck('receipt_number')->filter()->implode(', ');
        $latest = $invoice->payments->sortByDesc('date')->first();
        $installmentMap = ['quarter' => 'Quarter (25%)', 'half' => 'Half (50%)', 'full' => 'Full (100%)'];
        $installmentLabel = $latest ? ($installmentMap[$latest->installment_type] ?? ($latest->payment_status ? 'Partial' : 'Full')) : '-';
        $pdf->Cell(50, 7, 'Invoice Status: ' . $message, 0, 0, 'R');
        $pdf->Ln();
        $pdf->Cell(50, 7, '+255 799 888 234 | +255 799 888 233', 0, 0, 'L');
        $pdf->Cell(80, 7);
        $pdf->Cell(50, 7);
        $pdf->Ln(8);
        $pdf->Cell(50, 7, 'Receipt No(s): ' . ($receiptNumbers ?: '-'), 0, 0, 'L');
        $pdf->Cell(80, 7, '', 0);
        $pdf->Cell(50, 7, 'Installment: ' . $installmentLabel, 0, 0, 'R');


        // Table Headers
        $pdf->SetDrawColor(159, 161, 160);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(20);
        $pdf->Cell(10, 10, '#', 1);
        $pdf->Cell(60, 10, 'Course', 1);
        $pdf->Cell(20, 10, 'Quantity', 1);
        $pdf->Cell(50, 10, 'Enrollment Cost (Tsh)', 1);
        $pdf->Cell(40, 10, 'Total', 1,0,'C');
        $pdf->Ln();


        // Table Data
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(10, 10, '1.', 1);
        $pdf->Cell(60, 10, $invoice->enrollment->course->title, 1);
        $pdf->Cell(20, 10, '1', 1);
        $pdf->Cell(50, 10, number_format($invoice->enrollment->course->fee, 2, '.', ','), 1);
        $pdf->Cell(40, 10, number_format($invoice->enrollment->course->fee, 2, '.', ',') . ' Tsh', 1);
        $pdf->Ln();

        // Subtotal, Discount, Balance
        $pdf->Ln(10);
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(130, 129, 129);
        $pdf->Cell(50, 0, 'Sub-total: ' . number_format($invoice->enrollment->course->fee, 2, '.', ',') . ' Tsh', 0, 1, 'R');
        $pdf->Ln(5);
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 0, ': ' . number_to_words($invoice->enrollment->course->fee) . '', 0,0,'R');
        $pdf->Ln(5);

        $pdf->Cell(50, 0, '', 0);
        $pdf->Image(public_path('qrcodes/qrcode-'.$invoice->id.'.png'), 10, 140, 30);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(247, 62, 62);
        $pdf->Cell(50, 10, 'Discount: ' . number_format($invoice->discount_amount, 2, '.', ',') . ' Tsh', 0, 0,'R');
        $pdf->Ln();
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(50, 0, ': ' . number_to_words($invoice->discount_amount) . '', 0, 0, 'R');
        $pdf->Ln(10);
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetTextColor(56, 55, 55);
        $pdf->SetFont('Arial', 'B', 12);
        $isPartialInstallment = in_array(optional($latest)->installment_type, ['quarter','half']);
        $prefix = ($invoice->status == '2' || $isPartialInstallment) ? '- ' : '';
        $pdf->Cell(50, 10, 'Balance: ' . $prefix . number_format($invoice->balance, 2, '.', ',') . ' Tsh', 0, 0,'R');
        $pdf->Ln();
        $pdf->Cell(50, 0, '', 0);
        $pdf->Cell(80, 10, '', 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 0, ': ' . number_to_words($invoice->balance) . '', 0, 0, 'R');

        // Notes
        $pdf->Ln(20);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 180, $pdf->GetY()); // Adjust the length as needed
        $pdf->Cell(0, 10, '---------xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx--------', 0, 1);
        $pdf->MultiCell(0, 10, getSiteSettings()->invoice_footer ?? '');

        // Output PDF
        $pdf->Output('Invoice_' . $invoice->invoice_prefix . '.pdf', 'I');
        // $pdf->Output();

    }
}
