@extends('layouts.main')

@section('title', 'HDC Events')

@section('content')

<div id="search-container" class="col-md-12">
    <h1>Busque um evento</h1>
    {{-- adicionamos o metodo GET para a rota '/' --}}
    <form action="/" method="GET">
        <input type="text" id="search" name="search" class="form-control" placeholder="Procurar...">
    </form>
</div>
<div id="events-container" class="col-md-12">
    @if ($search)
        <h2>Buscando por {{ $search }}</h2>
    @else
        <h2>Proximos Eventos</h2>
        <p class="subtitle">Veja os eventos dos próximos dias</p>
    @endif
    <div id="cards-container" class="row">
        {{-- Onde sera listado os eventos vindos do banco --}}
        @if (count($events) == 0 && $search)
            <p>Não foi possivel encontrar nenhum evento com {{ $search }}! <a href="/">Ver todos!</a></p>
        @elseif (count($events) == 0)
            <p>Não há eventos disponiveis</p>
        @else
            @foreach ($events as $event)
                <div class="card col-md-3">
                    <img src="/img/events/{{ $event->image }}" alt="{{ $event->title }}">
                    <div class="card-body">
                        {{-- strtotime converte a data para um timestamp --}}
                        {{-- 'd/m/Y' converte para uo formato dia/mes/ano --}}
                        <p class="card-date"> {{ date('d/m/Y', strtotime($event->date)) }}</p>
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-participants">{{ count($event->users) }} Participante</p>
                        <a href="/events/{{ $event->id }}" class="btn btn-primary">Saber mais</a> {{-- leva a rota para visualizar um evento --}}
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection



