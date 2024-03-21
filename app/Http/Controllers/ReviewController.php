<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Log as ModelsLog;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    protected $mongo;
    public function __construct()
    {
        $this->mongo = new ModelsLog;
    }

    public function index(){
        DB::enableQueryLog();
        $reviews = Review::with('game','user')->get()->map(function($review){
            return [
                'id'  =>  $review->id,
                'review' => $review->comment,
                'user' => $review->user->name,
                'game' => $review->game->name,
            ];
        });
        $queries = DB::getQueryLog();
        $datalog = [
            'msg'   =>  'Listado de reviews',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $queries,
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Listado de reviews',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["reviews"=>$reviews]);
    }

    public function store(Request $request){
        $validate = Validator::make($request->all(),[
            'comment'  =>  'required|max:255',
            'game_id' =>  'required|exists:games,id|numeric',
          
        ]);
        if($validate->fails()){
           
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $review = Review::create([
            'comment' => $request->comment,
            'game_id' => $request->game_id,
            'user_id' => auth()->user()->id]);
        $datalog = [
            'msg'   =>  'Review creada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $review->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Review creada correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Review creada correctamente","review"=>$review]);
    }

    public function show($id){
        DB::enableQueryLog();
        $review = Review::with('game','user')->find($id);
        $queries = DB::getQueryLog();
        if(!$review){
            
            return response()->json(['msg'=>'Review no encontrada'],404);
        }
        $datalog = [
            'msg'   =>  'Review encontrada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $review->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Review encontrada',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["review"=>$review]);
    }

    public function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'comment'  =>  'required|max:255',
            'game_id' =>  'required|exists:games,id|numeric',
        ]);
        if($validate->fails()){
         
            return response()->json(['errors'=>$validate->errors()],422);
        }
        $review = Review::find($id);
        if(!$review){
           
            return response()->json(['msg'=>'Review no encontrada'],404);
        }
        $review->update([
            'comment' => $request->comment,
            'game_id' => $request->game_id,
            'user_id' => auth()->user()->id]);

        $datalog = [
            'msg'   =>  'Review actualizada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $review->toArray(),
            'timestamp' => date('Y-m-d H:i:s'),  // Agrega la fecha y la hora actual
        ];
        Log::info('Review actualizada correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Review actualizada correctamente","review"=>$review]);
    }

    public function destroy($id){
        $review = Review::find($id);
        if(!$review){
           
            return response()->json(['msg'=>'Review no encontrada'],404);
        }
        $review->delete();
        $datalog = [
            'msg'   =>  'Review eliminada',
            'user_id'  =>  auth()->user()->id,
            'verbo' =>  request()->method(),
            'ruta' =>   request()->url(),
            'data'  =>  $review->toArray(),
        ];
        Log::info('Review eliminada correctamente',$datalog);
        $this->mongo->Log = $datalog;
        $this->mongo->save();
        return response()->json(["msg"=>"Review eliminada correctamente"]);
    }

    public function games(){
        return response()->json(["games"=>Game::all()]);
    }
}
