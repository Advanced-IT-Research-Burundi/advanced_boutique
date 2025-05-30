<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['company', 'agency', 'createdBy']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtres
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Données pour les filtres
        $companies = Company::all();
        $agencies = Agency::all();
        $creators = User::whereHas('created_by')->distinct()->get();
        $roles = ['admin', 'manager', 'salesperson', 'employee'];
        $statuses = ['active', 'inactive', 'suspended'];

        return view('user.index', compact('users', 'companies', 'agencies', 'creators', 'roles', 'statuses'));
    }

    public function create()
    {
        $companies = Company::all();
        $agencies = Agency::all();
        $roles = ['admin', 'manager', 'salesperson', 'employee'];

        return view('users.create', compact('companies', 'agencies', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,manager,salesperson,employee',
            'company_id' => 'nullable|exists:companies,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $user->load(['company', 'agency', 'createdBy']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $companies = Company::all();
        $agencies = Agency::all();
        $roles = ['admin', 'manager', 'salesperson', 'employee'];

        return view('users.edit', compact('user', 'companies', 'agencies', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,manager,salesperson,employee',
            'company_id' => 'nullable|exists:companies,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
