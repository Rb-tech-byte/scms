<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class CertificateController
 *
 * Coordinates student graduation certificates and public verification.
 *
 * IMPLEMENTATION GUIDE FOR FUTURE DEVELOPERS:
 * 1. Certificate Eligibility Criteria:
 *    A student is eligible for an official completion certificate when:
 *    a) The course enrollment duration has concluded (end_date <= now()).
 *    b) Associated invoice balance is zero ($invoice->balance == 0 && $invoice->status == '1').
 *    c) Attendance meets minimum threshold (e.g. >= 80% if tracking attendance).
 *
 * 2. Generating Certificates (index / generate action):
 *    - Use Barryvdh\DomPDF or Codedge\Fpdf (both already installed in composer.json).
 *    - Layout: Landscape A4 format with institutional crest, certificate border, student name,
 *      course title, completion date, and authorized signatory signatures.
 *    - Embed a QR code generated via `SimpleSoftwareIO\QrCode` linking directly to the verification URL:
 *      route('certificates.verify', ['serial' => $certificate->serial_number])
 *
 * 3. Public Verification (verify action):
 *    - Publicly accessible endpoint without authentication.
 *    - Validates scanned QR code serial against database records.
 *    - Displays student name, course title, issuance date, and authenticity badge.
 */
class CertificateController extends Controller
{
    /**
     * Display listing of generated or eligible certificates.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('certificates.index');
    }

    /**
     * Public certificate verification endpoint.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function verify()
    {
        return view('certificates.verify');
    }
}
