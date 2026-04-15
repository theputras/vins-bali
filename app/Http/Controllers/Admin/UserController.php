<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request): Response
    {
        $query = User::query()->orderBy('created_at', 'desc');

        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->input('role') !== '') {
            $query->where('role', $request->input('role'));
        }

        $users = $query->paginate(15)->withQueryString();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role']),
            'stats' => [
                'totalUsers' => User::count(),
                'adminUsers' => User::where('role', 'admin')->count(),
                'regularUsers' => User::where('role', 'user')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/Form', [
            'user' => null,
            'isEditing' => false,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);
        unset($validated['password_confirmation']);

        User::create($validated);

        return to_route('admin-panel.users.index')
            ->with('success', "User \"{$validated['name']}\" berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/Form', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'two_factor_confirmed_at' => $user->two_factor_confirmed_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'isEditing' => true,
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Only update password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        unset($validated['password_confirmation']);

        // Prevent demoting the last admin
        if ($user->isAdmin() && $validated['role'] === 'user') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak bisa mengubah role admin terakhir.');
            }
        }

        $user->update($validated);

        return to_route('admin-panel.users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Prevent deleting the last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak bisa menghapus admin terakhir.');
            }
        }

        $userName = $user->name;
        $user->delete();

        return to_route('admin-panel.users.index')
            ->with('success', "User \"{$userName}\" berhasil dihapus.");
    }

    /**
     * Toggle user role between admin and user.
     */
    public function toggleRole(User $user): RedirectResponse
    {
        // Prevent toggling yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa mengubah role akun sendiri.');
        }

        // Prevent demoting the last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak bisa mengubah role admin terakhir.');
            }
        }

        $newRole = $user->role === 'admin' ? 'user' : 'admin';
        $user->update(['role' => $newRole]);

        return back()->with('success', "Role \"{$user->name}\" diubah menjadi {$newRole}.");
    }
}
