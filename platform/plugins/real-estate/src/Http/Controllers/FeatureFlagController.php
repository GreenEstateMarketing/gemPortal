<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\RealEstate\Supports\FeatureFlag;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Http\Request;

class FeatureFlagController extends BaseController
{
    public function index()
    {
        page_title()->setTitle('Feature Flags');

        return view('plugins/real-estate::feature-flags.index', ['flags' => FeatureFlag::all()]);
    }

    public function update(Request $request, BaseHttpResponse $response, SettingStore $settingStore)
    {
        foreach (array_keys(FeatureFlag::all()) as $key) {
            $settingStore->set('feature_flag_' . $key, $request->boolean('feature_flag_' . $key));
        }

        $settingStore->save();

        return $response
            ->setPreviousUrl(route('real-estate.feature-flags'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
