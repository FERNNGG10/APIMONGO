<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\ConsoleSaleController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DlcController;
use App\Http\Controllers\GadgetController;
use App\Http\Controllers\GadgetSaleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameSaleController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupplierController;
use App\Models\Game;
use App\Models\Game_Sale;
use App\Models\Review;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\GadgetSaleSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Nette\Utils\Json;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use Illuminate\Support\Facades\Event ;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('register',[AuthController::class,'register']);
    Route::middleware('isactive')->post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::get('me', [AuthController::class,'me']);
    Route::get('activate/{user}',[AuthController::class,'activate'])->name('activate')->middleware('signed');
    Route::get('rolid',[AuthController::class,'rolid']);
    Route::get('status',[AuthController::class,'status']);

});

Route::middleware(['auth:api','authstatus'])->prefix('users')->group(function(){
    Route::middleware('admin')->get('index',[UserController::class,'index']);
    Route::middleware('admin')->post('store',[UserController::class,'store']);
    Route::middleware('admin')->get('show/{user}',[UserController::class,'show'])->where('user', '[0-9]+');
    Route::middleware('admin')->put('update/{user}',[UserController::class,'update'])->where('user', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{user}',[UserController::class,'destroy'])->where('user', '[0-9]+');
    Route::middleware('admin')->get('roles',[UserController::class,'roles']);
});

Route::get('sse',[CategoryController::class,'SSE']);

Route::middleware(['auth:api','authstatus'])->prefix('categories')->group(function(){
    Route::get('index',[CategoryController::class,'index']);
    Route::middleware('admin.user')->post('store',[CategoryController::class,'store']);
    Route::get('show/{category}',[CategoryController::class,'show'])->where('category', '[0-9]+');
    Route::middleware('admin.user')->put('update/{category}',[CategoryController::class,'update'])->where('category', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{category}',[CategoryController::class,'destroy'])->where('category', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('classifications')->group(function(){
    Route::get('index',[ClassificationController::class,'index']);
    Route::middleware('admin.user')->post('store',[ClassificationController::class,'store']);
    Route::get('show/{classification}',[ClassificationController::class,'show'])->where('classification', '[0-9]+');
    Route::middleware('admin.user')->put('update/{classification}',[ClassificationController::class,'update'])->where('classification', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{classification}',[ClassificationController::class,'destroy'])->where('classification', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('developers')->group(function(){
    Route::middleware('admin.guest')->get('index',[DeveloperController::class,'index']);
    Route::middleware('admin')->post('store',[DeveloperController::class,'store']);
    Route::middleware('admin.guest')->get('show/{developer}',[DeveloperController::class,'show'])->where('developer', '[0-9]+');
    Route::middleware('admin')->put('update/{developer}',[DeveloperController::class,'update'])->where('developer', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{developer}',[DeveloperController::class,'destroy'])->where('developer', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('suppliers')->group(function(){
    Route::middleware('admin.guest')->get('index',[SupplierController::class,'index']);
    Route::middleware('admin')->post('store',[SupplierController::class,'store']);
    Route::middleware('admin.guest')->get('show/{supplier}',[SupplierController::class,'show'])->where('supplier', '[0-9]+');
    Route::middleware('admin')->put('update/{supplier}',[SupplierController::class,'update'])->where('supplier', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{supplier}',[SupplierController::class,'destroy'])->where('supplier', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('paymenth')->group(function(){
    Route::get('index',[PaymentMethodController::class,'index']);
    Route::middleware('admin.user')->post('store',[PaymentMethodController::class,'store']);
    Route::get('show/{paymentMethod}',[PaymentMethodController::class,'show'])->where('paymentMethod', '[0-9]+');
    Route::middleware('admin.user')->put('update/{paymentMethod}',[PaymentMethodController::class,'update'])->where('paymentMethod', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{paymentMethod}',[PaymentMethodController::class,'destroy'])->where('paymentMethod', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('games')->group(function(){
    Route::middleware('admin.guest')->get('index',[GameController::class,'index']);
    Route::middleware('admin')->post('store',[GameController::class,'store']);
    Route::middleware('admin.guest')->get('show/{game}',[GameController::class,'show'])->where('game', '[0-9]+');
    Route::middleware('admin')->put('update/{game}',[GameController::class,'update'])->where('game', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{game}',[GameController::class,'destroy'])->where('game', '[0-9]+');
    Route::get('categories',[GameController::class,'categories']);
    Route::get('classifications',[GameController::class,'classifications']);
    Route::get('developers',[GameController::class,'developers']);
    Route::get('suppliers',[GameController::class,'suppliers']);
});

Route::middleware(['auth:api','authstatus'])->prefix('consoles')->group(function(){
    Route::middleware('admin.guest')->get('index',[ConsoleController::class,'index']);
    Route::middleware('admin')->post('store',[ConsoleController::class,'store']);
    Route::middleware('admin.guest')->get('show/{console}',[ConsoleController::class,'show'])->where('console', '[0-9]+');
    Route::middleware('admin')->put('update/{console}',[ConsoleController::class,'update'])->where('console', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{console}',[ConsoleController::class,'destroy'])->where('console', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('gadgets')->group(function(){
    Route::middleware('admin.guest')->get('index',[GadgetController::class,'index']);
    Route::middleware('admin')->post('store',[GadgetController::class,'store']);
    Route::middleware('admin.guest')->get('show/{gadget}',[GadgetController::class,'show'])->where('gadget', '[0-9]+');
    Route::middleware('admin')->put('update/{gadget}',[GadgetController::class,'update'])->where('gadget', '[0-9]+');
    Route::middleware('admin.guest')->delete('destroy/{gadget}',[GadgetController::class,'destroy'])->where('gadget', '[0-9]+');
});

Route::middleware(['auth:api','authstatus'])->prefix('dlcs')->group(function(){
    Route::middleware('admin.guest')->get('index',[DlcController::class,'index']);
    Route::middleware('admin')->post('store',[DlcController::class,'store']);
    Route::middleware('admin.guest')->get('show/{dlc}',[DlcController::class,'show'])->where('dlc', '[0-9]+');
    Route::middleware('admin')->put('update/{dlc}',[DlcController::class,'update'])->where('dlc', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{dlc}',[DlcController::class,'destroy'])->where('dlc', '[0-9]+');
    Route::get('games',[DlcController::class,'games']);
});

Route::middleware(['auth:api','authstatus'])->prefix('reviews')->group(function(){
    Route::middleware('admin.guest')->get('index',[ReviewController::class,'index']);
    Route::middleware('admin')->post('store',[ReviewController::class,'store']);
    Route::middleware('admin.guest')->get('show/{review}',[ReviewController::class,'show'])->where('review', '[0-9]+');
    Route::middleware('admin')->put('update/{review}',[ReviewController::class,'update'])->where('review', '[0-9]+');
    Route::middleware('admin')->delete('destroy/{review}',[ReviewController::class,'destroy'])->where('review', '[0-9]+');
    Route::get('games',[ReviewController::class,'games']);
});

Route::middleware('auth:api')->prefix('game/sales')->group(function(){
    Route::get('index',[GameSaleController::class,'index']);
    Route::post('store',[GameSaleController::class,'store']);
    Route::get('show/{game}',[GameSaleController::class,'show'])->where('game', '[0-9]+');
   
});

Route::middleware('auth:api')->prefix('consoles/sales')->group(function(){
    Route::get('index',[ConsoleSaleController::class,'index']);
    Route::post('store',[ConsoleSaleController::class,'store']);
    Route::get('show/{console}',[ConsoleSaleController::class,'show'])->where('console', '[0-9]+');
   
});

Route::middleware('auth:api')->prefix('gadgets/sales')->group(function(){
    Route::get('index',[GadgetSaleController::class,'index']);
    Route::post('store',[GadgetSaleController::class,'store']);
    Route::get('show/{gadget}',[GadgetSaleController::class,'show'])->where('gadget', '[0-9]+');
   
});

Route::middleware(['auth:api','admin'])->get('logs',[LogController::class,'index']);

Route::get('test',[TestController::class,'test']);





/*Route::get('/stream',function(){
set_time_limit(0);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
Event::listen('App\Events\ConsoleEvent',function($event){
    echo "data: {$event->console->name}\n\n";
    ob_flush();
    flush();
});
while(true){
    sleep(5);
}
});*/























/*
Route::get('xd',function(){

    $venta = Game_Sale::with(['game','user'])->get();
    return response()->json(['data'=>$venta]);
    $ventas = Game_Sale::with(['game', 'user'])
    ->get()
    ->map(function ($venta) {
        return [
            'quantity' => $venta->quantity,
            'total' => $venta->total,
            'user_name' => $venta->user->name,
            'game_name' => $venta->game->name,
        ];
    });
    return response()->json(['data'=>$ventas]);
    $suppliers = Supplier::with(['games.sales'])->get();
    return response()->json(["data"=>$suppliers],200);
    $ventas = Game_Sale::with(['user.reviews', 'paymentMethod'])->where('game_id', 1)->get()->map(function ($venta) {
        return [
            'quantity' => $venta->quantity,
            'total' => $venta->total,
            'juego' => $venta->game->name,
            'user_name' => $venta->user->name,
            'payment_method' => $venta->paymentMethod->method,
            'reviews' => $venta->user->reviews->pluck('comment'),
        ];
    });
    return response()->json(['data' => $ventas]);
});

Route::get('prueba',function(Request $request){
    $url=$request->url();
    $method = $request->method();
    Log::critical("usuario solicito ruta ",["url"=>$url,"method"=>$method]);
    return response()->json(["url"=>$url,"method"=>$method],200);
});*/
/*
Route::get('mongo',[LogController::class,'index']);
Route::get('mysql',function(){
   
    return response()->json(['data'=>User::all()],200);
});
*/