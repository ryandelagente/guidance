<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use App\Models\GoodMoralCertificate;
use App\Models\StudentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GoodMoralCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodMoralCertificate::with(['studentProfile', 'issuedBy'])->latest('issued_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('studentProfile', fn ($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('student_id_number', 'like', "%$s%")
            );
        }

        $certificates = $query->paginate(20)->withQueryString();
        return view('certificates.index', compact('certificates'));
    }

    public function create(Request $request)
    {
        $students   = StudentProfile::orderBy('last_name')->get();
        $preselect  = $request->filled('clearance_id')
            ? ClearanceRequest::with('studentProfile')->find($request->clearance_id)
            : null;
        return view('certificates.create', compact('students', 'preselect'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_profile_id'  => 'required|exists:student_profiles,id',
            'clearance_request_id'=> 'nullable|exists:clearance_requests,id',
            'purpose'             => 'required|string|max:500',
            'validity_months'     => 'required|integer|min:1|max:24',
        ]);

        $cert = GoodMoralCertificate::create(array_merge($data, [
            'issued_by'          => $request->user()->id,
            'certificate_number' => GoodMoralCertificate::generateNumber(),
            'issued_at'          => now(),
        ]));

        return redirect()->route('certificates.show', $cert)
            ->with('success', 'Certificate issued successfully.');
    }

    public function show(GoodMoralCertificate $certificate)
    {
        $certificate->load(['studentProfile', 'issuedBy', 'clearanceRequest']);
        return view('certificates.show', compact('certificate'));
    }

    public function print(GoodMoralCertificate $certificate)
    {
        $certificate->load(['studentProfile', 'issuedBy']);
        return view('certificates.print', compact('certificate'));
    }

    public function download(GoodMoralCertificate $certificate)
    {
        $certificate->load(['studentProfile', 'issuedBy']);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'GMC-' . $certificate->certificate_number . '.pdf';

        return $pdf->download($filename);
    }

    public function revoke(Request $request, GoodMoralCertificate $certificate)
    {
        $data = $request->validate([
            'revoked_reason' => 'required|string|max:500',
        ]);
        $certificate->update(array_merge($data, ['is_revoked' => true]));
        return redirect()->route('certificates.show', $certificate)->with('success', 'Certificate revoked.');
    }

    public function destroy(GoodMoralCertificate $certificate)
    {
        abort_unless(request()->user()->isSuperAdmin(), 403);
        $certificate->delete();
        return redirect()->route('certificates.index')->with('success', 'Certificate deleted.');
    }
}
