<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\OrgSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrgSettingsController extends Controller
{
    public function updateOrgSettings(Request $request)
    {
        $data = $request->all();
        $key = $data['set_key'];
        $value = $data['set_value'];
        $org_id = Auth::user()->org_id;

        if (empty($key) && empty($value)) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'missing settings key and missing settings value.Default values will be used!',
                'data' => []
            ], 422);
        } else if (empty($key)) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'missing settings key!',
                'data' => []
            ], 200);
        } else if (empty($value)) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'Missing Value!',
                'data' => []
            ], 200);
        }

        if (!empty($key)) {
            $settings = OrgSettings::where('set_key', $key)->where('org_id', $org_id)->first();
            if (empty($settings->id)) {
                $settings = new OrgSettings();
                $settings->org_id = $org_id;
            }
            $settings->set_key = $key;
            $settings->set_value = $value;
            $settings->is_active = $request->is_active ?? 1;
            if ($settings->save()) {
                return response()->json([
                    'statusCode' => 200,
                    'message' => 'Settings updated!',
                    'data' => []
                ], 200);
            }
        }
        return response()->json([
            'statusCode' => 400,
            'message' => 'Some Error occured!',
            'data' => []
        ], 400);
    }
    public function fetchOrgSettings(Request $request)
    {
        $org_id = Auth::user()->org_id;
        $org_settings = OrgSettings::where('org_id', $org_id)->get();
        $formatted = $org_settings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'org_id' => $setting->org_id,
                'settings' => [
                    [
                        $setting->set_key => $setting->set_value,
                        'is_active' => $setting->is_active
                    ]
                ]
            ];
        });
        return response()->json([
            'statusCode' => 200,
            'message' => 'Settings fetched successfuly!',
            'data' => $formatted
        ], 200);
    }
    public function fetchCurrencies(Request $request)
    {
        $currencies = Currency::where('is_active', 1)->get();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Currencies fetched successfuly!',
            'data' => $currencies
        ], 200);
    }
}
