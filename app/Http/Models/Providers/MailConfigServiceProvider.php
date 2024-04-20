<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Http\Models\Setting;
use Config;
class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $emailServicesEmail = Setting::where('perimeter', 'smtp_email')->first();
        $emailServicesPassword = Setting::where('perimeter', 'smtp_password')->first();
        $emailServicesFromEmail = Setting::where('perimeter', 'smtp_from_email')->first();
        $emailServicesFromName = Setting::where('perimeter', 'smtp_from_name')->first();

        if ($emailServicesEmail && $emailServicesPassword) {
            $config = array(
                'driver'     => 'smtp',
                'host'       => 'smtp.gmail.com',
                'port'       => '465',
                'username'   => $emailServicesEmail->value,
                'password'   => $emailServicesPassword->value,
                'encryption' => 'ssl',
                'from'       => array('address' => $emailServicesFromEmail->value, 'name' => $emailServicesFromName->value),
                'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,'sendmail'   => '/usr/sbin/sendmail -bs',
                'pretend'    => false,
            );

            Config::set('mail', $config);
        }
    }
}
