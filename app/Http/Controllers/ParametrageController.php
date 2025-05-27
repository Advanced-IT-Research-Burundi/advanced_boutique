<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParametrageController extends Controller
{
    /**
     * Affiche la page des paramètres
     */
    public function index()
    {
        $company = Company::where('is_actif', true)->first();

        // Si l'entreprise n'existe pas, on crée une nouvelle instance vide
        if (!$company) {
            $company = new Company();
            $company->user_id = Auth::id();
            $company->is_actif = true;
            $company->save();
        }

        // Récupération des Agencies
        $agencies = Agency::with('stock')->get();

        // Récupération des catégories pour la pharmacie
        $categories = Category::all();

        // Récupération des utilisateurs pour l'onglet utilisateurs
        $users = User::all();

        // Liste des utilisateurs responsables de l'agence
        $usersList = User::whereIn('id', function($query) {
                        $query->select('manager_id')
                            ->from('agencies')
                            ->whereNotNull('manager_id');
                    })
                    ->get();


        return view('settings.parametre', compact(
            'company',
            'agencies',
            'categories',
            'users',
            'usersList',
        ));
    }

    /**
     * Met à jour les informations de l'entreprise
     */
    public function updateCompany(Request $request)
    {
        $company = Company::where('is_actif', true)->first();

        // Validation des données
        $validated = $request->validate([
            'tp_name' => 'required|string|max:250',
            'tp_type' => 'nullable|string|max:250',
            'tp_TIN' => 'nullable|string|max:250',
            'tp_trade_number' => 'nullable|string|max:250',
            'tp_postal_number' => 'nullable|string|max:250',
            'tp_phone_number' => 'nullable|string|max:250',
            'tp_email' => 'nullable|email|max:250',
            'tp_logo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Traitement du logo s'il est fourni
        if ($request->hasFile('tp_logo')) {
            // Suppression de l'ancien logo si existant
            if ($company->tp_logo) {
                Storage::delete($company->tp_logo);
            }

            // Stockage du nouveau logo
            $logoPath = $request->file('tp_logo')->store('logos', 'public');
            $company->tp_logo = $logoPath;
        }

        // Mise à jour des champs de l'entreprise
        $company->tp_name = $request->tp_name;
        $company->tp_type = $request->tp_type;
        $company->tp_TIN = $request->tp_TIN;
        $company->tp_trade_number = $request->tp_trade_number;
        $company->tp_postal_number = $request->tp_postal_number;
        $company->tp_phone_number = $request->tp_phone_number;
        $company->tp_email = $request->tp_email;
        $company->tp_website = $request->tp_website ? 'https://' . $request->tp_website : null;

        // Adresse
        $company->tp_address_privonce = $request->tp_address_province;
        $company->tp_address_commune = $request->tp_address_commune;
        $company->tp_address_quartier = $request->tp_address_quartier;
        $company->tp_address_avenue = $request->tp_address_avenue;
        $company->tp_address_rue = $request->tp_address_rue;
        $company->tp_address_number = $request->tp_address_number;

        // Informations fiscales
        $company->tp_fiscal_center = $request->tp_fiscal_center;
        $company->tp_activity_sector = $request->tp_activity_sector;
        $company->tp_legal_form = $request->tp_legal_form;
        $company->payment_type = $request->payment_type;
        $company->vat_taxpayer = $request->has('vat_taxpayer') ? '1' : '0';
        $company->ct_taxpayer = $request->has('ct_taxpayer') ? '1' : '0';
        $company->tl_taxpayer = $request->has('tl_taxpayer') ? '1' : '0';

        // Réseaux sociaux
        $company->tp_facebook = $request->tp_facebook;
        $company->tp_twitter = $request->tp_twitter;
        $company->tp_instagram = $request->tp_instagram;
        $company->tp_youtube = $request->tp_youtube;
        $company->tp_whatsapp = $request->tp_whatsapp;

        // Informations bancaires
        $company->tp_bank = $request->tp_bank;
        $company->tp_account_number = $request->tp_account_number;

        $company->save();

        return redirect()->route('parametres', ['tab' => 'company'])
            ->with('success', 'Informations de l\'entreprise mises à jour avec succès');
    }

}
