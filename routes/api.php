<?php



use App\Http\Controllers\api\pokemonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

Route::get('hola', function(){
    return 'Hola Mundo';
});
Route::post('user/login',[UserController::class, 'login']);
/* 
Route::post('usuari', [userController::class, 'create']); */

Route::group(['middleware'=>['auth:sanctum']], function(){

    Route::prefix('pokemon')->group(function(){
        Route::get('',[pokemonController::class,'index']);
        Route::post('',[pokemonController::class,'create']);
        Route::get('/{id}',[pokemonController::class,'show'])->where('id','[0-9]+');
        Route::patch('/{id}',[pokemonController::class,'update'])->where('id','[0-9]+');
        Route::delete('/{id}',[pokemonController::class,'destroy'])->where('id','[0-9]+');
    });

    Route::prefix('usuario')->group(function(){
        Route::get('', [userController::class, 'index']);
        Route::post('', [userController::class, 'create']);
        Route::get('/{id}', [userController::class, 'show'])->where('id', '[0-9]+');
        Route::patch('/{id}', [userController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [userController::class, 'destroy'])->where('id', '[0-9]+');
    });
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
