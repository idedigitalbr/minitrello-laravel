@extends('layouts.trello')

@section('title', $board->name)

@section('styles')
    <style>
        body {
            background:
                {{ $board->color }}
                !important;
        }
    </style>
@endsection

@section('content')
    <div class="board-container">
        <!-- Board Header -->
        <div class="d-flex align-items-center justify-content-between px-4 py-2" style="background: rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="text-white mb-0">{{ $board->name }}</h4>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editBoardModal">
                    <i class="bi bi-gear"></i>
                </button>
                <form action="{{ route('boards.destroy', $board) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Tem certeza que deseja excluir este quadro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Board Lists -->
        <div class="board-lists" id="boardLists">
            @foreach($board->lists as $list)
                <div class="task-list" data-list-id="{{ $list->id }}">
                    <div class="task-list-header">
                        <span class="list-title">{{ $list->name }}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"
                                        onclick="editList({{ $list->id }}, '{{ $list->name }}')"><i
                                            class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('lists.destroy', $list) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                            onclick="return confirm('Excluir esta lista e todos os cards?')">
                                            <i class="bi bi-trash me-2"></i>Excluir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="task-list-cards" data-list-id="{{ $list->id }}">
                        @foreach($list->cards as $card)
                            <div class="card-item" data-card-id="{{ $card->id }}" onclick="openCard({{ $card->id }})">
                                @if($card->color)
                                    <div class="rounded mb-2" style="height: 8px; background: {{ $card->color }};"></div>
                                @endif
                                <div class="card-title">{{ $card->title }}</div>
                                @if($card->description)
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-text-left"></i>
                                    </div>
                                @endif
                                @if($card->due_date)
                                    @php
                                        $isOverdue = $card->due_date->isPast();
                                        $isSoon = $card->due_date->isToday() || $card->due_date->isTomorrow();
                                    @endphp
                                    <div class="mt-2">
                                        <span
                                            class="due-date-badge {{ $isOverdue ? 'due-date-overdue' : ($isSoon ? 'due-date-soon' : 'due-date-normal') }}">
                                            <i class="bi bi-clock me-1"></i>{{ $card->due_date->format('d/m') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Card Form -->
                    <div class="add-card-form">
                        <form action="{{ route('cards.store', $list) }}" method="POST" class="add-card-inline"
                            style="display: none;">
                            @csrf
                            <textarea name="title" class="form-control mb-2" rows="2"
                                placeholder="Digite um título para este cartão..." required></textarea>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Adicionar</button>
                                <button type="button" class="btn btn-link text-muted" onclick="hideAddCard(this)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </form>
                        <button class="btn btn-link text-muted w-100 text-start add-card-btn" onclick="showAddCard(this)">
                            <i class="bi bi-plus me-1"></i>Adicionar cartão
                        </button>
                    </div>
                </div>
            @endforeach

        </div>

        <!-- Add List Button (Outside Sortable) -->
        <div class="px-2">
            <button class="add-list-btn" id="addListBtn" onclick="showAddList()">
                <i class="bi bi-plus me-1"></i>Adicionar lista
            </button>
            <div class="task-list" id="addListForm" style="display: none;">
                <form action="{{ route('lists.store', $board) }}" method="POST" class="p-3">
                    @csrf
                    <input type="text" name="name" class="form-control mb-2" placeholder="Nome da lista..." required>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Adicionar</button>
                        <button type="button" class="btn btn-link text-muted" onclick="hideAddList()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Board -->
    <div class="modal fade" id="editBoardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('boards.update', $board) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Quadro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome do Quadro *</label>
                            <input type="text" name="name" class="form-control" required value="{{ $board->name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" rows="2">{{ $board->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cor do Quadro</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @php
                                    $colors = ['#0079bf', '#d29034', '#519839', '#b04632', '#89609e', '#cd5a91', '#4bbf6b', '#00aecc', '#838c91'];
                                @endphp
                                @foreach($colors as $i => $color)
                                    <input type="radio" name="color" id="editColor{{ $i }}" value="{{ $color }}" class="d-none"
                                        {{ $board->color === $color ? 'checked' : '' }}>
                                    <label for="editColor{{ $i }}"
                                        class="color-option {{ $board->color === $color ? 'selected' : '' }}"
                                        style="background: {{ $color }};" onclick="selectColor(this)"></label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Card -->
    <div class="modal fade" id="cardModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="cardForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Cartão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" name="title" id="cardTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" id="cardDescription" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Entrega</label>
                                <input type="date" name="due_date" id="cardDueDate" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cor</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <input type="radio" name="color" id="cardColorNone" value="" class="d-none" checked>
                                    <label for="cardColorNone" class="color-option selected" style="background: #dfe1e6;"
                                        onclick="selectColor(this)">
                                        <i class="bi bi-x"></i>
                                    </label>
                                    @foreach(['#61bd4f', '#f2d600', '#ff9f1a', '#eb5a46', '#c377e0', '#00c2e0'] as $i => $color)
                                        <input type="radio" name="color" id="cardColor{{ $i }}" value="{{ $color }}"
                                            class="d-none">
                                        <label for="cardColor{{ $i }}" class="color-option" style="background: {{ $color }};"
                                            onclick="selectColor(this)"></label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" onclick="deleteCard()">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form oculto para deletar card -->
    <form id="deleteCardForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    <script>
        // Card data storage
        const cards = @json($board->lists->flatMap->cards->keyBy('id'));
        let currentCardId = null;

        // Color selection
        function selectColor(el) {
            el.closest('.d-flex').querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
            el.classList.add('selected');
        }

        // Add Card functions
        function showAddCard(btn) {
            const form = btn.previousElementSibling;
            form.style.display = 'block';
            btn.style.display = 'none';
            form.querySelector('textarea').focus();
        }

        function hideAddCard(btn) {
            const form = btn.closest('form');
            form.style.display = 'none';
            form.nextElementSibling.style.display = 'block';
        }

        // Add List functions
        function showAddList() {
            document.getElementById('addListBtn').style.display = 'none';
            document.getElementById('addListForm').style.display = 'block';
            document.getElementById('addListForm').querySelector('input').focus();
        }

        function hideAddList() {
            document.getElementById('addListBtn').style.display = 'block';
            document.getElementById('addListForm').style.display = 'none';
        }

        // Edit List
        function editList(listId, name) {
            const newName = prompt('Nome da lista:', name);
            if (newName && newName !== name) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/lists/${listId}`;
                form.innerHTML = `
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="${newName}">
                    `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Card Modal
        async function openCard(cardId) {
            currentCardId = cardId;

            // Show loading state or clear fields
            document.getElementById('cardTitle').value = 'Carregando...';

            try {
                const response = await fetch(`/cards/${cardId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Falha ao carregar');

                const card = await response.json();

                document.getElementById('cardForm').action = `/cards/${cardId}`;
                document.getElementById('cardTitle').value = card.title;
                document.getElementById('cardDescription').value = card.description || '';
                document.getElementById('cardDueDate').value = card.due_date ? card.due_date.split('T')[0] : '';

                // Reset color selection
                document.querySelectorAll('#cardModal .color-option').forEach(opt => opt.classList.remove('selected'));
                if (card.color) {
                    const colorInput = document.querySelector(`#cardModal input[value="${card.color}"]`);
                    if (colorInput) {
                        colorInput.checked = true;
                        colorInput.nextElementSibling.classList.add('selected');
                    }
                } else {
                    document.getElementById('cardColorNone').checked = true;
                    document.querySelector('label[for="cardColorNone"]').classList.add('selected');
                }

                new bootstrap.Modal(document.getElementById('cardModal')).show();
            } catch (error) {
                console.error(error);
                alert('Erro ao carregar dados do cartão.');
            }
        }

        function deleteCard() {
            if (confirm('Tem certeza que deseja excluir este cartão?')) {
                const form = document.getElementById('deleteCardForm');
                form.action = `/cards/${currentCardId}`;
                form.submit();
            }
        }

        // Initialize Sortable for drag and drop
        document.addEventListener('DOMContentLoaded', function () {
            // Make cards sortable within and between lists
            document.querySelectorAll('.task-list-cards').forEach(list => {
                new Sortable(list, {
                    group: 'cards',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const cardId = evt.item.dataset.cardId;
                        const newListId = evt.to.dataset.listId;
                        const newPosition = evt.newIndex;

                        fetch(`/cards/${cardId}/move`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                task_list_id: newListId,
                                position: newPosition
                            })
                        });
                    }
                });
            });

            // Make lists sortable
            new Sortable(document.getElementById('boardLists'), {
                animation: 150,
                handle: '.task-list-header',
                ghostClass: 'sortable-ghost',
                filter: '#addListBtn, #addListForm',
                onEnd: function (evt) {
                    const positions = [];
                    document.querySelectorAll('.task-list[data-list-id]').forEach((list, index) => {
                        positions.push(parseInt(list.dataset.listId));
                    });

                    fetch('{{ route("lists.positions", $board) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ positions })
                    });
                }
            });
        });
    </script>
@endsection