<?php

namespace App\Http\Controllers;

use App\Models\Commerce\CommercialDocument;
use Illuminate\Http\Request;

class CommercialDocumentController extends Controller
{
    public function show(Request $request, CommercialDocument $commercialDocument)
    {
        $user = $request->user();

        abort_unless(
            $user
            && (
                (int) $commercialDocument->user_id === (int) $user->id
                || $user->isSuperAdmin()
                || $user->isAcademyAdmin()
            ),
            403
        );

        return view('commercial.documents.show', [
            'document' => $commercialDocument->load(['user', 'paystackTransaction', 'marketplaceOrder', 'walletTransaction']),
        ]);
    }
}
