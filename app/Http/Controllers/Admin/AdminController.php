<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->get('section', 'statistics');
        
        $data = [
            'stats' => [
                'total_users'    => User::count(),
                'active_colocs'  => Colocation::where('status', 'active')->count(),
                'expense_total'  => Expense::sum('amount'),
                'banned_count'   => User::whereNotNull('banned_at')->count(),
            ],
            'users'       => User::with('roles')->orderBy('created_at', 'desc')->paginate(15),
            'colocations' => Colocation::with('owner')->withCount('members')->latest()->paginate(10),
            'banned_users' => User::whereNotNull('banned_at')->orderBy('banned_at', 'desc')->paginate(15),
            'current_section' => $section,
        ];

        return view('admin.index', $data);
    }

    public function ban(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous bannir vous-même.']);
        }

        $user->update(['banned_at' => now()]);

        return back()->with('success', $user->name . ' a été banni.');
    }

    public function unban(User $user)
    {
        $user->update(['banned_at' => null]);

        return back()->with('success', $user->name . ' a été débanni.');
    }
}
