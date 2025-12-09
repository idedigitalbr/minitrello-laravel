<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Auth::user()->boards()->latest()->get();
        return view('dashboard', compact('boards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        Auth::user()->boards()->create($request->only('name', 'description', 'color'));

        return redirect()->route('dashboard')->with('success', 'Board criado com sucesso!');
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);
        $board->load(['lists.cards']);
        return view('boards.show', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $board->update($request->only('name', 'description', 'color'));

        return redirect()->back()->with('success', 'Board atualizado com sucesso!');
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);
        $board->delete();
        return redirect()->route('dashboard')->with('success', 'Board excluído com sucesso!');
    }
}
