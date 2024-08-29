<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class pokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = (int) $request->input('rows', 10);
        $page = 1 + (int) $request->input('page', 0);
    
        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        
        $productos = Pokemon::select('nombre', 'pokemones.id', 'url', 'pokemones.updated_at', 'users.name')
        ->join('users', 'users.id', '=', 'pokemones.id_user')
        ->where('eliminado', 0)
        ->paginate($rows);
    
        return response()->json([
            'estatus' => 1,
            'data'=> $productos->items(),
            'total'=> $productos->total()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'tipo' => 'required',
            'url' => 'required|url',
            'hp' => 'required',
            'defensa' => 'required',
            'ataque' => 'required',
            'rapidez' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => $validator->errors()
            ]);
        }

        $id_user = auth()->user()->id;

        $pokemon = new Pokemon();
        $pokemon->nombre = $request->nombre;
        $pokemon->tipo = $request->tipo;
        $pokemon->url = $request->url;
        $pokemon->hp = $request->hp;
        $pokemon->defensa = $request->defensa;
        $pokemon->ataque = $request->ataque;
        $pokemon->rapidez = $request->rapidez;
        $pokemon->id_user = $id_user;
        $pokemon->eliminado = 0;
        $pokemon->save();

        return response()->json([
            'data'=>$pokemon,
            'estatus' => 1,
            'mensaje' => 'Pokemon registrado con exito'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pokemon = Pokemon::select('nombre', 'tipo', 'pokemones.id', 'url', 'rapidez', 'defensa', 'hp', 'ataque', 'users.name')
        ->join('users', 'users.id', '=', 'pokemones.id_user')
        ->where('pokemones.id', $id)
        ->where('pokemones.eliminado', 0)
        ->first();

        if(!$pokemon){
            return response()->json([
                'estatus'=>0,
                'mensaje'=> 'Pokemon no encontrado'
            ]);
        }

        return response()->json([
            'estatus'=>1,
            'data'=> $pokemon
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'tipo' => 'required',
            'url' => 'required|url',
            'hp' => 'required|integer',
            'defensa' => 'required|integer',
            'ataque' => 'required|integer',
            'rapidez' => 'required|integer',
        ]);
   
        if ($validator->fails()) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => $validator->errors()
            ]);
        }
   
        $pokemon = Pokemon::find($id);
        if (!$pokemon || $pokemon->eliminado == 1) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Pokemon no encontrado'
            ]);
        }
   
        // Verificación para asegurarse de que solo el creador puede actualizar
        if ($pokemon->id_user !== auth()->user()->id) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'No puedes modificar un pokemon que no has creado.'
            ]);
        }
   
        // Actualización del Pokémon
        $pokemon->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'url' => $request->url,
            'hp' => $request->hp,
            'defensa' => $request->defensa,
            'ataque' => $request->ataque,
            'rapidez' => $request->rapidez,
        ]);
   
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Pokemon actualizado con éxito'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el Pokémon por ID
        $pokemon = Pokemon::find($id);
   
        if (!$pokemon || $pokemon->eliminado == 1) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Pokemon no encontrado'
            ]);
        }
   
        if ($pokemon->id_user !== auth()->user()->id) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'No autorizado'
            ]);
        }
   
        $pokemon->eliminado = 1;
        $pokemon->save();
   
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Pokemon eliminado'
        ]);
    }

}
