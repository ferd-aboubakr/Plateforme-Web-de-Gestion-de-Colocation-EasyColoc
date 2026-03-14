<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
            'expense_date' => 'required|date|before_or_equal:today',
            'category_id'  => 'nullable|exists:categories,id',
            'paid_by'      => 'required|exists:users,id',
        ]);

        // Vérifier que le payeur est bien membre actif
        $isMember = $colocation->members()->where('user_id', $request->paid_by)->exists();
        if (!$isMember) {
            return back()->withErrors(['paid_by' => 'Le payeur doit être membre actif de la colocation.']);
        }

        $colocation->expenses()->create($request->only([
            'title', 'amount', 'expense_date', 'category_id', 'paid_by',
        ]));

        return back()->with('success', 'Dépense ajoutée avec succès.');
    }

    public function destroy(Expense $expense)
    {
        $colocation = $expense->colocation;

        // Seul le payeur ou l'owner peut supprimer
        if (Auth::id() !== $expense->paid_by && Auth::id() !== $colocation->owner_id) {
            abort(403, 'Vous ne pouvez pas supprimer cette dépense.');
        }

        $expense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }
}



