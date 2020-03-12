<?php

namespace Laravel\Spark\Http\Controllers\Settings\Profile;

use Illuminate\Http\Request;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation;
use Laravel\Spark\User;

class ContactInformationController extends Controller
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
     * Update the user's contact information settings.
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        if (auth()->check()) {
            $userInfo = auth()->user();
            $input = $request->all();
            if (isset($input['tz']) && (int)$input['tz'] > 0) {
                $users = new User();
                $users->where('id', $userInfo['id'])->update(['tz'=> (int)$input['tz']]);
            } else {
                return json_encode(["status" => 5000], true);
            }
            $this->interaction(
                $request, UpdateContactInformation::class,
                [$request->user(), $request->all()]
            );
        }
    }
}
