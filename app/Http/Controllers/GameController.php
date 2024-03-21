<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Classification;
use App\Models\Developer;
use App\Models\Game;
use App\Models\Log as ModelsLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    protected $mongo;
    public function __construct()
    {
       
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $games = Game::with(
            'game_inventory',
            'category',
            'classification',
            'developer',
            'supplier',
            'sales'
        )->where('status', true)->get()->map(function($game){
            return [
                'id'  =>  $game->id,  
                'game'=>$game->name,
                'stock' => $game->game_inventory->stock,
                'category'=>$game->category->category,
                'classification'=>$game->classification->classification,
                'developer' => $game->developer->name,
                'supplier'  => $game->supplier->name,
                'price' => $game->game_inventory->price
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de juegos',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        Log::info('Listado de juegos',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["games"=>$games]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'description' =>  'required|max:50',
            'category_id' =>  'required|exists:categories,id|numeric',
            'classification_id' =>  'required|exists:classifications,id|numeric',
            'developer_id' =>  'required|exists:developers,id|numeric',
            'supplier_id' =>  'required|exists:suppliers,id|numeric',
            'stock' =>  'required|numeric|between:1,999999',
            'price' =>  'required|numeric|between:1,999999.99'
        ]);

        if($validate->fails()){
           
            return response()->json(["errors"=>$validate->errors()],400);
        }

        $game=Game::create([
            'name'  =>  $request->name,
            'description'   =>  $request->description,
            'category_id'   =>  $request->category_id,
            'classification_id' =>  $request->classification_id,
            'developer_id'  =>  $request->developer_id,
            'supplier_id'   =>  $request->supplier_id
        ]);
        $game->game_inventory()->create([
            'stock' => $request->stock,
            'price' => $request->price
        ]);

        $datalog = [
            'msg'   =>  'Juego creado correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $game->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Juego creado correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Juego creado correctamente","game"=>$game],201);
    }

    public function show(int $id){
        DB::enableQueryLog();
        $game = Game::with(
            'game_inventory',
            'category',
            'classification',
            'developer',
            'supplier',
            'sales'
        )->find($id);
        $queries = DB::getQueryLog();
        if($game){
            $datalog = [
                'msg'   =>  'Detalle de juego',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries,
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Detalle de juego',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["game"=>$game]);
        }
        
        return response()->json(["msg"=>"Juego no encontrado"],404);
    }

    public function update(Request $request,int $id){
        $game = Game::find($id);
        if($game){
            $validate = Validator::make($request->all(),[
                'name'  =>  'required|max:30',
                'description' =>  'required|max:50',
                'category_id' =>  'required|exists:categories,id|numeric',
                'classification_id' =>  'required|exists:classifications,id|numeric',
                'developer_id' =>  'required|exists:developers,id|numeric',
                'supplier_id' =>  'required|exists:suppliers,id|numeric',
                'stock' =>  'required|numeric|between:1,999999.99',
                'price' =>  'required|numeric|between:1,999999'
            ]);
    
            if($validate->fails()){
              
                return response()->json(["errors"=>$validate->errors()],400);
            }
    
            $game->update([
                'name'  =>  $request->name,
                'description'   =>  $request->description,
                'category_id'   =>  $request->category_id,
                'classification_id' =>  $request->classification_id,
                'developer_id'  =>  $request->developer_id,
                'supplier_id'   =>  $request->supplier_id
            ]);
            $game->game_inventory()->update([
                'stock' => $request->stock,
                'price' => $request->price
            ]);
    
            $datalog = [
                'msg'   =>  'Juego actualizado correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  $request->method(),
                'ruta' =>   $request->url(),
                'data'  =>  $game->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Juego actualizado correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["msg"=>"Juego actualizado correctamente","game"=>$game],201);
        }
       
        return response()->json(["msg"=>"Juego no encontrado"],404);
    }

    public function destroy(int $id){
        $game = Game::find($id);
        if($game){
            $game->status=false;
            $game->save();
            $datalog = [
                'msg'   =>  'Juego eliminado correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $game->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Juego eliminado correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["msg"=>"Juego eliminado correctamente","game"=>$game],201);
        }
       
        return response()->json(["msg"=>"Juego no encontrado"],404);
    }

    public function categories(){
        $categories = Category::where('status',true)->get();
        return response()->json(["categories"=>$categories],200);
    }

    public function classifications(){
        $classifications = Classification::where('status',true)->get();
        return response()->json(["classifications"=>$classifications],200);
    }

    public function developers(){
        $developers = Developer::where('status',true)->get();
        return response()->json(["developers"=>$developers],200);
    }

    public function suppliers(){
        $suppliers = Supplier::where('status',true)->get();
        return response()->json(["suppliers"=>$suppliers],200);
    }

    
}
