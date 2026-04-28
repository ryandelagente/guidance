<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentSelfServiceController extends Controller
{
    /**
     * Student-facing profile page — only fields they're allowed to update.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);

        $profile = $user->studentProfile;
        abort_unless($profile, 403, 'No student profile linked. Contact your guidance counselor.');

        $profile->load('emergencyContacts');

        return view('my-profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);

        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $data = $request->validate([
            // Allowed editable fields ONLY
            'contact_number'    => 'nullable|string|max:30',
            'home_address'      => 'nullable|string|max:500',
            'civil_status'      => 'nullable|string|max:50',
            'religion'          => 'nullable|string|max:100',
            'father_name'       => 'nullable|string|max:200',
            'father_occupation' => 'nullable|string|max:200',
            'father_contact'    => 'nullable|string|max:30',
            'mother_name'       => 'nullable|string|max:200',
            'mother_occupation' => 'nullable|string|max:200',
            'mother_contact'    => 'nullable|string|max:30',
            'guardian_name'     => 'nullable|string|max:200',
            'guardian_relationship' => 'nullable|string|max:100',
            'guardian_contact'  => 'nullable|string|max:30',
            'profile_photo'     => 'nullable|image|max:5120',  // 5MB
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($profile->profile_photo) {
                Storage::disk('public')->delete($profile->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $profile->update($data);

        return redirect()->route('my-profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Add an emergency contact.
     */
    public function storeContact(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        $profile = $user->studentProfile;
        abort_unless($profile, 403);

        $data = $request->validate([
            'name'           => 'required|string|max:200',
            'relationship'   => 'required|string|max:100',
            'contact_number' => 'required|string|max:30',
            'address'        => 'nullable|string|max:500',
        ]);

        $data['student_profile_id'] = $profile->id;
        $data['is_primary'] = $profile->emergencyContacts()->count() === 0;

        EmergencyContact::create($data);

        return back()->with('success', 'Emergency contact added.');
    }

    public function deleteContact(Request $request, EmergencyContact $contact)
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403);
        abort_unless($contact->student_profile_id === $user->studentProfile?->id, 403);

        $contact->delete();
        return back()->with('success', 'Contact removed.');
    }
}
