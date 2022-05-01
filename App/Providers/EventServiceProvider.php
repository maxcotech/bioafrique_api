<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CourseLessonPosted::class => [
            SendNewLessonPushNotification::class
        ],
        QuestionPosted::class => [
            SendNewQuestionPushNotification::class
        ],
        AnswerPosted::class => [
            SendNewAnswerPushNotification::class
        ],
        NewPayment::class => [
            SendNewPaymentPushNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
