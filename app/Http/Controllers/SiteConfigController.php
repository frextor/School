<?php

namespace App\Http\Controllers;

use App\Models\SiteConstant;
use App\Models\TypeAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Portage de `Configuration::site()` — constantes du site (clé/valeur) et
 * activation/désactivation de sections (`activate()`).
 */
class SiteConfigController extends Controller
{
    public function index(): View
    {
        return view('configuration.site', [
            'constants' => SiteConstant::orderBy('label')->get(),
            'accesses' => TypeAccess::with('enfants')
                ->where(fn ($q) => $q->whereNull('id_parent')->orWhere('id_parent', 0))
                ->get(),
        ]);
    }

    public function updateConstant(Request $request, SiteConstant $constant): RedirectResponse
    {
        $data = $request->validate(['value' => ['required', 'string', 'max:150']]);

        $constant->update($data);

        return redirect()->route('configuration.site')->with('status', "Constante « {$constant->label} » mise à jour.");
    }

    /** Portage de `activate($id_access, $status)`. */
    public function toggleAccess(TypeAccess $access): JsonResponse
    {
        $access->update(['status' => ! $access->status]);

        return response()->json('ok');
    }
}
