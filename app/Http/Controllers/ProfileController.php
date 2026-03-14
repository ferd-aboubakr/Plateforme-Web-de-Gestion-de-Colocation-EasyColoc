<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Membership;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $section = $request->get('section', 'info');
        $user = $request->user();
        
        $data = [
            'user' => $user,
            'current_section' => $section,
        ];
        
        // Ajouter les données spécifiques selon la section
        if ($section === 'colocations') {
            $data['memberships'] = $user->memberships()->with('colocation.owner')->latest()->get();
            $data['activeMembership'] = $user->activeMembership;
        }
        
        if ($section === 'expenses') {
            $membership = $user->activeMembership;
            if ($membership) {
                $data['expenses'] = Expense::where('paid_by', $user->id)
                    ->with(['category', 'colocation'])
                    ->orderBy('expense_date', 'desc')
                    ->paginate(10);
            } else {
                $data['expenses'] = new \Illuminate\Pagination\LengthAwarePaginator(
                    [], 
                    0, 
                    10, 
                    1, 
                    ['path' => request()->url(), 'pageName' => 'page']
                );
            }
        }
        
        if ($section === 'activity') {
            $data['recentActivities'] = $this->getUserActivities($user);
        }
        
        return view('profile.edit', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
    /**
     * Get user activities for profile section
     */
    private function getUserActivities(User $user): array
    {
        $activities = [];
        
        // Recent expenses
        $expenses = Expense::where('paid_by', $user->id)
            ->with('colocation')
            ->orderBy('expense_date', 'desc')
            ->take(5)
            ->get();
            
        foreach ($expenses as $expense) {
            $activities[] = [
                'type' => 'expense',
                'description' => 'A payé ' . number_format($expense->amount, 2) . '€ pour "' . $expense->title . '"',
                'colocation' => $expense->colocation->name,
                'date' => $expense->expense_date->format('d/m/Y H:i'),
                'icon' => '💰'
            ];
        }
        
        // Recent memberships
        $memberships = $user->memberships()
            ->with('colocation')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        foreach ($memberships as $membership) {
            if ($membership->left_at) {
                $activities[] = [
                    'type' => 'left',
                    'description' => 'A quitté la colocation',
                    'colocation' => $membership->colocation->name,
                    'date' => $membership->left_at->format('d/m/Y H:i'),
                    'icon' => '🚪'
                ];
            } else {
                $activities[] = [
                    'type' => 'joined',
                    'description' => 'A rejoint la colocation',
                    'colocation' => $membership->colocation->name,
                    'date' => $membership->joined_at->format('d/m/Y H:i'),
                    'icon' => '🏠'
                ];
            }
        }
        
        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activities, 0, 10);
    }
}
