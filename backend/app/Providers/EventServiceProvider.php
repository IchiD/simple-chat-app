<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\PasswordReset;
use App\Listeners\SendPasswordResetSuccessNotification;

class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    PasswordReset::class => [
      SendPasswordResetSuccessNotification::class,
    ],
  ];

  /**
   * Register any events for your application.
   */
  public function boot()
  {
    parent::boot();
  }
}
