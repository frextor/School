<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Portage de `Roles.php` / `Roles_model.php` (CodeIgniter).
 *
 * Différences assumées par rapport au legacy :
 * - la liste ne repasse pas par le contrat JSON DataTables (serveur-side
 *   processing) : on utilise la pagination Eloquent classique, le futur
 *   front Laravel n'étant pas encore défini.
 * - `nom_machine` est généré avec Str::slug() plutôt que
 *   `url_title(convert_accented_characters())`.
 */
class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::query()
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_role', 'like', "%{$terme}%")
                    ->orWhere('nom_machine', 'like', "%{$terme}%");
            })
            ->withCount('permissions')
            ->orderBy('nom_role')
            ->paginate(25)
            ->withQueryString();

        return view('roles.index', ['roles' => $roles]);
    }

    public function create(): View
    {
        return view('roles.create', ['permissions' => Permission::orderBy('nom_permission')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_role' => ['required', 'string', 'max:100', 'unique:amos_roles,nom_role'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:amos_permissions,id_permission'],
        ]);

        $role = DB::transaction(function () use ($data) {
            $role = Role::create([
                'nom_role' => $data['nom_role'],
                'nom_machine' => Str::lower(Str::slug($data['nom_role'])),
                'date_creation' => now(),
                'date_modification' => now(),
                'locked' => 0,
            ]);

            $role->permissions()->sync($data['permissions'] ?? []);

            return $role;
        });

        return redirect()
            ->route('roles.index')
            ->with('status', "Rôle « {$role->nom_role} » créé avec succès.");
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('roles.edit', [
            'role' => $role,
            'permissions' => Permission::orderBy('nom_permission')->get(),
            'selectedPermissions' => $role->permissions->pluck('id_permission')->all(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'nom_role' => ['required', 'string', 'max:100', 'unique:amos_roles,nom_role,'.$role->id_role.',id_role'],
            'permissions' => ['array'],
            'permissions.*' => ['integer', 'exists:amos_permissions,id_permission'],
        ]);

        DB::transaction(function () use ($data, $role) {
            $ancienNomMachine = $role->nom_machine;
            $nouveauNomMachine = Str::lower(Str::slug($data['nom_role']));

            $role->update([
                'nom_role' => $data['nom_role'],
                'nom_machine' => $nouveauNomMachine,
                'date_modification' => now(),
            ]);

            // Les admins sont rattachés par le champ texte `profil` (legacy) : on le garde synchronisé.
            Admin::where('profil', $ancienNomMachine)->update(['profil' => $nouveauNomMachine]);

            $role->permissions()->sync($data['permissions'] ?? []);
        });

        return redirect()
            ->route('roles.index')
            ->with('status', "Rôle « {$role->nom_role} » mis à jour.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->permissions()->detach();
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('status', 'Rôle supprimé.');
    }
}
