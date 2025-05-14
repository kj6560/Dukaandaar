<?php

namespace App\Http\Controllers\Api\AppContact;

use App\Http\Controllers\Controller;
use App\Models\AppContact;
use App\Models\AppContactResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function contactFromApp(Request $request)
    {
        $data = $request->all();
        $name = $data["name"];
        $email = $data["email"];
        $message = $data["message"];
        $user_id = $data["user_id"];
        $appContact = new AppContact();
        $appContact->user_id = $user_id;
        $appContact->name = $name;
        $appContact->email = $email;
        $appContact->message = $message;
        $appContact->query_status = 1;
        if ($appContact->save()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'We have received your request.You will hear from us soon!',
                'data' => []
            ], 200);
        }
        return response()->json([
            'statusCode' => 202,
            'message' => 'You don\'t have an active subscription. Plz contact admin',
            'data' => []
        ], 202);
    }
    public function fetchContactResponses(Request $request)
    {
        $user_id = $request->user_id;
        if (empty($user_id)) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Org id is required',
                'data' => []
            ], 202);
        }
        $appContacts = AppContactResponse::join('app_contacts', 'app_contact_responses.request_id', '=', 'app_contacts.id')
            ->join('users', 'users.id', '=', 'app_contacts.user_id')
            ->select('app_contacts.')
            ->where('app_contacts.user_id', $user_id)
            ->where('users.is_active', 1)
            ->orderBy('app_contacts.id', 'desc')
            ->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Fetched Successfully',
            'data' => $appContacts
        ], 200);
    }
}
