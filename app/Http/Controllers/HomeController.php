<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Models\Urls;
use App\Models\Url_visits;
use Carbon\Carbon;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // $this->middleware('subscribed');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'home',
                "title" => 'Dashboard'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('home')->with($rv);
    }
    public function renderLinks(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'links',
                "title" => 'Destination Links'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('links')->with($rv);
    }
    public function singleUrlReport(Request $request, $id)
    {
        $getTotalVisit = $this->getTotalVisit();
        $urlModel = new Urls();
        $url = $urlModel->select(
            'urls.*',
            'sources.title as source',
            'domains.title as domain',
            'media.title as media',
            'url_categories.title as category')
            ->where('urls.id', $id)
            ->where('urls.is_active', 1)
            ->leftJoin('sources', 'urls.source_id', '=', 'sources.id')
            ->leftJoin('domains', 'urls.domain_id', '=', 'domains.id')
            ->leftJoin('media', 'urls.media_id', '=', 'media.id')
            ->leftJoin('url_categories', 'urls.cat_id', '=', 'url_categories.id')
            ->get()->toArray();
        if(count($url) > 0){
            $url = $url[0];
            $urlVisit = new Url_visits();
            $yesterday = $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-1 days")))
                ->where('created_at', '<', date('Y-m-d 00:00:00'))
                ->count();
            $today = $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                ->where('created_at', '>=', date('Y-m-d 00:00:00'))
                ->count();
            $totalVisit = $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                ->count();
            $weekReport =  array(
                0 => $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                    ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-6 days")))
                    ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-5 days")))
                    ->count(),
                1 => $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                    ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-5 days")))
                    ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-4 days")))
                    ->count(),
                2 => $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                    ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-4 days")))
                    ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-3 days")))
                    ->count(),
                3 => $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                    ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-3 days")))
                    ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-2 days")))
                    ->count(),
                4 => $urlVisit->where('is_active', 1)->where('url_id', $url['id'])
                    ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-2 days")))
                    ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-1 days")))
                    ->count(),
                5 => $yesterday,
                6 => $today,
            );
            $url['visit'] = $totalVisit;
            $url['today_visit'] = $today;
            $url['yesterday_visit'] = $yesterday;
            $url['weekReport'] = $weekReport;
            $totalUrl = $url;

            $urlChildModel = new Urls();
            $child = $urlChildModel
                ->select(
                    'urls.*',
                    'sources.title as source',
                    'domains.title as domain',
                    'media.title as media',
                    'url_categories.title as category')
                ->where('urls.parent_id', $id)
                ->where('urls.is_active', 1)
                ->leftJoin('sources', 'urls.source_id', '=', 'sources.id')
                ->leftJoin('domains', 'urls.domain_id', '=', 'domains.id')
                ->leftJoin('media', 'urls.media_id', '=', 'media.id')
                ->leftJoin('url_categories', 'urls.cat_id', '=', 'url_categories.id')
                ->get()->toArray();
            $childRv = array();
            $source_ids = array();
            $sourceRepo = array();
            if(count($child) > 0){
                foreach ($child as $u){
                    $source_ids[] = $u['source_id'];
                    $urlVisit = new Url_visits();
                    $yesterday = $urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                        ->where('created_at', '=', Carbon::yesterday())->count();
                    $today = $urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                        ->where('created_at', '>=', Carbon::today())->count();
                    $totalVisit = $urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                        ->count();

                    $totalUrl['weekReport'][0] = $totalUrl['weekReport'][0] + ($urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                            ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-6 days")))
                            ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-5 days")))
                            ->count());
                    $totalUrl['weekReport'][1] = $totalUrl['weekReport'][1] + ($urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                            ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-5 days")))
                            ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-4 days")))
                            ->count());
                    $totalUrl['weekReport'][2] = $totalUrl['weekReport'][2] + ($urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                            ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-4 days")))
                            ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-3 days")))
                            ->count());
                    $totalUrl['weekReport'][3] = $totalUrl['weekReport'][3] + ($urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                            ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-3 days")))
                            ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-2 days")))
                            ->count());
                    $totalUrl['weekReport'][4] = $totalUrl['weekReport'][4] + ($urlVisit->where('is_active', 1)->where('url_id', $u['id'])
                            ->where('created_at', '>=', date('Y-m-d 00:00:00',strtotime("-2 days")))
                            ->where('created_at', '<', date('Y-m-d 00:00:00',strtotime("-1 days")))
                            ->count());
                    $totalUrl['weekReport'][5] = $totalUrl['weekReport'][5] + $yesterday;
                    $totalUrl['weekReport'][6] = $totalUrl['weekReport'][6] + $today;


                    $totalUrl['visit'] = $totalUrl['visit']+$today;
                    $totalUrl['today_visit'] = $totalUrl['today_visit']+$today;
                    $totalUrl['yesterday_visit'] = $totalUrl['yesterday_visit']+$today;
                    $u['visit'] = $totalVisit;
                    $u['today_visit'] = $today;
                    $u['yesterday_visit'] = $yesterday;
                    $childRv[] = $u;
                }
                $source_ids = array_map("unserialize", array_unique(array_map("serialize", $source_ids)));
                foreach ($source_ids as $sId){
                    $eachSourceRepo = array(
                        "id" => $sId,
                        "title" => '',
                        "today_visit" => 0,
                        "yesterday_visit" => 0,
                        "visit" => 0,
                    );
                    foreach ($childRv as $repo){
                        if($repo['source_id'] == $sId){
                            $eachSourceRepo['title'] = $repo['source'];
                            $eachSourceRepo['today_visit'] = $eachSourceRepo['today_visit'] + $repo['today_visit'];
                            $eachSourceRepo['yesterday_visit'] = $eachSourceRepo['yesterday_visit'] + $repo['yesterday_visit'];
                            $eachSourceRepo['visit'] = $eachSourceRepo['visit'] + $repo['visit'];
                        }
                    }
                    $sourceRepo[] = $eachSourceRepo;
                }
            }
            array_unshift($childRv, $url);
//            $childRv[]= $url;
//            dd($url, $childRv);
            $rv = array(
                "page" => array(
                    "feedLoad" => 'links',
                    "title" => 'Link Report'
                ),
                'total_visit' => $getTotalVisit['total_visit'],
                'percentVisit' => $getTotalVisit['percentVisit'],
                'planInfo' => $getTotalVisit['planInfo'],
                "url" => $totalUrl,
                "child" => $childRv,
                "sourceInfo" => $sourceRepo,
            );
            return view('singleReport')->with($rv);
        } else {
            return abort(404);
        }
    }
    public function renderSources(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'sources',
                "title" => 'Sources'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('assets.sources')->with($rv);
    }
    public function renderMedia(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'media',
                "title" => 'Medium'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('assets.media')->with($rv);
    }
    public function renderContent(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'content',
                "title" => 'Contents'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('assets.content')->with($rv);
    }
    public function renderDomains(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'domains',
                "title" => 'Custom Domains'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('assets.domains')->with($rv);
    }
    public function renderCategory(Request $request)
    {
        $getTotalVisit = $this->getTotalVisit();
        $rv = array(
            "page" => array(
                "feedLoad" => 'category',
                "title" => 'Category'
            ),
            'total_visit' => $getTotalVisit['total_visit'],
            'percentVisit' => $getTotalVisit['percentVisit'],
            'planInfo' => $getTotalVisit['planInfo']
        );
        return view('assets.category')->with($rv);
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
        return $rv;
    }
}
