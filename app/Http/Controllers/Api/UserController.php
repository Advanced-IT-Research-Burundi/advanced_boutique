<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends \App\Http\Controllers\Controller
{
    /**
     * Liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->with(['company', 'agency']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->input('agency_id'));
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        $users = $query->orderByDesc('created_at')->paginate(15);

        return sendResponse($users, 'Liste des utilisateurs récupérée avec succès');
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:active,inactive,banned',
            'role' => 'nullable|in:admin,salesperson,manager,viewer',
            'permissions' => 'nullable|json',
            'company_id' => 'nullable|exists:companies,id',
            'agency_id' => 'nullable|exists:agencies,id',
            'created_by' => 'nullable|exists:users,id',
            'must_change_password' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ]);

        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }

        $data = $validator->validated();

        // Gestion de l'image
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        // Hachage du mot de passe
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return sendResponse($this->formatUser($user), 'Utilisateur créé avec succès', 201);
    }

    /**
     * Afficher un utilisateur
     */
    public function show(User $user)
    {
        return sendResponse($this->formatUser($user->load(['company', 'agency', 'creator'])), 'Détail de l\'utilisateur récupéré avec succès');
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'profile_photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive,banned',
            'role' => 'sometimes|in:admin,salesperson,manager,viewer',
            'permissions' => 'sometimes|nullable|json',
            'company_id' => 'sometimes|nullable|exists:companies,id',
            'agency_id' => 'sometimes|nullable|exists:agencies,id',
            'created_by' => 'sometimes|nullable|exists:users,id',
            'must_change_password' => 'sometimes|boolean',
            'two_factor_enabled' => 'sometimes|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return sendError('Erreur de validation', 422, $validator->errors());
        }

        $data = $validator->validated();

        // Gestion de l'image
        if ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        // Gestion du mot de passe
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return sendResponse($user, 'Utilisateur mis à jour avec succès');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Supprimer l'image de profil si elle existe
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        return sendResponse(null, 'Utilisateur supprimé avec succès');
    }
}
