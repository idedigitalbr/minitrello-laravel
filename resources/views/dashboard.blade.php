@extends('layouts.trello')

@section('title', 'Meus Quadros')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white mb-0">
                <i class="bi bi-grid-3x3-gap me-2"></i>Meus Quadros
            </h2>
            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#newBoardModal">
                <i class="bi bi-plus-lg me-1"></i>Novo Quadro
            </button>
        </div>

        <div class="row g-4">
            @forelse($boards as $board)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <a href="{{ route('boards.show', $board) }}" class="text-decoration-none">
                        <div class="board-card p-3" style="background: {{ $board->color }};">
                            <h5 class="mb-2">{{ $board->name }}</h5>
                            @if($board->description)
                                <p class="mb-0 small opacity-75">{{ Str::limit($board->description, 50) }}</p>
                            @endif
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-white-50"></i>
                        <h4 class="text-white mt-3">Nenhum quadro ainda</h4>
                        <p class="text-white-50">Crie seu primeiro quadro para começar a organizar suas tarefas!</p>
                        <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#newBoardModal">
                            <i class="bi bi-plus-lg me-1"></i>Criar Primeiro Quadro
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Novo Quadro -->
    <div class="modal fade" id="newBoardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('boards.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Quadro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome do Quadro *</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ex: Projeto Website">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" rows="2"
                                placeholder="Descrição opcional..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cor do Quadro</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @php
                                    $colors = ['#0079bf', '#d29034', '#519839', '#b04632', '#89609e', '#cd5a91', '#4bbf6b', '#00aecc', '#838c91'];
                                @endphp
                                @foreach($colors as $i => $color)
                                    <input type="radio" name="color" id="color{{ $i }}" value="{{ $color }}" class="d-none" {{ $i === 0 ? 'checked' : '' }}>
                                    <label for="color{{ $i }}" class="color-option {{ $i === 0 ? 'selected' : '' }}"
                                        style="background: {{ $color }};" onclick="selectColor(this)"></label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Quadro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function selectColor(el) {
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
            el.classList.add('selected');
        }
    </script>
@endsection