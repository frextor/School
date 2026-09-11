<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Permissions.php` / `Permissions_model.php` (CodeIgniter).
 * Voir RoleController pour les décisions de portage communes (pagination
 * standard plutôt que contrat JSON DataTables).
 */
class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        $permissions = Permission::query()
            ->with('parent')
            ->when($request->filled('recherche'), function ($q) use ($request) {
                $terme = $request->string('recherche');
                $q->where('nom_permission', 'like', "%{$terme}%")
                    ->orWhere('route', 'like', "%{$terme}%");
            })
            ->orderBy('nom_permission')
            ->paginate(25)
            ->withQueryString();

        return view('permissions.index', ['permissions' => $permissions]);
    }

    public function create(): View
    {
        return view('permissions.create', [
            'permissionsParentes' => Permission::orderBy('nom_permission')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom_permission' => ['required', 'string', 'max:100', 'unique:amos_permissions,nom_permission'],
            'route' => ['required', 'string'],
            'ParentID' => ['nullable', 'integer', 'exists:amos_permissions,id_permission'],
        ]);

        $permission = Permission::create([
            'nom_permission' => $data['nom_permission'],
            'route' => $data['route'],
            'ParentID' => $data['ParentID'] ?? null,
            'date_creation' => now(),
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('status', "Permission « {$permission->nom_permission} » créée avec succès.");
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', [
            'permission' => $permission,
            'permissionsParentes' => Permission::where('id_permission', '!=', $permission->id_permission)
                ->orderBy('nom_permission')
                ->get(),
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $data = $request->validate([
            'nom_permission' => ['required', 'string', 'max:100', 'unique:amos_permissions,nom_permission,'.$permission->id_permission.',id_permission'],
            'route' => ['required', 'string'],
            'ParentID' => ['nullable', 'integer', 'exists:amos_permissions,id_permission'],
        ]);

        $permission->update([
            'nom_permission' => $data['nom_permission'],
            'route' => $data['route'],
            'ParentID' => $data['ParentID'] ?? null,
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('permissions.index')
            ->with('status', "Permission « {$permission->nom_permission} » mise à jour.");
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()
            ->route('permissions.index')
            ->with('status', 'Permission supprimée.');
    }
}
