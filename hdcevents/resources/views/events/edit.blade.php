@extends('layouts.main')

@section('title', 'Edit: ' . $event->title)

@section('content')

<div id="event-create-container" class="col-md-6 offset-md-3">
    <h1>Editando {{ $event->title }}</h1>
    {{-- este form vai para a rota "/events/update/{{ $event->id }}" com metodo PUT --}}
    {{-- enctype="multipart/form-data é nescessario para enviar arquivos por um formulario em html --}}
    <form action="/events/update/{{ $event->id }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- diretiva para o laravel permitir salvar por conta da proteção de csrf --}}
        @method('PUT'){{-- informa que o metodo é 'PUT' e não 'POST' --}}
        <div class="form-group">
            <label for="image">Imagem do evento:</label>
            <input type="file" class="form-control-file" id="image" name="image">
            <img src="/img/events/{{ $event->image }}" alt="{{ $event->title }}" class="img-preview">
        </div>
        <div class="form-group">
            <label for="title">Evento:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Nome do evento" value="{{ $event->title }}">
        </div>
        <div class="form-group">
            <label for="date">Data do evento:</label>
            <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d', strtotime($event->date));}}" >
        </div>
        <div class="form-group">
            <label for="title">Cidade:</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Nome da cidade" value="{{ $event->city}}">
        </div>
        <div class="form-group">
            <label for="title">O evento é privado?</label>
            <select name="private" id="private" class="form-control">
                <option value="0">Não</option>
                <option value="1" {{ $event->private == 1 ? "selected = 'selected'" : "" }}>Sim</option>
            </select>
        </div>
        <div class="form-group">
            <label for="title">Descrição:</label>
            <textarea name="description" id="description" class="form-control" placeholder="O que vai acontecer no evento?">{{ $event->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="title">Adicione itens de infraestrutura:</label>
            <div class="form-group">
                <input type="checkbox" name="items[]" value="Cadeiras" <?php if (in_array("Cadeiras", $event->items)) { echo "checked"; } ?>> Cadeiras
            </div>
            <div class="form-group">
                <input type="checkbox" name="items[]" value="Palco" <?php if (in_array("Palco", $event->items)) { echo "checked"; } ?>> Palco
            </div>
            <div class="form-group">
                <input type="checkbox" name="items[]" value="Cerveja Grátis" <?php if (in_array("Cerveja Grátis", $event->items)) { echo "checked"; } ?>> Cerveja Grátis
            </div>
            <div class="form-group">
                <input type="checkbox" name="items[]" value="Open Food" <?php if (in_array("Open Food", $event->items)) { echo "checked"; } ?>> Open Food
            </div>
            <div class="form-group">
                <input type="checkbox" name="items[]" value="Brindes" <?php if (in_array("Brindes", $event->items)) { echo "checked"; } ?>> Brindes
            </div>
        </div>
        {{-- botão que chama a action do form --}}
        <input type="submit" class="btn btn-primary" value="Editar evento">
    </form>
</div>

@endsection
