<?php

namespace App\Console\Commands;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use EmailHandler;

class RenewProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gem:renew-properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command renews properties.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $properties = DB::table('re_properties')
            ->where('moderation_status', '=', ModerationStatusEnum::APPROVED)
            ->where('auto_renew', '=', 1)
            ->get();

        $total = 0;

        foreach($properties as $property) {
            if (Carbon::parse($property->expire_date)->isPast()) {
                if($property->member_id) { //If the member has to pay
                    $member= DB::table('members')->where('id', '=', $property->member_id)->first();
                    if ($member && $member->credits > 0) {
                        DB::table('members')->where('id', $member->id)->decrement('credits', 1);
        
                        $newExpireDate = Carbon::today()->addDays(45)->toDateString();
        
                        DB::table('re_properties')->where('id', $property->id)->update([
                            'expire_date' => $newExpireDate,
                        ]);

                        $total++;

                        //Send Email over here
                        $variables = [
                            'name' => 'Name',
                            'property_url' => 'Property Url',
                            'title' => 'Title',
                            'dashboard_url' => 'Dashboard Url'
                        ];

                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $member->full_name,
                                'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                                'title' => $property->name,
                                'dashboard_url' => route('member.dashboard'),
                            ])
                            ->sendUsingTemplate('renew', $member->email, [], false, 'plugins', 'GEM - Property Renewd');

                    } else { //Email them if the credits are not available to topup their account
                        $variables = [
                            'name' => 'Name',
                            'property_url' => 'Property Url',
                            'title' => 'Title',
                            'credits_url' => 'Credits Url'
                        ];

                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $member->full_name,
                                'property_url' => route('public.member.properties.edit', ['id' => $property->id]),
                                'title' => $property->name,
                                'credits_url' => route('public.member.packages')
                            ])
                            ->sendUsingTemplate('renew', $member->email, [], false, 'plugins', 'GEM - Property Expired');
                    }
                } else if($property->author_id) { //if the property is owned by agent
                    $agent= DB::table('re_accounts')->where('id', '=', $property->author_id)->first();
                    if ($agent && $agent->credits > 0) {
                        DB::table('re_accounts')->where('id', $agent->id)->decrement('credits', 1);
        
                        $newExpireDate = Carbon::today()->addDays(45)->toDateString();
        
                        DB::table('re_properties')->where('id', $property->id)->update([
                            'expire_date' => $newExpireDate,
                        ]);

                        $total++;

                        //Send Email over here
                        $variables = [
                            'name' => 'Name',
                            'property_url' => 'Property Url',
                            'title' => 'Title',
                            'dashboard_url' => 'Dashboard Url'
                        ];

                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $agent->first_name . ' ' . $agent->last_name,
                                'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                                'title' => $property->name,
                                'dashboard_url' => route('public.account.dashboard'),
                            ])
                            ->sendUsingTemplate('renew', $agent->email, [], false, 'plugins', 'GEM - Property Renewd');

                    } else { //Email them if the credits are not available to topup their account
                        $variables = [
                            'name' => 'Name',
                            'property_url' => 'Property Url',
                            'title' => 'Title',
                            'credits_url' => 'Credits Url'
                        ];

                        EmailHandler::setModule('real-estate')
                            ->addVariables($variables)
                            ->setVariableValues([
                                'name' => $agent->first_name . ' ' . $agent->last_name,
                                'property_url' => route('public.account.properties.edit', ['property' => $property->id]),
                                'title' => $property->name,
                                'credits_url' => route('public.account.packages')
                            ])
                            ->sendUsingTemplate('renew', $member->email, [], false, 'plugins', 'GEM - Property Expired');
                    }
                }
            }
        }

        
        $this->info($total . ' properties have been renewd.');
    }
}
