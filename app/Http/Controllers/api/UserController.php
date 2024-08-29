<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = (int) $request->input('rows', 10);
        $page = 1+ (int) $request->input('page', 0);

        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $users = User::paginate($rows);
        return response()->json([
            'estatus' => 1,
            'data'=> $users->items(),
            'total'=> $users->total()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email|unique:users',
            'password'=> 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'estatus'=>0,
                'mensaje'=> $validator->errors()
            ]);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'estatus'=>1,
            'mensaje'=> 'Registrado'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user){
            return response()->json([
                'estatus'=>0,
                'mensaje'=> 'Usuario no es encontrado'
            ]);
        }

        return response()->json([
            'estatus'=>1,
            'data'=> $user
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
        if(!isset($request->name)){
            return response()->json([
                'estatus'=>0,
                'mensaje'=> 'Falta el nombre'
            ]);
        }
        User::where('id', $id)->update([
            'name' => $request->name
        ]);

        return response()->json([
            'estatus'=>1,
            'data'=> 'Actualizado'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user){
            return response()->json([
                'estatus'=>0,
                'mensaje'=> 'Usuario no es encontrado'
            ]);
        }

        $user->delete();

        return response()->json([
            'estatus'=>1,
            'mensaje'=> 'Usuario eliminado'
        ]);
    }

    public function login(Request $request){
        $request-> validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', '=', $request->email)->first();

        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'status'=>1,
                    'mensaje'=>'Usuario correcto',
                    'access_token' => $token,
                    'data'=> $user
                ]);
            } else {
                return response()->json([
                    'status'=>0,
                    'mensaje'=>'Contraseña incorrecta'
                ]);
            }
        } else {
            return response()->json([
                'status'=>0,
                'mensaje'=>'No existe el usuario'
            ]);
        }
    }
}
