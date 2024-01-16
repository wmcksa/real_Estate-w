<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Models\Amenity;
use App\Models\AmenityDetails;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;


class AmenitiesController extends Controller
{
    public function amenities(){
        $data['amenities'] = Amenity::with('details')->latest()->get();
        return view('admin.amenities.index', $data);
    }

    public function amenitiesCreate(){
        $languages = Language::all();
        return view('admin.amenities.create', compact('languages'));
    }

    public function amenitiesStore(Request $request, $language){
        $purifiedData = Purify::clean($request->except('image', '_token', '_method'));

        $rules = [
            'title.*' => 'required|max:191',
            'icon'    => 'required|max:100',
        ];

        $message = [
            'title.*.required' => 'Title Field is required',
            'icon.required' => 'Icon field is required',
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $amenity = new Amenity();
        $amenity->icon = $request->icon;

        if ($request->has('status')){
            $amenity->status = $request->status;
        }
        $amenity->save();

        $amenity->details()->create([
            'language_id' => $language,
            'title' => $purifiedData["title"][$language],
        ]);

        return back()->with('success', __('Amenity Successfully Saved'));
    }

    public function amenitiesEdit($id)
    {
        $data['languages']      = Language::all();
        $data['amenityDetails'] = AmenityDetails::with('amenity')->where('amenity_id', $id)->get()->groupBy('language_id');
        return view('admin.amenities.edit', $data, compact('id'));
    }

    public function amenitiesUpdate(Request $request, $id, $language_id){

        $purifiedData = Purify::clean($request->except('image', '_token', '_method'));

        $rules = [
            'title.*' => 'required|max:191',
            'icon' => 'sometimes|required|max:100',
        ];

        $message = [
            'title.*.required' => 'Title field is required',
            'icon.required' => 'Icon field is required',
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $amenity = Amenity::findOrFail($id);
        if ($request->has('status')){
            $amenity->icon = $request->icon;
            $amenity->status = $request->status;
        }

        $amenity->save();

        $amenity->details()->updateOrCreate([
            'language_id' => $language_id
        ],
            [
                'title' => $purifiedData["title"][$language_id],
            ]
        );

        return back()->with('success', __('Amenities Successfully Updated'));
    }
}
