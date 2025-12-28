<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Daret;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $users = User::orderByDesc('created_at')->paginate(10);
        $darets = Daret::with('owner')->withCount('members')->orderByDesc('created_at')->paginate(10);
        $pendingContributions = Contribution::with(['daret.owner', 'user', 'cycle'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('admin.dashboard', [
            'users' => $users,
            'darets' => $darets,
            'pendingContributions' => $pendingContributions,
        ]);
    }
}
