@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class ="col-md-8">
            <div class="card">
                <div class="card-header"> Diario di {{ $pianta->nome }}</div>

                    <div class="card-body">
                        <div style="padding: 10px;"> 
                            <a href="{{ URL::action('DiarioController@create', $id) }}" class="btn btn-success"> Crea ricordo</a>
                        </div>
                        <div class="row">
                            @foreach($diario as $i)
                                @include('diario.diariopost')
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection