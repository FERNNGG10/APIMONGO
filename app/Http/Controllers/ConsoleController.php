<?php

namespace App\Http\Controllers;

use App\Events\ConsoleEvent;
use App\Models\Console;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConsoleController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $consoles = Console::with('console_inventory','supplier')->where('status',true)->get()->map(function($console){
            return [
                'id'  =>  $console->id,
                'console' => $console->name,
                'description' => $console->description,
                'stock' => $console->console_inventory->stock,
                'price' => $console->console_inventory->price,
                'supplier' => $console->supplier->name
            ];
        });
        
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de consolas',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de consolas',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["consoles"=>$consoles]);
    }
    
    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'description' =>  'required|max:50',
            'supplier_id' =>  'required|exists:suppliers,id|numeric',
            'stock' =>  'required|numeric|between:1,999999',
            'price' =>  'required|numeric|between:1,999999.99'
        ]);
        if($validate->fails()){
         
            return response()->json(["errors"=>$validate->errors()],400);
        }
        $console = Console::create([
            'name'  =>  $request->name,
            'description' =>  $request->description,
            'supplier_id' =>  $request->supplier_id
        ]);
        $console->console_inventory()->create([
            'stock' => $request->stock,
            'price' => $request->price
        ]);
        $datalog = [
            'msg'   =>  'Consola creada correctamente',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $console->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Consola creada correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        //event(new ConsoleEvent($console->name));
        $this->sendnotification('console',$console->name);
        return response()->json(["msg"=>"Consola creada correctamente","console"=>$console],201);
    
    }

    public function show($id){
        DB::enableQueryLog();
        $console = Console::with('console_inventory','supplier')->find($id);
        $queries = DB::getQueryLog();
        if($console){
            $datalog = [
                'msg'   =>  'Consola encontrada',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $queries,
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Consola encontrada',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["console"=>$console]);
        }
        
       
        return response()->json(["msg"=>"Consola no encontrada"],404);
    }


    public function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'name'  =>  'required|max:30',
            'description' =>  'required|max:50',
            'supplier_id' =>  'required|exists:suppliers,id|numeric',
            'stock' =>  'required|numeric|between:1,999999',
            'price' =>  'required|numeric|between:1,999999.99'
        ]);
        if($validate->fails()){
           
            return response()->json(["errors"=>$validate->errors()],400);
        }
        $console = Console::find($id);
        if($console){
            
            $console->update([
                'name'  =>  $request->name,
                'description' =>  $request->description,
                'supplier_id' =>  $request->supplier_id
            ]);
            $console->console_inventory()->update([
                'stock' => $request->stock,
                'price' => $request->price
            ]);
            $datalog = [
                'msg'   =>  'Consola actualizada correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $console->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Consola actualizada correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["msg"=>"Consola actualizada correctamente","console"=>$console]);
        }
        
        return response()->json(["msg"=>"Consola no encontrada"],404);
    }
    

    public function destroy($id){
        $console = Console::find($id);
        if($console){
            $console->status=false;
            $console->save();
            $datalog = [
                'msg'   =>  'Consola eliminada correctamente',
                'user_id'  =>  auth()->user()->id,
                'verbo' =>  request()->method(),
                'ruta' =>   request()->url(),
                'data'  =>  $console->toArray(),
                'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
            ];
            Log::info('Consola eliminada correctamente',$datalog);
            $this->mongo->Log = $datalog;
            $this->mongo->save();
            return response()->json(["msg"=>"Consola eliminada correctamente"]);
        }
       
        return response()->json(["msg"=>"Consola no encontrada"],404);
    }

    private function sendnotification($event,$data){
        $message = json_encode(['event' => $event, 'data' => $data]);
        echo "event: $event\n";
        echo "data: $message\n\n";
        flush();
    }
    
}
