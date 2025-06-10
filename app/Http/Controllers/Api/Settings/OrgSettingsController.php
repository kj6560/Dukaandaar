<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
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
                dd($settings);
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
}
