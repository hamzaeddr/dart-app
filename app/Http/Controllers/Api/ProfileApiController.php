<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $profile = $user->profile;

        $avatarUrl = $profile?->getFirstMediaUrl('avatar') ?: null;
        $revolutQrUrl = $profile?->getFirstMediaUrl('revolut_qr') ?: null;

        $shareUrl = route('darets.index', ['ref' => $user->id]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $profile?->phone,
            'city' => $profile?->city,
            'bio' => $profile?->bio,
            'avatar_url' => $avatarUrl,
            'revolut_qr_url' => $revolutQrUrl,
            'share_url' => $shareUrl,
        ]);
    }
}
