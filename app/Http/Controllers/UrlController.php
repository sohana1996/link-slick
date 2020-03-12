<?php

namespace App\Http\Controllers;

use App\Models\Domains;
use App\Models\Media;
use App\Models\Sources;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Urls;
use App\Models\UrlMetaTag;
use App\Models\UrlMetaImage;
use App\Models\Url_categories;
use App\Models\Url_visits;
use App\Models\Utm_contents;
use DOMDocument;

class UrlController extends Controller
{
    public function generate(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'url_id' => 'required',
                'source_id' => 'required',
                'media_id' => 'required'
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            $url_id = (int)$input['url_id'];
            $url_name = '';
            $source_id = (int)$input['source_id'];
            $source_name = '';
            $media_id = (int)$input['media_id'];
            $media_name = '';
            $content_id = isset($input['content_id']) ? (int)$input['content_id'] : 0;
            $content_name = '';
            $domain_id = isset($input['domain_id']) ? (int)$input['domain_id'] : 0;
            $domain_name = '';
            $short = uniqid();
            //=====================
            // Is Parent Check
            //=====================
            if($source_id == 0){
                $urlCheck = new Urls();
                $check = $urlCheck->where('id', $url_id)
                    ->where('source_id', $source_id)
                    ->where('media_id', $media_id)
                    ->where('content_id', $content_id)
                    ->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($check) > 0){
                    $rv = array(
                        "status" => 2000,
                        "data" => $check[0],
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["Invalid Request. Please try again."]
                        ),
                    );
                    return json_encode($rv, true);
                }
            }
            //=====================
            // Existence Check
            //=====================
            $urlCheck = new Urls();
            $check = $urlCheck->where('parent_id', $url_id)
                ->where('source_id', $source_id)
                ->where('media_id', $media_id)
                ->where('content_id', $content_id)
                ->where('domain_id', $domain_id)
                ->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $readyUrl = $check[0];
                $readyUrl['domain'] = '';
                $domainModel = new Domains();
                $domain = $domainModel->select('title')->where('id', $readyUrl['domain_id'])->where('is_active', 1)->get()->toArray();
                if(count($domain) > 0){
                    $domain_name = $domain[0]['title'];
                    $domain_name = strtolower($domain_name);
                    $readyUrl['domain'] = $domain_name;
                }
                $rv = array(
                    "status" => 2000,
                    "data" => $readyUrl,
                );
                return json_encode($rv, true);
            } else {
                //=====================
                // Existence Parent Check
                //=====================
                $parentCheck = new Urls();
                $checkP = $parentCheck->where('id', $url_id)
                    ->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($checkP) > 0){
                    $parentData = $checkP[0];
                    //=====================
                    // Generate GET UTM
                    //=====================
                    $sourceModel = new Sources();
                    $source = $sourceModel->select('title')->where('id', $source_id)->where('is_active', 1)->get()->toArray();
                    if(count($source) > 0){
                        $source_name = $source[0]['title'];
                        $source_name = strtolower($source_name);
                        $source_name = str_replace(' ', '+',$source_name);
                    }

                    $mediaModel = new Media();
                    $media = $mediaModel->select('title')->where('id', $media_id)->where('is_active', 1)->get()->toArray();
                    if(count($media) > 0){
                        $media_name = $media[0]['title'];
                        $media_name = strtolower($media_name);
                        $media_name = str_replace(' ', '+',$media_name);
                    }

                    $contentModel = new Utm_contents();
                    $content = $contentModel->select('title')->where('id', $content_id)->where('is_active', 1)->get()->toArray();
                    if(count($content) > 0){
                        $content_name = $content[0]['title'];
                        $content_name = strtolower($content_name);
                        $content_name = str_replace(' ', '+',$content_name);
                    }

                    $domainModel = new Domains();
                    $domain = $domainModel->select('title')->where('id', $domain_id)->where('is_active', 1)->get()->toArray();
                    if(count($domain) > 0){
                        $domain_name = $domain[0]['title'];
                        $domain_name = strtolower($domain_name);
                    }

                    $url_name = $parentData['url'];
                    if($source_name != ''){
                        $url_name .= '?utm_source='.$source_name;
                        if($media_name != ''){
                            $url_name .= '&utm_medium='.$media_name;
                        }
                        if($content_name != ''){
                            $url_name .= '&utm_content='.$content_name;
                        }
                    }
                    //=====================
                    // Store Info
                    //=====================
                    $urlModel = new Urls();
                    $urlModel->user_id = $userId;
                    $urlModel->parent_id = $url_id;
                    $urlModel->source_id = $source_id;
                    $urlModel->content_id = $content_id;
                    $urlModel->domain_id = $domain_id;
                    $urlModel->media_id = $media_id;
                    $urlModel->title = '';
                    $urlModel->url = '';
                    $urlModel->url_android = '';
                    $urlModel->url_ios = '';
                    $urlModel->short = $short;
                    $urlModel->visit = 0;
                    $urlModel->is_active = 1;
                    $urlModel->created_at = Carbon::now();
                    if($urlModel->save()){
                        $retData = $urlModel->toArray();
                        $retData['domain'] = $domain_name;
                        $rv = array(
                            "status" => 2000,
                            "msg" => 'New url has been generated successfully.',
                            "data" => $retData,
                        );
                        return json_encode($rv, true);
                    }
                    else {
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
                            "error" => ["Invalid Request. Please try again."]
                        ),
                    );
                    return json_encode($rv, true);
                }
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
    public function generateWithDomain(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'url_id' => 'required',
                'domain_id' => 'required'
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            $url_id = (int)$input['url_id'];
            $url_name = '';

            $urlCheck = new Urls();
            $check = $urlCheck->where('id', $url_id)
                ->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $retData = $check[0];
                $source_id = (int)$retData['source_id'];
                $source_name = '';
                $media_id = (int)$retData['media_id'];
                $media_name = '';
                $content_id = (int)$retData['content_id'];
                $content_name = '';
            } else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ["Invalid Request. Please try again."]
                    ),
                );
                return json_encode($rv, true);
            }
            $domain_id = isset($input['domain_id']) ? (int)$input['domain_id'] : 0;
            $domain_name = '';
            $short = uniqid();
            //=====================
            // Is Parent Check
            //=====================
            if($domain_id == 0){
                $urlCheck = new Urls();
                $check = $urlCheck->where('id', $url_id)
                    ->where('source_id', $source_id)
                    ->where('media_id', $media_id)
                    ->where('content_id', $content_id)
                    ->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($check) > 0){
                    $rv = array(
                        "status" => 2000,
                        "data" => $check[0],
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["Invalid Request. Please try again."]
                        ),
                    );
                    return json_encode($rv, true);
                }
            }

            //=====================
            // Existence Check
            //=====================
            $urlCheck = new Urls();
            $check = $urlCheck->where('parent_id', $url_id)
                ->where('source_id', $source_id)
                ->where('media_id', $media_id)
                ->where('content_id', $content_id)
                ->where('domain_id', $domain_id)
                ->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $readyUrl = $check[0];
                $readyUrl['domain'] = '';
                $domainModel = new Domains();
                $domain = $domainModel->select('title')->where('id', $readyUrl['domain_id'])->where('is_active', 1)->get()->toArray();
                if(count($domain) > 0){
                    $domain_name = $domain[0]['title'];
                    $domain_name = strtolower($domain_name);
                    $readyUrl['domain'] = $domain_name;
                }
                $rv = array(
                    "status" => 2000,
                    "data" => $readyUrl,
                );
                return json_encode($rv, true);
            } else {
                //=====================
                // Existence Parent Check
                //=====================
                $parentCheck = new Urls();
                $checkP = $parentCheck->where('id', $url_id)
                    ->where('is_active', 1)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($checkP) > 0){
                    $parentData = $checkP[0];
                    //=====================
                    // Generate GET UTM
                    //=====================
                    $sourceModel = new Sources();
                    $source = $sourceModel->select('title')->where('id', $source_id)->where('is_active', 1)->get()->toArray();
                    if(count($source) > 0){
                        $source_name = $source[0]['title'];
                        $source_name = strtolower($source_name);
                        $source_name = str_replace(' ', '+',$source_name);
                    }

                    $mediaModel = new Media();
                    $media = $mediaModel->select('title')->where('id', $media_id)->where('is_active', 1)->get()->toArray();
                    if(count($media) > 0){
                        $media_name = $media[0]['title'];
                        $media_name = strtolower($media_name);
                        $media_name = str_replace(' ', '+',$media_name);
                    }

                    $contentModel = new Utm_contents();
                    $content = $contentModel->select('title')->where('id', $content_id)->where('is_active', 1)->get()->toArray();
                    if(count($content) > 0){
                        $content_name = $content[0]['title'];
                        $content_name = strtolower($content_name);
                        $content_name = str_replace(' ', '+',$content_name);
                    }

                    $domainModel = new Domains();
                    $domain = $domainModel->select('title')->where('id', $domain_id)->where('is_active', 1)->get()->toArray();
                    if(count($domain) > 0){
                        $domain_name = $domain[0]['title'];
                        $domain_name = strtolower($domain_name);
                    }

                    $url_name = $parentData['url'];
                    if($source_name != ''){
                        $url_name .= '?utm_source='.$source_name;
                        if($media_name != ''){
                            $url_name .= '&utm_medium='.$media_name;
                        }
                        if($content_name != ''){
                            $url_name .= '&utm_content='.$content_name;
                        }
                    }
                    //=====================
                    // Store Info
                    //=====================
                    $urlModel = new Urls();
                    $urlModel->user_id = $userId;
                    $urlModel->parent_id = $url_id;
                    $urlModel->source_id = $source_id;
                    $urlModel->content_id = $content_id;
                    $urlModel->domain_id = $domain_id;
                    $urlModel->media_id = $media_id;
                    $urlModel->title = '';
                    $urlModel->url = '';
                    $urlModel->url_android = '';
                    $urlModel->url_ios = '';
                    $urlModel->short = $short;
                    $urlModel->visit = 0;
                    $urlModel->is_active = 1;
                    $urlModel->created_at = Carbon::now();
                    if($urlModel->save()){
                        $retData = $urlModel->toArray();
                        $retData['domain'] = $domain_name;
                        $rv = array(
                            "status" => 2000,
                            "msg" => 'New url has been generated successfully.',
                            "data" => $retData,
                        );
                        return json_encode($rv, true);
                    }
                    else {
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
                            "error" => ["Invalid Request. Please try again."]
                        ),
                    );
                    return json_encode($rv, true);
                }
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
    public function store(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $input = $request->input();
            $validator = Validator::make($input, [
                'url' => 'required',
                'title' => 'required',
                'cat_id' => 'required',
            ]);
            if($validator->fails()){
                $rv = array(
                    "status" => 5000,
                    "data" => $validator->messages()
                );
                return json_encode($rv, true);
            }
            $title = $input['title'];
            $url = $input['url'];
            $url_android = isset($input['url_android']) ? $input['url_android'] : '';
            $url_ios = isset($input['url_ios']) ? $input['url_ios'] : '';
            $cat_id = $input['cat_id'];
            $short = uniqid();
            //=====================
            // Existence Check
            //=====================
            $urlCheck = new Urls();
            $check = $urlCheck->where('url', $url)->where('is_active', 1)
                ->where('user_id', $userId)->get()->toArray();
            if(count($check) > 0){
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ["New url already exist. Please try another one"]
                    ),
                );
                return json_encode($rv, true);
            }
            //=======================
            // Iframe embed Check
            //=======================
            if(isset($input['checkbox'])){
                $header = get_headers($url, 1);
                if(isset($header["X-Frame-Options"]) && $header["X-Frame-Options"] == 'DENY'){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["This url don't allow iframe embed"]
                        ),
                    );
                    return json_encode($rv, true);
                }
            }
            //=====================
            // Store Info
            //=====================
            $urlModel = new Urls();
            $urlModel->user_id = $userId;
            $urlModel->cat_id = $cat_id;
            $urlModel->title = $title;
            $urlModel->checkbox = isset($input['checkbox']) ? $input['checkbox'] : null;
            $urlModel->write_script = isset($input['write_script']) ? $input['write_script'] : null;
            $urlModel->url = $url;
            $urlModel->url_android = $url_android;
            $urlModel->url_ios = $url_ios;
            $urlModel->short = $short;
            $urlModel->visit = 0;
            $urlModel->is_active = 1;
            $urlModel->created_at = Carbon::now();
            if($urlModel->save()){
                $rv = array(
                    "status" => 2000,
                    "msg" => 'New url has been added successfully.',
                    "data" => $urlModel->toArray(),
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
                'url' => 'required',
                'title' => 'required',
                'cat_id' => 'required',
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
                $checkbox = isset($input['checkbox']) ? $input['checkbox'] : null;
                $write_script = isset($input['write_script']) ? $input['write_script'] : null;
                $url = $input['url'];
                $url_android = isset($input['url_android']) ? $input['url_android'] : '';
                $url_ios = isset($input['url_ios']) ? $input['url_ios'] : '';
                $cat_id = $input['cat_id'];
                //=======================
                // Permission Check
                //=======================
                $pemCheck = new Urls();
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
                $urlCheck = new Urls();
                $check = $urlCheck->where('url', $url)->where('is_active', 1)
                    ->where('id','!=', $id)
                    ->where('user_id', $userId)->get()->toArray();
                if(count($check) > 0){
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ["New url already exist. Please try another one"]
                        ),
                    );
                    return json_encode($rv, true);
                }
                //=======================
                // Iframe embed Check
                //=======================
                if(isset($input['checkbox'])){
                    $header = get_headers($url, 1);
                    if(isset($header["X-Frame-Options"]) && $header["X-Frame-Options"] == 'DENY'){
                        $rv = array(
                            "status" => 5000,
                            "data" => array(
                                "error" => ["This url don't allow iframe embed"]
                            ),
                        );
                        return json_encode($rv, true);
                    }
                }
                //=======================
                // Update Info
                //=======================
                $urlModel = new Urls();
                $urlModel->where('id', $id)
                    ->update([
                        "title" => $title,
                        "checkbox" => $checkbox,
                        "write_script" => $write_script,
                        "url" => $url,
                        "url_android" => $url_android,
                        "url_ios" => $url_ios,
                        "cat_id" => $cat_id,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Url has been updated successfully.'
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
                $pemCheck = new Urls();
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
                $urlModel = new Urls();
                $urlModel->where('id', $id)
                    ->update([
                        "is_active" => 0,
                        "updated_at" => Carbon::now(),
                    ]);
                $rv = array(
                    "status" => 2000,
                    "msg" => 'Url has been removed successfully.'
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
    public function getAllUrl(Request $request){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $urlModel = new Urls();
            $data = $urlModel->where('is_active', 1)
                ->where('user_id', $userId)
                ->where('parent_id', 0)
                ->orderBy('title', 'asc')->get()->toArray();
            if(count($data) > 0){
                $rv = array();
                foreach ($data as $url){
                    $urlVisit = new Url_visits();
                    $yesterday = $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                        ->where('created_at', '=', Carbon::yesterday())->count();
                    $today = $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                        ->where('created_at', '>=', Carbon::today())->count();
                    $url['today_visit'] = $today;
                    $url['yesterday_visit'] = $yesterday;
                    $rv[] = $url;
                }
                $rv = array(
                    "status" => 2000,
                    "data" => $rv
                );
                return json_encode($rv, true);
            }
            else {
                $rv = array(
                    "status" => 5000,
                    "data" => array(
                        "error" => ['No more url to preview']
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
    public function getUrl(Request $request, $pageNo){
        if(auth()->check()){
            $userInfo = auth()->user();
            $userId = $userInfo->id;
            $pageNo = (int)$pageNo;
            if($pageNo > 0){
                $limit = 10;
                $offset = ($pageNo - 1) * $limit;
                $urlModel = new Urls();
                $data = $urlModel->where('is_active', 1)
                    ->where('user_id', $userId)
                    ->where('parent_id', 0)
                    ->take($limit)
                    ->orderBy('id', 'desc')
                    ->skip($offset)->get()->toArray();
                if(count($data) > 0){
                    $rv = array();
                    foreach ($data as $url){
                        $eachUrl = $this->visitFrame($url['id']);
                        $rv[] = $eachUrl;
                    }
                    $rv = array(
                        "status" => 2000,
                        "data" => $rv
                    );
                    return json_encode($rv, true);
                } else {
                    $rv = array(
                        "status" => 5000,
                        "data" => array(
                            "error" => ['No more url to preview']
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
                $urlModel = new Urls();
                $data = $urlModel->where('is_active', 1)->where('id', $id)->get()->toArray();
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
                            "error" => ['No more url to preview']
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
    public function urlTo(Request $request, $short){
        $machine = $_SERVER['HTTP_USER_AGENT'];
        $androidCheck = '/Andriod/';
//        $macCheck = '/Macintosh/';
        $iphoneCheck = '/iPhone/';

        preg_match($androidCheck, $machine, $Android, PREG_OFFSET_CAPTURE, 3);
//        preg_match($macCheck, $machine, $iOS, PREG_OFFSET_CAPTURE, 3);
//        if(count($iOS) == 0){
            preg_match($iphoneCheck, $machine, $iOS, PREG_OFFSET_CAPTURE, 3);
//        }

        $urlModel = new Urls();
        $url = $urlModel
            ->select('urls.*','sources.title as source','media.title as media','utm_contents.title as content')
            ->where('urls.short', $short)
            ->where('urls.is_active', 1)
            ->leftJoin('sources', 'urls.source_id', '=', 'sources.id')
            ->leftJoin('media', 'urls.media_id', '=', 'media.id')
            ->leftJoin('utm_contents', 'urls.content_id', '=', 'utm_contents.id')
            ->get()->toArray();
        if(count($url) > 0){
            $userModel = new User();
            $userInfo = $userModel->select('tzs.title')
                ->where('users.id', $url[0]['user_id'])
                ->leftJoin('tzs', 'users.tz', '=', 'tzs.id')
                ->get();
            if(count($userInfo) > 0 && $userInfo[0]->title != null){
                date_default_timezone_set($userInfo[0]->title);
            }
            $count = (int)$url[0]['visit'];
            $urlSave = new Urls();
            $urlSave->where('id', $url[0]['id'])->update(['visit' => 0]);

            $urlVisit = new Url_visits();
            $urlVisit->url_id = $url[0]['id'];
            $urlVisit->is_active = 1;
            $urlVisit->created_at = Carbon::now();
            $urlVisit->save();

            if($url[0]['parent_id'] > 0){
                $parent_id = $url[0]['parent_id'];
                $parentModel = new Urls();
                $parent = $parentModel->where('id', $parent_id)->where('is_active', 1)->get()->toArray();
                if(count($parent) > 0){
                    if(count($Android) > 0){
                        $redirectUrl = $parent[0]['url_android'];
                        if($redirectUrl == ''){
                            $redirectUrl = $parent[0]['url'];
                        }
                    }
                    elseif(count($iOS) > 0){
                        $redirectUrl = $parent[0]['url_ios'];
                        if($redirectUrl == ''){
                            $redirectUrl = $parent[0]['url'];
                        }
                    }
                    else{
                        $redirectUrl = $parent[0]['url'];
                    }
                    $url = $url[0];
                    if($url['source_id'] > 0){
                        $redirectUrl .= '?utm_source='.str_replace(' ', '+', $url['source']);
                        if($url['media_id'] > 0){
                            $redirectUrl .= '&utm_medium='.str_replace(' ', '+', $url['media']);
                        }
                        if($url['content_id'] > 0){
                            $redirectUrl .= '&utm_content='.str_replace(' ', '+', $url['content']);
                        }
                    }
                    if($url['checkbox'] == 1){
                        return $this->preView($url['short']);
                    }
                    else{
                        return redirect($redirectUrl);
                    }
                } else {
                    return abort(404);
                }
            } else {
                if(count($Android) > 0){
                    $redirectUrl = $url[0]['url_android'];
                    if($redirectUrl == ''){
                        $redirectUrl = $url[0]['url'];
                    }
                }
                elseif(count($iOS) > 0){
                    $redirectUrl = $url[0]['url_ios'];
                    if($redirectUrl == ''){
                        $redirectUrl = $url[0]['url'];
                    }
                }
                else{
                    $redirectUrl = $url[0]['url'];
                }
                if($url[0]['checkbox'] == 1){
                    return $this->preView($url[0]['short']);
                }
                else{
                    return redirect($redirectUrl);
                }
            }
        } else {
            return view('404');
        }
    }

    public function singleUrlReport(Request $request, $id)
    {
        if(auth()->check()) {
            $userInfo = auth()->user();
            $link = $this->visitFrame($id);
            if(count($link) > 0 && $userInfo->id == $link['user_id'] && $link['parent_id'] == 0){
                $child = [];
                array_unshift($link['child'], $link['id']);
                if(count($link['child']) > 0){
                    foreach ($link['child'] as $c){
                        $eachChild = $this->visitFrame($c, 1);
                        $child[] = $eachChild;
                    }
                }

                $urlModel = new Urls();
                $sources = $urlModel->select('source_id')
                    ->where('is_active', 1)
                    ->where('parent_id', $link['id'])
                    ->get()->toArray();
                $sources = array_column($sources, 'source_id');
                $sources = array_unique($sources);
                $sourceInfo = [];
                $woSource = $this->visitFrame($link['id'], 1);
                $sourceInfo[] = array(
                    "id"=>$woSource['source_id'],
                    "title"=>$woSource['source'],
                    "visit"=>$woSource['visit'],
                    "report"=>$woSource['report']
                );
                foreach ($sources as $sN){
                    $eachSourcesInit = $urlModel->select('id')
                        ->where('is_active', 1)
                        ->where('source_id', $sN)
                        ->where('parent_id', $link['id'])
                        ->get()->toArray();
                    $eachSourcesInit = array_column($eachSourcesInit, 'id');
                    $eachSourcesInit = array_unique($eachSourcesInit);
                    $sourceModel = new Sources();
                    $retInfo = $sourceModel->where('id',$sN)->get()->toArray();
                    if(count($retInfo) > 0){
                        $eachSourceInfo['title'] = $retInfo[0]['title'];
                    }

                    $eachSourceInfo['id'] = $sN;
                    $visitModel = new Url_visits();
                    $eachSourceInfo['visit'] = $visitModel->whereIn('url_id', $eachSourcesInit)->count();
                    $eachSourceInfo['report'] = array(
                        0 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-6 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-5 days")))
                            ->count(),
                        1 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-5 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-4 days")))
                            ->count(),
                        2 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-4 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-3 days")))
                            ->count(),
                        3 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-3 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-2 days")))
                            ->count(),
                        4 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-2 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-1 days")))
                            ->count(),
                        5 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-1 days")))
                            ->where('created_at','<', date('Y-m-d 00:00:00'))
                            ->count(),
                        6 => $visitModel->whereIn('url_id', $eachSourcesInit)
                            ->where('created_at','>=', date('Y-m-d 00:00:00'))->count(),
                    );
                    $sourceInfo[] = $eachSourceInfo;
                }

//                dd($sourceInfo);
//
//                $sourceInfo[] = $this->visitFrame($link['id'], 1);

                $rv = array(
                    "page" => array(
                        "feedLoad" => 'links',
                        "title" => 'Link Report'
                    ),
                    "url" => $link,
                    "child" => $child,
                    "sourceInfo" => $sourceInfo,
                );
//                dd($rv);
                return view('singleReport')->with($rv);
            } else {
                return abort(404);
            }
        } else {
            return redirect('/');
        }
    }

    public function getTotalVisit(){
        $userInfo = auth()->user();
        $urlModel = new Urls();
        $myUrls = $urlModel
            ->select('id')
            ->where('is_active', 1)
            ->where('user_id', $userInfo['id'])
            ->get()->toArray();
        $myUrls = array_column($myUrls, 'id');
        $visitModel = new Url_visits();
        $total_visit = $visitModel
            ->whereIn('url_id', $myUrls)
            ->count();
        $planInfo = array(
            'visit' => 1000
        );
        $percentVisit = ($total_visit / $planInfo['visit']) * 100;
        $rv = array(
            'total_visit' => $total_visit,
            'percentVisit' => $percentVisit,
            'planInfo' => $planInfo
        );
        return json_encode($rv, true);
    }
    public function visitFrame($id, $child=null){
        $userInfo = auth()->user();
        $userModel = new User();
        $tz = $userModel->select('tzs.title')
            ->where('users.id', $userInfo->id)
            ->leftJoin('tzs', 'users.tz', '=', 'tzs.id')
            ->get();
        if(count($tz) > 0 && $tz[0]->title != null){
            date_default_timezone_set($tz[0]->title);
        }

        $urlModel = new Urls();
        $link = $urlModel
            ->select('urls.*','sources.title as source','media.title as media','domains.title as domain','utm_contents.title as content'
                ,'url_categories.title as category')
            ->where('urls.id', $id)
            ->where('urls.is_active', 1)
            ->leftJoin('sources', 'urls.source_id', '=', 'sources.id')
            ->leftJoin('media', 'urls.media_id', '=', 'media.id')
            ->leftJoin('domains', 'urls.domain_id', '=', 'domains.id')
            ->leftJoin('url_categories', 'urls.cat_id', '=', 'url_categories.id')
            ->leftJoin('utm_contents', 'urls.content_id', '=', 'utm_contents.id')
            ->get()->toArray();;
        if(count($link) > 0){
            $link = $link[0];
            $link['parent'] = array();
            $link['child'] = array();
            $idsVisit = array();
            if($link['parent_id'] == 0 && $child == null){
                $urlModel = new Urls();
                $childRf = $urlModel->select('id')->where('parent_id', $link['id'])->get()->toArray();
                $childRf = array_column($childRf, 'id');
                $link['child'] = $childRf;
                $idsVisit = $childRf;
            }
            $idsVisit[] = $link['id'];


            $visitModel = new Url_visits();
            $link['visit'] = $visitModel->whereIn('url_id', $idsVisit)->count();
            $link['report'] = array(
                0 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-6 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-5 days")))
                    ->count(),
                1 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-5 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-4 days")))
                    ->count(),
                2 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-4 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-3 days")))
                    ->count(),
                3 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-3 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-2 days")))
                    ->count(),
                4 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-2 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00',strtotime("-1 days")))
                    ->count(),
                5 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00',strtotime("-1 days")))
                    ->where('created_at','<', date('Y-m-d 00:00:00'))
                    ->count(),
                6 => $visitModel->whereIn('url_id', $idsVisit)
                    ->where('created_at','>=', date('Y-m-d 00:00:00'))->count(),
            );
        }
        return $link;
    }
    public function preView($short){

        $findShort = Urls::where('short', $short)->first();
        if($findShort == null){
            abort('404');
        }
        $findMeta = UrlMetaTag::where('url_id',$findShort->id)->get();
        $parse = parse_url($findShort->url);

        $metaTags = [];
        $storeImg = [];
        if(count($findMeta) == 0){
            $html = '';
            try{
                $html = file_get_contents( $findShort->url );
            } catch (\Exception $e){}
    
            $dom = new domDocument;
            @$dom->loadHTML($html);
            $dom->preserveWhiteSpace = false;
            // $images = $dom->getElementsByTagName('img');
            $metaData = $dom->getElementsByTagName('meta');
            // $storeImg = array();
            $tags = [];
            foreach ($metaData as $meta)
            {
                $name = $meta->getAttribute('name');
                $content = $meta->getAttribute('content');
                $property = $meta->getAttribute('property');
                $metaTags[] = array(
                    'type' => $name != "" ? 'name' : 'property',
                    'name' => $name != "" ? $name : $property,
                    'content' => $content,
                );
                UrlMetaTag::create([
                    'url_id' => $findShort->id,
                    'type' => $name != "" ? 'name' : 'property',
                    'name' => $name != "" ? $name : $property,
                    'content' => $content,
                ]);
            }
        }
        else{
            foreach ($findMeta as $meta)
            {
                $metaTags[] = array(
                    'type' => $meta->type,
                    'name' => $meta->name,
                    'content' => $meta->content,
                    
                );
            }
        }
        $findImg = UrlMetaImage::where('url_id',$findShort->id)->orderBy('width', 'desc')->get();
        if(count($findImg) == 0){
            $html = '';
            try{
                $html = file_get_contents( $findShort->url );
            } catch (\Exception $e){}
            $parse = parse_url($findShort->url);
    
            $dom = new domDocument;
            @$dom->loadHTML($html);
            $dom->preserveWhiteSpace = false;
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image)
            {
                $img = $image->getAttribute('src');
                $check = @get_headers($img);
                if($check == false){
                    $img = '//'.$parse['host'].$img;
                    $check = @get_headers($img);
                }
                if($check != false){
                    list($width,$height) = @getimagesize($img);
                    if($width>=200 || $height>=200){
                        $storeImg[] = $img;
                        UrlMetaImage::create([
                            'url_id' => $findShort->id,
                            'width' => $width,
                            'link' =>$img,
                        ]);
                    } 
                }
            }
        }
        else{
        foreach ($findImg as $image)
        {
            $storeImg[] = $image->link;
        }
    }

        $doc = new \DOMDocument();
        @$doc->loadHTML( file_get_contents( $findShort->url ) );
        $xpt = new \DOMXPath( $doc );


        $ddt = array();
        $title = $xpt->query("//title");
        if(isset($title[0])){
            $ddt['title'] = $xpt->query("//title")->item(0)->nodeValue;
        } else {
            $ddt['title'] = $parse['host'];
        }
        $ddt['favicon'] = "https://www.google.com/s2/favicons?domain=".$parse['host'];

        return view('urlPreView', compact('findShort', 'metaTags', 'ddt', 'storeImg'));
    }


}
