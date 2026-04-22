<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('role')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }

        $users = $query->paginate(25)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:super_admin,guidance_director,guidance_counselor,student,faculty',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'password'  => Hash::make($data['password']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User account created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:200',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:super_admin,guidance_director,guidance_counselor,student,faculty',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $update = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function toggle(User $user)
    {
        abort_if($user->isSuperAdmin() && auth()->id() !== $user->id, 403, 'Cannot deactivate another super admin.');
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User account {$status}.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');
        abort_if($user->isSuperAdmin(), 403, 'Super admin accounts cannot be deleted.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
