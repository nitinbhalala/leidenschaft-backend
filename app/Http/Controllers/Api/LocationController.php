<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Http\Controllers\Api\BaseController;

class LocationController extends BaseController
{
    public function countries()
    {
        $countries = Country::orderBy('name')->get();

        return $this->success($countries, "Countries fetched successfully");
    }

    public function states($country_id)
    {
        $states = State::where('country_id', $country_id)->orderBy('name')->get();

        return $this->success($states, "States fetched successfully");
    }

    public function cities($state_id)
    {
        $cities = City::where('state_id', $state_id)->orderBy('name')->get();

        return $this->success($cities, "Cities fetched successfully");
    }
}
