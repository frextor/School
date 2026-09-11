<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Etablissement;
use App\Models\Role;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Portage de la gestion des comptes administrateurs dans `Configuration.php`
 * (admins()) + `Admin.php` (add_admin / valide_update_admin_user / delete_admin).
 */
class AdminController extends Controller
{
    public function index(): View
    {
        $admins = Admin::with(['service', 'etablissements'])->orderBy('nom')->paginate(25);

        return view('admins.index', ['admins' => $admins]);
    }

    public function create(): View
    {
        return view('admins.create', [
            'roles' => Role::orderBy('nom_role')->get(),
            'services' => Service::orderBy('libelle')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerDonnees($request, mdpObligatoire: true);

        $admin = DB::transaction(function () use ($data, $request) {
            $admin = Admin::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => md5($data['password']),
                'avatar' => $this->stockerAvatar($request) ?? 'userprofile.png',
                'profil' => $data['profil'],
                'id_service' => $data['id_service'],
                'token' => '',
            ]);

            $this->synchroniserEtablissements($admin, $data);

            return $admin;
        });

        return redirect()
            ->route('admins.index')
            ->with('status', "Administrateur « {$admin->username} » créé avec succès.");
    }

    public function edit(Admin $admin): View
    {
        $admin->load('etablissements');

        return view('admins.edit', [
            'admin' => $admin,
            'roles' => Role::orderBy('nom_role')->get(),
            'services' => Service::orderBy('libelle')->get(),
            'etablissements' => Etablissement::orderBy('nom_etablissement')->get(),
        ]);
    }

    public function update(Request $request, Admin $admin): RedirectResponse
    {
        $data = $this->validerDonnees($request, mdpObligatoire: false, admin: $admin);

        DB::transaction(function () use ($data, $admin, $request) {
            $admin->update([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'] ? md5($data['password']) : $admin->password,
                'avatar' => $this->stockerAvatar($request) ?? $admin->avatar,
                'profil' => $data['profil'],
                'id_service' => $data['id_service'],
            ]);

            $this->synchroniserEtablissements($admin, $data);
        });

        return redirect()
            ->route('admins.index')
            ->with('status', "Administrateur « {$admin->username} » mis à jour.");
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        DB::transaction(function () use ($admin) {
            $admin->etablissements()->detach();
            $admin->delete();
        });

        return redirect()
            ->route('admins.index')
            ->with('status', 'Administrateur supprimé.');
    }

    private function validerDonnees(Request $request, bool $mdpObligatoire, ?Admin $admin = null): array
    {
        $idAdmin = $admin?->id_admin;

        return $request->validate([
            'nom' => ['required', 'string', 'max:30'],
            'prenom' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:20', 'unique:amos_admins,username,'.$idAdmin.',id_admin'],
            'email' => ['required', 'email', 'max:100'],
            'password' => [$mdpObligatoire ? 'required' : 'nullable', 'string', 'min:6'],
            'profil' => ['required', 'string', 'max:50'],
            'id_service' => ['required', 'integer', 'exists:amos_services,id_service'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'etablissements' => ['array'],
            'etablissements.*' => ['integer', 'exists:amos_etablissement,id_etablissement'],
            'etablissement_principal' => ['nullable', 'integer'],
        ]);
    }

    /** La colonne `avatar` (varchar(30), héritée du legacy) impose un nom de fichier court. */
    private function stockerAvatar(Request $request): ?string
    {
        if (! $request->hasFile('avatar')) {
            return null;
        }

        $fichier = $request->file('avatar');
        $nom = Str::random(20).'.'.$fichier->getClientOriginalExtension();
        $fichier->storeAs('avatars_admins', $nom, 'public');

        return $nom;
    }

    private function synchroniserEtablissements(Admin $admin, array $data): void
    {
        $etablissements = $data['etablissements'] ?? [];
        $principal = $data['etablissement_principal'] ?? null;

        $pivotData = collect($etablissements)->mapWithKeys(fn ($id) => [
            $id => ['etablissement_principal' => $principal == $id ? 1 : 0],
        ])->all();

        $admin->etablissements()->sync($pivotData);
    }
}
