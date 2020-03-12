<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Your Company',
        'product' => 'Your Product',
        'street' => 'PO Box 111',
        'location' => 'Your Town, NY 12345',
        'phone' => '555-555-5555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'hoomanmar2002@yahoo.com'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::useStripe()->noCardUpFront()->trialDays(10);

        Spark::freePlan()
            ->features([
                'Free 1000 traffics for your shortened Link',
//                'Second One',
//                'Third One'
            ]);

        Spark::plan('Basic', 'LINKSLICKBASIC')
            ->price(19)
            ->trialDays(14)
            ->features([
                'Free 10000 traffics for your shortened Link',
//                'Second One',
//                'Third One'
            ]);
/*        Spark::plan('Pro', 'provider-id-pro')
            ->price(20)
            ->trialDays(5)
            ->features([
                'Free 15000 traffics for your shortened Link',
//                'Second One',
//                'Third One'
            ]);
        Spark::plan('Ultimate', 'provider-id-ultra')
            ->price(50)
            ->features([
                'Unlimited traffics for your shortened Link',
//                'Second One',
//                'Third One'
            ]);*/
    }
}
