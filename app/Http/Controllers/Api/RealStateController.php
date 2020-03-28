<?php

namespace App\Http\Controllers\Api;

use App\RealState;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RealStateRequest;
use App\Api\ApiMessages;

class RealStateController extends Controller
{
    public function index()
    {
        $realStates = auth('api')->user()->real_state();

        return response()->json($realStates->paginate(10), 200);
    }

    public function show($id)
    {
        try {
            $realState = auth('api')->user()->real_state()
                                            ->with('photos')
                                            ->findOrFail($id)
                                            ->makeHidden('thumb');

            return response()->json(['data' => $realState], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function store(RealStateRequest $request)
    {
        $data = $request->all();
        $images = $request->file('images');

        try {
            $data['user_id'] = auth('api')->user()->id;

            $realState = RealState::create($data);

            if(isset($data['categories']) && count($data['categories'])) {
                $realState->categories()->sync($data['categories']);
            }

            if($images) {
                $imagesUploaded = [];

                foreach ($images as $image) {
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path, 'is_thumb' => false];
                }
                
                $realState->photos()->createMany($imagesUploaded);
            }

            return response()->json([
                'data' => [
                    'msg' => 'Imóvel cadastrado com sucesso!'
                ]
            ], 201);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function update(RealStateRequest $request, $id)
    {
        $data = $request->all();
        $images = $request->file('images');

        try {
            $realState = auth('api')->user()->real_state()->findOrFail($id);
            $realState->update($data);

            if(isset($data['categories']) && count($data['categories'])) {
                $realState->categories()->sync($data['categories']);
            }

            if($images) {
                $imagesUploaded = [];

                foreach ($images as $image) {
                    $path = $image->store('images', 'public');
                    $imagesUploaded[] = ['photo' => $path, 'is_thumb' => false];
                }
                
                $realState->photos()->createMany($imagesUploaded);
            }

            return response()->json([
                'data' => [
                    'msg' => 'Imóvel atualizado com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }

    public function destroy($id)
    {
        try {
            $realState = auth('api')->user()->real_state()->findOrFail($id);
            $realState->delete();

            return response()->json([
                'data' => [
                    'msg' => 'Imóvel removido com sucesso!'
                ]
            ], 200);
        } catch (\Exception $e) {
            $message = new ApiMessages($e->getMessage());
            return response()->json($message->getMessage(), 401);
        }
    }
}
