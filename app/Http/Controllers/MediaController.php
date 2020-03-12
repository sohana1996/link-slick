<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Urls;
use App\Models\Sources;
use App\Models\Media;
use App\Models\Url_visits;
use App\Url;

class MediaController extends Controller
{

    public function store(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'title' => 'required',
                'source_id' => 'required',
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            $title = $input['title'];
            $source_id = $input['source_id'];
            //=====================
            // Existence Check
            //=====================
            $sourceCheck = new Media();
            $check = $sourceCheck->where('title', $title)->where('source_id', $source_id)->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ["New Medium already exist. Please try another one"]
                    ),
                );
                return json_encode($rv, true);
            }
            //=====================
            // Store Info
            //=====================
            $mediaModel = new Media();
            $mediaModel->source_id = $source_id;
            $mediaModel->user_id = $userId;
            $mediaModel->title = $title;
            $mediaModel->is_active = 1;
            $mediaModel->created_at = Carbon::now();
            if($mediaModel->save()){
                $rv = array(
                    "status" => 2000,
                    "msg" => 'New Medium has been added successfully.',
                    "data" => $mediaModel->toArray(),
                );
                return json_encode($rv, true);
            } else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ["Fatal Error! Database not found."]
                    ),
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                ),
            );
            return json_encode($rv, true);
        }
    }
    public function update(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'id' => 'required',
                'title' => 'required',
                'source_id' => 'required',
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            else {
                //=======================
                // Decorate Info
                //=======================
                $id = $input['id'];
                $title = $input['title'];
                $source_id = $input['source_id'];
                //=======================
                // Permission Check
                //=======================
                $pemCheck = new Media();
                $checkPem = $pemCheck->where('id', $id)->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($checkPem) == 0){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["Invalid Request. You have no permission to do this action."]
                        ),
                    );
                    return json_encode($rv, true);
                }
                //=======================
                // Existence Check
                //=======================
                $mediaCheck = new Media();
                $check = $mediaCheck->where('title', $title)->where('source_id', $source_id)->where('is_active', 1)
                    ->where('id','!=', $id)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($check) > 0){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["New Medium already exist. Please try another one"]
                        ),
                    );
                    return json_encode($rv, true);
                }
                //=======================
                // Update Info
                //=======================
                $mediaModel = new Media();
                $mediaModel->where('id', $id)
                    ->update([
                        "title" => $title,
                        "source_id" => $source_id,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Medium has been updated successfully.'
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                ),
            );
            return json_encode($rv, true);
        }
    }
    public function remove(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            else {
                //=======================
                // Decorate Info
                //=======================
                $id = $input['id'];
                //=======================
                // Permission Check
                //=======================
                $pemCheck = new Media();
                $checkPem = $pemCheck->where('id', $id)->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($checkPem) == 0){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["Invalid Request. You have no permission to do this action."]
                        ),
                    );
                    return json_encode($rv, true);
                }
                //=======================
                // Update Info
                //=======================
                $mediaModel = new Media();
                $mediaModel->where('id', $id)
                    ->update([
                        "is_active" => 0,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Medium has been removed successfully.'
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                ),
            );
            return json_encode($rv, true);
        }
    }
    public function getRelatedMedia(Request $request, $source_id){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $source_id = (int)$source_id;
            if($source_id > 0){
                $urlModel = new Media();
                $data = $urlModel->where('is_active', 1)->where('user_id', $userId)
                    ->where('source_id', $source_id)
                    ->orderBy('title', 'asc')->get()->toArray();
                if(count($data) > 0){
                    $rvM = array();
                    foreach ($data as $m){
                        $sourceModel = new Sources();
                        $source = $sourceModel->select('id', 'title')->where('is_active', 1)->where('id', $m['source_id'])->get()->toArray();
                        if(count($source) > 0){
                            $m['source'] = $source[0];
                        }
                        $rvM[] = $m;
                    }
                    $rv = array(
                        "status" => 2000,
                        "data" => $rvM
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ['No more source to preview']
                        )
                    );
                    return json_encode($rv, true);
                }

            }
            else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ['Invalid Request.']
                    )
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                )
            );
            return json_encode($rv, true);
        }
    }
    public function getMedia(Request $request, $pageNo){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $pageNo = (int)$pageNo;
            if($pageNo > 0){
                $limit = 10;
                $offset = ($pageNo - 1) * $limit;
                $urlModel = new Media();
                $data = $urlModel->where('is_active', 1)->where('user_id', $userId)
                    ->take($limit)
                    ->orderBy('id', 'desc')
                    ->skip($offset)->get()->toArray();
                if(count($data) > 0){
                    $rvM = array();
                    foreach ($data as $m){
                        $sourceModel = new Sources();
                        $source = $sourceModel->select('id', 'title')->where('is_active', 1)->where('id', $m['source_id'])->get()->toArray();
                        if(count($source) > 0){
                            $m['source'] = $source[0];
                        }
                        $rvM[] = $m;
                    }
                    $rv = array(
                        "status" => 2000,
                        "data" => $rvM
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ['No more source to preview']
                        )
                    );
                    return json_encode($rv, true);
                }

            }
            else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ['Invalid Request.']
                    )
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                )
            );
            return json_encode($rv, true);
        }
    }
    public function getMediaUrl(Request $request, $id){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $id = (int)$id;
            if($id > 0){
                $mediaModel = new Media();
                $data = $mediaModel->where('is_active', 1)->where('id', $id)->get()->toArray();
                if(count($data) > 0){
                    $rv = array(
                        "status" => 2000,
                        "data" => $data
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ['No more source to preview']
                        )
                    );
                    return json_encode($rv, true);
                }

            }
            else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ['Invalid Request.']
                    )
                );
                return json_encode($rv, true);
            }
        } else {
            $rv = array(
                "status" => 5000,
                "data" => array(
                    "error" => ["Authentication failed!"]
                )
            );
            return json_encode($rv, true);
        }
    }

}
