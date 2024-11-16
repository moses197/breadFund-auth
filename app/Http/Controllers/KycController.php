<?php

namespace App\Http\Controllers;

use App\Models\Kyc;
use App\Models\User;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            // 'firstname' => $user->firstname,
            // 'lastname' => $user->lastname,
            'middlename' => 'required',
            // 'nin' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'photo' => 'nullable|file|mimes:jpg,png|max:2048',
        ]);

        $kycData = [
            'user_id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'middlename' => $validated['middlename'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'photo' => $validated['phote'] ?? null,
        ];

        Kyc::upsert();
    }
}
