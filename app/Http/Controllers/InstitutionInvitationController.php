<?php

namespace App\Http\Controllers;

use App\Models\Admin\InstitutionInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class InstitutionInvitationController extends Controller
{
    public function accept(string $token): RedirectResponse
    {
        $invitation = InstitutionInvitation::with(['institution', 'invitee'])
            ->where('token', $token)
            ->firstOrFail();

        if (! $invitation->isPending()) {
            $message = $invitation->status === 'accepted'
                ? 'This invitation has already been accepted.'
                : 'This invitation is no longer active.';

            return redirect()->route('login')->with('status', $message);
        }

        try {
            $membership = $invitation->accept($invitation->invitee);
            Auth::login($membership->user);

            return redirect()
                ->route('institution.portal')
                ->with('message', 'Invitation accepted. You can now manage ' . $invitation->institution->name . '.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->with('error', $e->getMessage());
        }
    }
}
