<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('manage', User::class);

        return view('users.index', ['users' => User::query()->latest()->paginate(30)]);
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $this->authorize('manage', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['required', 'string', 'min:16', 'max:255'],
        ]);

        $user = User::create($validated);
        $logger->log('user_created', $request->user(), null, $user, ['role' => $user->role->value], $request);

        return redirect()->route('users.index');
    }

    public function update(User $user, Request $request, ActivityLogger $logger)
    {
        $this->authorize('manage', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'email_verified_at' => $validated['is_active'] ? ($user->email_verified_at ?? now()) : null,
        ])->save();

        $logger->log('user_updated', $request->user(), null, $user, [
            'role' => $user->role->value,
            'is_active' => (bool) $validated['is_active'],
        ], $request);

        return redirect()->route('users.index');
    }

    public function destroy(User $user, Request $request, ActivityLogger $logger)
    {
        $this->authorize('manage', User::class);

        abort_if($user->is($request->user()), 422, '自分自身の権限は削除できません。');

        $logger->log('user_access_removed', $request->user(), null, $user, ['email' => $user->email], $request);
        $user->forceFill(['email_verified_at' => null])->save();

        return redirect()->route('users.index');
    }
}
