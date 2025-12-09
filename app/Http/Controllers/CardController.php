<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\TaskList;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function store(Request $request, TaskList $list)
    {
        $this->authorize('update', $list->board);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'due_date' => 'nullable|date',
        ]);

        $maxPosition = $list->cards()->max('position') ?? -1;

        $list->cards()->create([
            'title' => $request->title,
            'description' => $request->description,
            'color' => $request->color,
            'due_date' => $request->due_date,
            'position' => $maxPosition + 1,
        ]);

        return redirect()->back()->with('success', 'Card criado com sucesso!');
    }

    public function show(Card $card)
    {
        $this->authorize('update', $card->taskList->board);
        return response()->json($card);
    }

    public function update(Request $request, Card $card)
    {
        $this->authorize('update', $card->taskList->board);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'due_date' => 'nullable|date',
        ]);

        $card->update($request->only('title', 'description', 'color', 'due_date'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'card' => $card]);
        }

        return redirect()->back()->with('success', 'Card atualizado!');
    }

    public function move(Request $request, Card $card)
    {
        $this->authorize('update', $card->taskList->board);

        $request->validate([
            'task_list_id' => 'required|exists:task_lists,id',
            'position' => 'required|integer|min:0',
        ]);

        $oldListId = $card->task_list_id;
        $oldPosition = $card->position;
        $newListId = $request->task_list_id;
        $newPosition = $request->position;

        if ($oldListId == $newListId) {
            // Mesma lista
            if ($oldPosition == $newPosition) {
                return response()->json(['success' => true]);
            }

            if ($newPosition < $oldPosition) {
                // Movendo para cima: incrementa posições intermediárias
                Card::where('task_list_id', $oldListId)
                    ->where('id', '!=', $card->id)
                    ->whereBetween('position', [$newPosition, $oldPosition - 1])
                    ->increment('position');
            } else {
                // Movendo para baixo: decrementa posições intermediárias
                Card::where('task_list_id', $oldListId)
                    ->where('id', '!=', $card->id)
                    ->whereBetween('position', [$oldPosition + 1, $newPosition])
                    ->decrement('position');
            }
        } else {
            // Listas diferentes

            // 1. Ajustar lista antiga (fechar buraco)
            Card::where('task_list_id', $oldListId)
                ->where('position', '>', $oldPosition)
                ->decrement('position');

            // 2. Ajustar nova lista (abrir espaço)
            Card::where('task_list_id', $newListId)
                ->where('position', '>=', $newPosition)
                ->increment('position');
        }

        // Atualizar card
        $card->update([
            'task_list_id' => $newListId,
            'position' => $newPosition,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Card $card)
    {
        $this->authorize('update', $card->taskList->board);
        $card->delete();
        return redirect()->back()->with('success', 'Card excluído!');
    }
}
