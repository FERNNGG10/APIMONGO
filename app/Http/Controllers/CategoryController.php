<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $categories = Category::where('status', true)->get();
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de categorias',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de categorias',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["categories"=>$categories]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'category'  =>  'required|max:30'
        ]);

        if($validate->fails()){
           
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $category = Category::create([
            'category'  =>  $request->category
        ]);

        $logdata=[
            'msg'   =>  'Categoria creada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $category->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Categoria creada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Categoria creada correctamente","category"=>$category],201);
    }

    public function update(Request $request, int $id){
        $category = Category::find($id);
        if(!$category){

            return response()->json(["msg"=>"No se encontro la categoria"],404);
        }

        $validate = Validator::make($request->all(),[
            'category'  =>  'required|max:30'
        ]);

        if($validate->fails()){
      
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $category->category = $request->category;
        $category->save();

        $logdata=[
            'msg'   =>  'Categoria actualizada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $category->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Categoria actualizada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Categoria actualizada correctamente","category"=>$category],200);
    }

    public function destroy(Request $request, int $id){
       
        $category = Category::find($id);
        if(!$category){
            return response()->json(["msg"=>"No se encontro la categoria"],404);
        }

        $category->status = false;
        $category->save();

        $logdata=[
            'msg'   =>  'Categoria eliminada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  $request->method(),
            'ruta' =>   $request->url(),
            'data'  =>  $category->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Categoria eliminada correctamente',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["msg"=>"Categoria eliminada correctamente","category"=>$category],200);
    }

    public function show(int $id){
        DB::enableQueryLog();
        $category = Category::find($id);
        $queries = DB::getQueryLog();
        if(!$category){
           
            return response()->json(["msg"=>"No se encontro la categoria"],404);
        }

        $logdata=[
            'msg'   =>  'Categoria encontrada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Categoria encontrada',$logdata);
        $this->mongo->Log = $logdata;
        $this->mongo->save();
        return response()->json(["category"=>$category],200);
    }

}
