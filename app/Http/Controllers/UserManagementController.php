<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with search and filter functionality
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            // Filter by role
            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            // Sort by latest first
            $users = $query->latest()->paginate(10)->withQueryString();

            // Calculate statistics efficiently
            $stats = [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'manager' => User::where('role', 'manager')->count(),
                'staff' => User::where('role', 'staff')->count(),
            ];

            // Log user access
            Log::info('User Management accessed', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'ip_address' => $request->ip(),
                'search_term' => $request->search,
                'filter_role' => $request->role,
                'timestamp' => now()->toDateTimeString()
            ]);

            return view('users.index', compact('users', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error loading user management index', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('dashboard')
                           ->with('error', 'Terjadi kesalahan saat memuat halaman user management.');
        }
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        try {
            Log::info('User create form accessed', [
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return view('users.create');

        } catch (\Exception $e) {
            Log::error('Error loading user create form', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat memuat form tambah user.');
        }
    }

    /**
     * Store a newly created user in database
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'min:2'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'in:admin,manager,staff'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.min' => 'Nama minimal 2 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email sudah terdaftar.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'role.required' => 'Role wajib dipilih.',
                'avatar.image' => 'File harus berupa gambar.',
                'avatar.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            DB::beginTransaction();

            $userData = [
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'email_verified_at' => now(), // Auto verify for admin created users
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                try {
                    $avatar = $request->file('avatar');
                    $filename = 'avatar_' . Str::random(20) . '.' . $avatar->getClientOriginalExtension();
                    $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
                    $userData['avatar'] = $avatarPath;
                } catch (\Exception $e) {
                    Log::warning('Avatar upload failed during user creation', [
                        'error' => $e->getMessage(),
                        'user_email' => $validated['email']
                    ]);
                    // Continue without avatar
                }
            }

            $user = User::create($userData);

            DB::commit();

            // Log successful user creation
            Log::info('New user created successfully', [
                'created_user_id' => $user->id,
                'created_user_name' => $user->name,
                'created_user_email' => $user->email,
                'created_user_role' => $user->role,
                'created_by_user_id' => Auth::id(),
                'created_by_user_name' => Auth::user()->name,
                'ip_address' => $request->ip(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('success', "User '{$user->name}' berhasil ditambahkan!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating new user', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password', 'password_confirmation']),
                'user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menambahkan user. Silakan coba lagi.')
                         ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        try {
            Log::info('User detail viewed', [
                'viewed_user_id' => $user->id,
                'viewed_user_name' => $user->name,
                'viewer_user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return view('users.show', compact('user'));

        } catch (\Exception $e) {
            Log::error('Error loading user detail', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'viewer_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat memuat detail user.');
        }
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        try {
            Log::info('User edit form accessed', [
                'edited_user_id' => $user->id,
                'edited_user_name' => $user->name,
                'editor_user_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return view('users.edit', compact('user'));

        } catch (\Exception $e) {
            Log::error('Error loading user edit form', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'editor_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat memuat form edit user.');
        }
    }

    /**
     * Update the specified user in database
     */
    public function update(Request $request, User $user)
    {
        try {
            // Store original data for comparison
            $originalData = $user->toArray();

            // Validate request
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'min:2'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'role' => ['required', 'in:admin,manager,staff'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.min' => 'Nama minimal 2 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email sudah digunakan user lain.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'role.required' => 'Role wajib dipilih.',
                'avatar.image' => 'File harus berupa gambar.',
                'avatar.max' => 'Ukuran gambar maksimal 2MB.'
            ]);

            DB::beginTransaction();

            $userData = [
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'role' => $validated['role'],
                'updated_at' => now()
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                try {
                    // Delete old avatar if exists
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    $avatar = $request->file('avatar');
                    $filename = 'avatar_' . Str::random(20) . '.' . $avatar->getClientOriginalExtension();
                    $avatarPath = $avatar->storeAs('avatars', $filename, 'public');
                    $userData['avatar'] = $avatarPath;

                } catch (\Exception $e) {
                    Log::warning('Avatar upload failed during user update', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'user_email' => $validated['email']
                    ]);
                    // Continue without updating avatar
                }
            }

            $user->update($userData);

            DB::commit();

            // Log successful user update
            Log::info('User updated successfully', [
                'updated_user_id' => $user->id,
                'updated_user_name' => $user->name,
                'updated_user_email' => $user->email,
                'updated_user_role' => $user->role,
                'original_data' => $originalData,
                'new_data' => $user->fresh()->toArray(),
                'password_changed' => $request->filled('password'),
                'avatar_changed' => $request->hasFile('avatar'),
                'updated_by_user_id' => Auth::id(),
                'updated_by_user_name' => Auth::user()->name,
                'ip_address' => $request->ip(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('success', "User '{$user->name}' berhasil diupdate!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'request_data' => $request->except(['password', 'password_confirmation']),
                'updater_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat mengupdate user. Silakan coba lagi.')
                         ->withInput();
        }
    }

    /**
     * Remove the specified user from database
     */
    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === Auth::id()) {
                return redirect()->route('users.index')
                               ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
            }

            // Store user data for logging before deletion
            $deletedUserData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'avatar' => $user->avatar,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];

            DB::beginTransaction();

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                try {
                    Storage::disk('public')->delete($user->avatar);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete user avatar', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                        'avatar_path' => $user->avatar
                    ]);
                    // Continue with user deletion even if avatar deletion fails
                }
            }

            $user->delete();

            DB::commit();

            // Log successful user deletion
            Log::info('User deleted successfully', [
                'deleted_user_data' => $deletedUserData,
                'deleted_by_user_id' => Auth::id(),
                'deleted_by_user_name' => Auth::user()->name,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('success', "User '{$deletedUserData['name']}' berhasil dihapus!");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
                'deleter_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }

    /**
     * Bulk delete multiple users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'exists:users,id'
            ], [
                'user_ids.required' => 'Pilih minimal satu user untuk dihapus.',
                'user_ids.min' => 'Pilih minimal satu user untuk dihapus.',
                'user_ids.*.exists' => 'User yang dipilih tidak valid.'
            ]);

            $userIds = $validated['user_ids'];

            // Remove current user from bulk delete
            $userIds = array_filter($userIds, function($id) {
                return $id != Auth::id();
            });

            if (empty($userIds)) {
                return redirect()->route('users.index')
                               ->with('error', 'Tidak ada user yang dapat dihapus! Anda tidak dapat menghapus akun sendiri.');
            }

            DB::beginTransaction();

            // Get users data before deletion for logging
            $usersToDelete = User::whereIn('id', $userIds)->get();
            $deletedUsersData = $usersToDelete->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar
                ];
            })->toArray();

            // Delete avatars
            foreach ($usersToDelete as $user) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    try {
                        Storage::disk('public')->delete($user->avatar);
                    } catch (\Exception $e) {
                        Log::warning('Failed to delete user avatar during bulk delete', [
                            'error' => $e->getMessage(),
                            'user_id' => $user->id,
                            'avatar_path' => $user->avatar
                        ]);
                        // Continue with deletion even if avatar deletion fails
                    }
                }
            }

            // Delete users
            $deletedCount = User::whereIn('id', $userIds)->delete();

            DB::commit();

            // Log successful bulk deletion
            Log::info('Bulk user deletion completed', [
                'deleted_users_data' => $deletedUsersData,
                'deleted_count' => $deletedCount,
                'requested_ids' => $userIds,
                'deleted_by_user_id' => Auth::id(),
                'deleted_by_user_name' => Auth::user()->name,
                'ip_address' => $request->ip(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('success', "{$deletedCount} user berhasil dihapus!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error during bulk user deletion', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'deleter_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat menghapus user. Silakan coba lagi.');
        }
    }

    /**
     * Export users data to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = User::query();

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            $users = $query->latest()->get();

            $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, ['ID', 'Nama', 'Email', 'Role', 'Tanggal Bergabung', 'Terakhir Update']);

                // CSV data
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        ucfirst($user->role),
                        $user->created_at->format('d/m/Y H:i'),
                        $user->updated_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            // Log export activity
            Log::info('Users data exported', [
                'exported_by_user_id' => Auth::id(),
                'exported_by_user_name' => Auth::user()->name,
                'total_exported' => $users->count(),
                'filters' => [
                    'search' => $request->search,
                    'role' => $request->role
                ],
                'ip_address' => $request->ip(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting users data', [
                'error' => $e->getMessage(),
                'exporter_id' => Auth::id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return redirect()->route('users.index')
                           ->with('error', 'Terjadi kesalahan saat mengexport data user.');
        }
    }
}
