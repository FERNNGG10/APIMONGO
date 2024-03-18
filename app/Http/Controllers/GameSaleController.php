<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Game_Inventory;
use App\Models\Game_Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GameSaleController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $sales = Game_Sale::with('game','user','paymentMethod')->get()->map(function($sale){
            return [
                'sale' => $sale->game->name,
                'user' => $sale->user->name,
                'payment_method' => $sale->paymentMethod->method,
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de game sales',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries
        ];
        Log::info('Listado de game sales',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["game_sales"=>$sales]);
    }


    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'game_id'  =>  'required|exists:games,id|numeric',
            'payment_method_id' =>  'required|exists:payment_methods,id|numeric',
            'quantity' =>  'required|numeric|min:1',

        ]);
        if($validate->fails()){
            $datalog = [
                'msg'   =>  'Error en validacion de datos',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $validate->errors()->toArray()
            ];
            Log::alert('Error en validacion de datos',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $game_inventory=Game_Inventory::where('game_id',$request->game_id)->first();
        
        if (!$game_inventory->game->status) {
            $datalog = [
                'msg'   =>  'El juego no está disponible',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  => null
            ];
            Log::alert('El juego no está disponible',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['errors'=>'El juego no está disponible'], 409);
        }
        if($game_inventory->stock<$request->quantity){
            $datalog = [
                'msg'   =>  'No hay suficiente stock',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  => null
            ];
            return response()->json(['errors'=>'No hay suficiente stock'],409);
        }

        $total = $game_inventory->price * $request->quantity;
        $game_sale=Game_Sale::create([
            'game_id' => $request->game_id,
            'user_id' => auth()->user()->id,
            'payment_method_id' => $request->payment_method_id,
            'quantity' => $request->quantity,
            'total' => $total
        ]);
        $game_inventory->stock = $game_inventory->stock - $request->quantity;
        $game_inventory->save();
        $datalog = [
            'msg'   =>  'Venta creada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $game_sale->toArray()
        ];
        Log::info('Venta hecha correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Compra hecha correctamente","game_sale"=>$game_sale],201);


    }

    public function show($id){
        DB::enableQueryLog();
        $sale = Game_Sale::with('game','user','paymentMethod')->find($id);
        $queries = DB::getQueryLog();
        if(!$sale){
            $datalog = [
                'msg'   =>  'Venta no encontrada',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries
            ];
            Log::alert('Venta no encontrada',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(['msg'=>'Venta no encontrada'],404);
        }
        $datalog = [
            'msg'   =>  'Venta encontrada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries
        ];
        Log::info('Venta encontrada',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["game_sale"=>$sale]);
    }
}
