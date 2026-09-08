<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Services\CredentialVault;
use Illuminate\Http\Request;

class SiteCredentialController extends Controller
{
    public function reveal(Credential $credential, Request $request, CredentialVault $vault)
    {
        $this->authorize('view', $credential);

        return response()->json($vault->reveal($credential, $request->user(), $request));
    }

    public function copy(Credential $credential, Request $request, CredentialVault $vault)
    {
        $this->authorize('view', $credential);
        $vault->recordCopy($credential, $request->user(), $request);

        return response()->noContent();
    }
}
