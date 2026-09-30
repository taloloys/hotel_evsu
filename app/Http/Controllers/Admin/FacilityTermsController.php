<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityTermsController extends Controller
{
    public function edit(): View
    {
        $content = SystemSetting::get('facility_terms_content', '');
        $updatedAt = SystemSetting::get('facility_terms_updated_at');

        return view('admin.facility-terms.edit', compact('content', 'updatedAt'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:20000'],
        ]);

        SystemSetting::set('facility_terms_content', $validated['content']);
        SystemSetting::set('facility_terms_updated_at', now()->toISOString());

        ActivityLog::log('FACILITY_TERMS_UPDATED', 'Updated facility booking Terms & Conditions.');

        return redirect()->route('admin.facility-terms.edit')->with('success', 'Terms & Conditions saved.');
    }
}
