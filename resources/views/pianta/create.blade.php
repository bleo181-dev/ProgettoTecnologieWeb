
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Creazione pianta') }}</div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ URL::action('PiantaController@store') }}" method="POST" enctype="multipart/form-data" >
                        {{ csrf_field() }}
                        <h1> Inserisci i dati della pianta </h1>

                        <!--<input type="number" name="codice_serra" placeholder="1111" value="{{ old('codice_serra') }}"/> <label> Codice serra </label>
                        <br>
                        <br>-->

                        <input type="text" name="nome" placeholder="Basilico" value="{{ old('nome') }}" /> <label> Nome pianta </label>
                        <br>
                        <br>

                        <input type="text" name="luogo" placeholder="Giardino" value="{{ old('luogo') }}"/> <label> Dove si trova </label>
                        <br>
                        <br>

                        <input type="file" name="foto"> <label> Carica una foto</label>
                        <br>
                        <br>

                        Visibilità
                        <br>
                        <input type="radio" name="stato" value="0" /> privata
                        <br>
                        <input type="radio" name="stato" value="1" /> pubblica
                        <br>
                        <br>

                        <input type="submit" value="Aggiungi pianta" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
