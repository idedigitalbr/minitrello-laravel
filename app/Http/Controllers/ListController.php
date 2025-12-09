<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\TaskList;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxPosition = $board->lists()->max('position') ?? -1;

        $board->lists()->create([
            'name' => $request->name,
            'position' => $maxPosition + 1,
        ]);

        return redirect()->back()->with('success', 'Lista criada com sucesso!');
    }

    public function update(Request $request, TaskList $list)
    {
        $this->authorize('update', $list->board);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $list->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Lista atualizada!');
    }

    public function updatePositions(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'positions' => 'required|array',
            'positions.*' => 'integer|exists:task_lists,id',
        ]);

        foreach ($request->positions as $position => $listId) {
            TaskList::where('id', $listId)->update(['position' => $position]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(TaskList $list)
    {
        $this->authorize('update', $list->board);
        $list->delete();
        return redirect()->back()->with('success', 'Lista excluída!');
    }
}
