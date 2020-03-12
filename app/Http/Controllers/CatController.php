<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Urls;
use App\Models\Sources;
use App\Models\Url_categories;
use App\Models\Url_visits;
use App\Url;

class CatController extends Controller
{

    public function store(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'title' => 'required',
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            $title = $input['title'];
            //=====================
            // Existence Check
            //=====================
            $sourceCheck = new Url_categories();
            $check = $sourceCheck->where('title', $title)->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ["New source already exist. Please try another one"]
                    ),
                );
                return json_encode($rv, true);
            }
            //=====================
            // Store Info
            //=====================
            $sourceModel = new Url_categories();
            $sourceModel->user_id = $userId;
            $sourceModel->title = $title;
            $sourceModel->is_active = 1;
            $sourceModel->created_at = Carbon::now();
            if($sourceModel->save()){
                $rv = array(
                    "status" => 2000,
                    "msg" => 'New category has been added successfully.',
                    "data" => $sourceModel->toArray(),
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
                //=======================
                // Permission Check
                //=======================
                $pemCheck = new Url_categories();
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
                $sourceCheck = new Url_categories();
                $check = $sourceCheck->where('title', $title)->where('is_active', 1)
                    ->where('id','!=', $id)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($check) > 0){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["New category already exist. Please try another one"]
                        ),
                    );
                    return json_encode($rv, true);
                }
                //=======================
                // Update Info
                //=======================
                $sourceModel = new Url_categories();
                $sourceModel->where('id', $id)
                    ->update([
                        "title" => $title,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Category has been updated successfully.'
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
                $pemCheck = new Url_categories();
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
                $sourceModel = new Url_categories();
                $sourceModel->where('id', $id)
                    ->update([
                        "is_active" => 0,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Category has been removed successfully.'
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
    public function getAllSource(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $urlModel = new Url_categories();
            $data = $urlModel->where('is_active', 1)->where('user_id', $userId)
                ->orderBy('title', 'asc')->get()->toArray();
            if(count($data) > 0){
                $rv = array(
                    "status" => 2000,
                    "data" => $data
                );
                return json_encode($rv, true);
            }
            else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ['No more category to preview']
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
    public function getSource(Request $request, $pageNo){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $pageNo = (int)$pageNo;
            if($pageNo > 0){
                $limit = 10;
                $offset = ($pageNo - 1) * $limit;
                $urlModel = new Url_categories();
                $data = $urlModel->where('is_active', 1)->where('user_id', $userId)
                    ->take($limit)
                    ->orderBy('id', 'desc')
                    ->skip($offset)->get()->toArray();
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
    public function getSingleUrl(Request $request, $id){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $id = (int)$id;
            if($id > 0){
                $sourceModel = new Url_categories();
                $data = $sourceModel->where('is_active', 1)->where('id', $id)->get()->toArray();
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
