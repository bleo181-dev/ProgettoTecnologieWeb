<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Pianta;
use App\Serra;
use App\Diario;
use App\Evento;
use App\Bisogno;
use App\Collabora;
use Auth;



class PiantaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->admin === 'AD'){
            $piante = Pianta::all();
            return view('pianta.index', compact('piante'));
        }else{
            return redirect()->route('serra.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()){
            return view('pianta.create');
        }else{
            return view('/auth/login');
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nome'         => 'required|max:100',
            'foto'         => 'required',
            'luogo'        => 'required|max:100',
            'stato'        => 'required'
        ]);

        $serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();

        /* Ottengo le informazioni sull'immagine originale
        list($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/foto/mydog.jpg');

        // Creo la versione 120*90 dell'immagine (thumbnail)
        $thumb = imagecreatetruecolor(120, 90);
        $source = imagecreatefromjpeg($_FILES['foto']['tmp_name']);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, 120, 90, $width, $height);

         Salvo l'immagine ridimensionata */

        $data = file_get_contents($_FILES['foto']['tmp_name']);

        Pianta::create([
            'codice_serra'  => $serra,
            'nome'          => $validateData['nome'],
            'foto'          => $data,
            'luogo'         => $validateData['luogo'],
            'stato'         => $validateData['stato'],
        ]);

        return redirect()->route('serra.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $utente = Auth::user();
        $pianta=Pianta::find($id);
        $serra=Serra::where('codice_serra', $pianta->codice_serra)->first();
        $codici_collab=Collabora::where('codice_serra',$pianta->codice_serra)->pluck('id')->toArray();
        if(Auth::user()){

            if(Auth::user()->admin === 'AD'){

                $pianta = Pianta::find($id);
                $serra=Serra::where('codice_serra', $pianta->codice_serra)->first();
                $diario = Diario::where('codice_pianta',$id)->get();
                $eventi = Bisogno::Join('evento', 'evento.codice_bisogno', '=', 'bisogno.codice_bisogno')
                                ->where('evento.codice_pianta', $id)
                                ->orderBy('data', 'desc')
                                ->get()
                                ->unique('nome');

                $bisogni = Bisogno::where('codice_pianta', $id)->get();

                /*per grafici*/
                //bisogni
                $bis = Bisogno::where('codice_pianta', $id)->get();
                //eventi di un anno, raggruppati e contati per mese
                $ev = collect();
                $year = date("Y");
                foreach($bis as $b)
                {
                    $evto=DB::table('evento')
                                    ->where('codice_pianta', $pianta->codice_pianta)
                                    ->whereYear('created_at', $year)
                                    ->where('codice_bisogno', $b->codice_bisogno)
                                    ->select(DB::raw('codice_bisogno'),DB::raw('created_at'), DB::raw('count(codice_bisogno) as `volte`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('MONTH(created_at) month'))
                                    ->groupby('month')
                                    ->get();
                    $ev = $ev->merge($evto);
                }
                $bis = Bisogno::where('codice_pianta', $id)->get();
                /*fine roba per grafici*/


                return view('pianta.show', compact('utente', 'pianta','diario','eventi','serra','bisogni','bis','ev'));

            }else if(in_array(auth()->id(), $codici_collab)){
                $pianta = Pianta::find($id);
                $serra=Serra::where('codice_serra', $pianta->codice_serra)->first();
                $diario = Diario::where('codice_pianta',$id)->get();
                $eventi = Bisogno::Join('evento', 'evento.codice_bisogno', '=', 'bisogno.codice_bisogno')
                                ->where('evento.codice_pianta', $id)
                                ->orderBy('data', 'desc')
                                ->get()
                                ->unique('nome');

                $bisogni = Bisogno::where('codice_pianta', $id)->get();

                /*per grefici*/
                //bisogni
                $bis = Bisogno::where('codice_pianta', $id)->get();
                //eventi di un anno, raggruppati e contati per mese
                $ev = collect();
                $year = date("Y");
                foreach($bis as $b)
                {
                    $evto=DB::table('evento')
                                    ->where('codice_pianta', $pianta->codice_pianta)
                                    ->whereYear('created_at', $year)
                                    ->where('codice_bisogno', $b->codice_bisogno)
                                    ->select(DB::raw('codice_bisogno'),DB::raw('created_at'), DB::raw('count(codice_bisogno) as `volte`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('MONTH(created_at) month'))
                                    ->groupby('month')
                                    ->get();

                    $ev = $ev->merge($evto);
                }
                $bis = Bisogno::where('codice_pianta', $id)->get();
                /*fine roba per grafici*/

                return view('pianta.show', compact('utente', 'pianta','diario','eventi','serra','ev','bis','year', 'bisogni'));
            }else if(Auth::user()){

                $cod_serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();

                //-------------Si verifica se la pianta è posseduta realmente dall'utente-------------
                $pianta = Pianta::where('codice_pianta', '=', $id)
                            ->where('codice_serra', '=', $cod_serra)
                            ->get()->first();

                if($pianta == null){
                    return redirect()->route('home');
                }
                //------------------------------------------------------------------------------------

                $serra=Serra::where('codice_serra', $pianta->codice_serra)->first();

                $diario = Diario::where('codice_pianta',$id)
                                //->where('id','=', auth()->id())
                                ->get();

                $eventi = Bisogno::Join('evento', 'evento.codice_bisogno', '=', 'bisogno.codice_bisogno')
                                ->where('evento.codice_pianta', $id)
                                ->orderBy('data', 'desc')
                                ->get()
                                ->unique('nome');

                $bisogni = Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();

                /*per grefici*/
                //bisogni
                $bis=Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();
                //eventi di un anno, raggruppati e contati per mese
                $ev = collect();
                $year = date("Y");
                foreach($bis as $b)
                {
                    $evto=DB::table('evento')
                                    ->where('codice_pianta', $id)
                                    ->whereYear('created_at', $year)
                                    ->where('codice_bisogno', $b->codice_bisogno)
                                    ->select(DB::raw('codice_bisogno'), DB::raw('created_at'), DB::raw('count(codice_bisogno) as `volte`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('MONTH(created_at) month'))
                                    ->groupby('month')
                                    ->get();

                    $ev = $ev->merge($evto);
                }
                $bis=Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();
                /*fine roba per grafici*/

                return view('pianta.show', compact('utente', 'pianta','diario','eventi','serra', 'bisogni', 'bis', 'ev', 'year'));


            }else{
                return view('/auth/login');
            }
        }
    }

    /**
     * Mostra la pianta tralasciando alcuni dati, da usare in contesti
     * dove si deve far vedere la pianta ad utenti esterni la serra
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $utente = Auth::user();
        $pianta=Pianta::find($id);
        $serra=Serra::where('codice_serra', $pianta->codice_serra)->first();

                $cod_serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();
                $diario = Diario::where('codice_pianta',$id)->get();

                $eventi = Bisogno::Join('evento', 'evento.codice_bisogno', '=', 'bisogno.codice_bisogno')
                                ->where('evento.codice_pianta', $id)
                                ->orderBy('data', 'desc')
                                ->get()
                                ->unique('nome');

                $bisogni = Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();

                /*per grafici*/
                //bisogni
                $bis=Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();
                //eventi di un anno, raggruppati e contati per mese
                $ev = collect();
                $year = date("Y");
                foreach($bis as $b)
                {
                    $evto=DB::table('evento')
                                    ->where('codice_pianta', $id)
                                    ->whereYear('created_at', $year)
                                    ->where('codice_bisogno', $b->codice_bisogno)
                                    ->select(DB::raw('codice_bisogno'), DB::raw('created_at'), DB::raw('count(codice_bisogno) as `volte`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('MONTH(created_at) month'))
                                    ->groupby('month')
                                    ->get();

                    $ev = $ev->merge($evto);
                }
                $bis=Bisogno::where('codice_pianta', $pianta->codice_pianta)->get();
                /*fine roba per grafici*/

                return view('pianta.view', compact('utente', 'pianta','diario','eventi','serra', 'bisogni', 'bis', 'ev', 'year'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()){

            $utente = Auth::user();
            if(Auth::user()->admin === 'AD'){

                $pianta = Pianta::find($id);
                return view('pianta.edit', compact('pianta'));

            }elseif(Auth::user()){

                $serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();
                $pianta = Pianta::where('codice_pianta', '=', $id)
                                ->where('codice_serra', '=', $serra)
                                ->get()->first();

                if($pianta == null){
                    return redirect()->route('home');
                }else{
                    return view('pianta.edit', compact('utente', 'pianta'));
                }
            }

        }else{
            return view('/auth/login');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()){

            $validateData = $request->validate([
                'codice_serra' => 'required',
                'nome'         => 'required|max:100',
                'foto'         => 'nullable',
                'luogo'        => 'required|max:100',
                'stato'        => 'required'
            ]);

            $input = $request->all();
            if(Auth::user()->admin === 'AD'){
                $pianta = Pianta::find($id);
            }else{
                $serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();
                    $pianta = Pianta::where('codice_pianta', '=', $id)
                                    ->where('codice_serra', '=', $serra)
                                    ->get()->first();
            }


            $pianta->codice_serra = $input['codice_serra'];
            $pianta->nome = $input['nome'];
            $pianta->luogo = $input['luogo'];
            if(!empty($input['foto'])){
                $data = file_get_contents($_FILES['foto']['tmp_name']);
                $pianta->foto = $data;
            }
            $pianta->stato = $input['stato'];

            $pianta->save();
            return redirect()->route('serra.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()){
            if(Auth::user()->admin === 'AD'){
                $pianta = Pianta::where('codice_pianta', $id)
                            ->delete();
                return redirect()->route('pianta.index');
            }else{
                $serra = Serra::where('id', auth()->id())->pluck('codice_serra')->first();
                $pianta = Pianta::where('codice_pianta', '=', $id)
                                ->where('codice_serra', '=', $serra)
                                ->delete();
                return redirect()->route('serra.index');
            }
        }
    }
}
