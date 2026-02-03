<?php

namespace Modules\Support\App\Http\Controllers;

use App\Models\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function index(){
        return view('contact');
    }
    public function postContact(Request $request){
        $data = $request->except('_token');
        $data['created_at'] = $data['updated_at'] = Carbon::now();
        Contact::insert($data);

        return redirect()->back();
    }
}
