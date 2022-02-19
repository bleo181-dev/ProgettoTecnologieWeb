@extends('layouts.app')

@section('content')
<div style="width: fill; background-color: #1e90ff; margin-bottom: 0rem;">
    <br>
    <div id="cont" class="container">
        <div style="display: flex; align-items: center;">
            @if(auth()->id() == $pianta->codice_serra)
            <a href="{{ URL::action('PiantaController@show', $pianta->codice_pianta) }}"><img src="{{ asset('immagini/back.png') }}" class="iconaBack"></a>
            @else
            <a href="{{URL::action('PiantaController@show', $pianta->codice_pianta)  }}"><img src="{{ asset('immagini/back.png') }}" class="iconaBack"></a>
            @endif
            <h1 style="margin-left:10px">Diario di {{ $pianta->nome }}</h1>
        </div>
    </div>  
        
        <br>
        <hr>
    </div>
    <div class="row justify-content-center">
        @foreach($diario as $i)
            @include('diario.diariopost')
        @endforeach
    </div>
    </div>
    <div class="row justify-content-center">
        <div class="jumbotron jumbotron-fluid col-md-8">
            <div class="container">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <a href="{{ URL::action('DiarioController@create', $id) }}" > <img src="{{ asset('immagini/addSerra.png') }}" style="height: 80px; width:80px;" /></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection