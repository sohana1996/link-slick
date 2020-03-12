<?php

namespace Laravel\Spark\Http\Controllers\Settings;

use Laravel\Spark\Http\Controllers\Controller;
use App\Models\Tz;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the settings dashboard.
     *
     * @return Response
     */
    public function show()
    {
        $tz = new Tz();
        $tzList = $tz->where('is_active', 1)->get()->toArray();
        return view('spark::settings')->with(['timezone'=>$tzList]);
    }
}
