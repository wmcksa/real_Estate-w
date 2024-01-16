<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AddressDetails;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class AddressController extends Controller
{
    public function addressList(){
        $data['addresses'] = Address::with('details')->latest()->get();
        return view('admin.address.index', $data);
    }

    public function addressCreate(){
        $languages = Language::all();
        return view('admin.address.create', compact('languages'));
    }

    public function addressStore(Request $request, $language){
        $purifiedData = Purify::clean($request->except('image', '_token', '_method'));

        $rules = [
            'title.*' => 'required|unique:address_details,title|max:191',
        ];

        $message = [
            'title.*.required' => 'Address Field is required',
            'title.*.unique' => 'This address has already been taken!',
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $address = new Address();
        if ($request->has('status')){
            $address->status = $request->status;
        }
        $address->save();

        $address->details()->create([
            'language_id' => $language,
            'title' => $purifiedData["title"][$language],
        ]);

        return back()->with('success', __('Address Successfully Created'));
    }

    public function addressEdit($id)
    {
        $data['languages']      = Language::all();
        $data['addressDetails'] = AddressDetails::with('address')->where('address_id', $id)->get()->groupBy('language_id');
        return view('admin.address.edit', $data, compact('id'));
    }

    public function addressUpdate(Request $request, $id, $language_id){

        $purifiedData = Purify::clean($request->except('image', '_token', '_method'));

        $rules = [
            'title.*' => 'required|max:191',
        ];

        $message = [
            'title.*.required' => 'Address field is required',
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $address = Address::findOrFail($id);
        if ($request->has('status')){
            $address->status = $request->status;
        }

        $address->save();

        $address->details()->updateOrCreate([
            'language_id' => $language_id
        ],
            [
                'title' => $purifiedData["title"][$language_id],
            ]
        );

        return back()->with('success', __('Address Successfully Updated'));
    }

}
