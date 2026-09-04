<?php

namespace App\Http\Controllers\Admin\Loyalty;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Loyalty\UpdateLoyaltySettingsRequest;
use App\Services\LoyaltySettingsService;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function update(UpdateLoyaltySettingsRequest $request, LoyaltySettingsService $settings): RedirectResponse
    {
        $data = $request->validated();
        $data['enabled'] = $request->boolean('enabled') ? '1' : '0';

        $settings->update($data);

        return redirect()->route('admin.loyalty.index', ['tab' => 'settings'])->with('success', 'Paramètres mis à jour avec succès.');
    }
}
