<?php

namespace App\Http\Controllers;

use App\Models\Admin\InstitutionInvitation;
use Illuminate\Http\Request;

class InstitutionInvitationController extends Controller
{
    public function accept(Request $request, string $token)
    {
        if (!$request->user()) {
            return redirect()->guest(route('login'));
        }
        $invitation = $this->invitationFor($request, $token);

        return view('institution.invitation', ['invitation' => $invitation]);
    }

    public function confirm(Request $request, string $token)
    {
        abort_unless($request->user(), 403);
        $invitation = $this->invitationFor($request, $token);
        $invitation->accept($request->user());

        return redirect()->route('dashboard')->with('message', 'Your institution membership is active.');
    }

    private function invitationFor(Request $request, string $token): InstitutionInvitation
    {
        $invitation = InstitutionInvitation::with('institution')->where('token', $token)->firstOrFail();
        abort_unless((int) $invitation->user_id === (int) $request->user()->id
            && strcasecmp($invitation->email, $request->user()->email) === 0, 403);
        abort_unless($invitation->isPending(), 410, 'This invitation has expired or is no longer pending.');

        return $invitation;
    }
}
