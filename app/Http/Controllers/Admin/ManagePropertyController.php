<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Address;
use App\Models\Amenity;
use App\Models\Analytics;
use App\Models\Configure;
use App\Models\Favourite;
use App\Models\Image;
use App\Models\Language;
use App\Models\ManageProperty;
use App\Models\ManagePropertyDetails;
use App\Models\ManageTime;
use Carbon\Carbon;
use hisorange\BrowserDetect\Exceptions\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stevebauman\Purify\Facades\Purify;

class ManagePropertyController extends Controller
{
    use Upload, Notify;

    public function propertyList(Request $request, $type = 'all')
    {
        $handleProperty = config('handleProperty');

        $types = array_keys($handleProperty);

        abort_if(!in_array($type, $types), 404);

        $title = $handleProperty[$type]['title'];

        $data['manageProperties'] = ManageProperty::with(['details', 'image', 'getInvestment'])
            ->when($type == "upcoming", function ($query) {
                $query->where('start_date', '>', now());
            })
            ->when($type == "running", function ($query) {
                $query->where('expire_date', '>', now())->where('start_date', '<', now());
            })
            ->when($type == "expired", function ($query) {
                $query->where('expire_date', '<', now());
            })
            ->where('status', 1)
            ->latest()
            ->paginate(config('basic.paginate'));
        return view($handleProperty[$type]['property_view'], $data, compact('title'));
    }

    public function propertyCreate()
    {
        $data['languages'] = Language::all();
        $data['allAmenities'] = Amenity::with('details')->where('status', 1)->latest()->get();
        $data['allSchedule'] = ManageTime::where('status', 1)->get();
        $data['allAddress'] = Address::with('details')->where('status', 1)->get();
        return view('admin.property.create', $data);
    }

    public function propertyStore(Request $request, $language = null)
    {
        $reqData = Purify::clean($request->except('_token', '_method', 'thumbnail', 'property_image'));
        $reqData['thumbnail'] = $request->thumbnail ?? null;
        $reqData['property_image'] = $request->property_image ?? null;

        $is_invest_type = (int)$reqData['is_invest_type'];
        $is_return_type = (int)$reqData['is_return_type'];
        $isInstallment = (int)$reqData['is_installment'];

        $minimum_amount = ($is_invest_type == 0 ? null : $reqData['minimum_amount']);
        $maximum_amount = ($is_invest_type == 0 ? null : $reqData['maximum_amount']);
        $fixed_amount = ($is_invest_type == 0 ? $reqData['fixed_amount'] : null);
        $total_investment_amount = $reqData['total_investment_amount'];

        // validation start
        $rules = [
            'property_title.*' => 'required|string|max:191',
            'address_id' => 'required|exists:addresses,id',
            'location' => 'nullable',
            'amenity_id' => 'nullable|exists:amenities,id',
            'thumbnail' => 'nullable|sometimes|required|mimes:jpg,jpeg,png|max:5120',
            'property_image.*' => 'nullable|mimes:jpeg,png,jpg',
            'installment_duration' => 'nullable|numeric|min:1',
            'fixed_amount' => 'nullable|numeric|min:1',
            'minimum_amount' => 'nullable|numeric|min:1',
            'maximum_amount' => 'nullable|gt:minimum_amount',
            'total_investment_amount' => 'required|nullable|numeric|min:1',
            'how_many_times' => 'nullable|numeric|min:1',
            'how_many_days' => 'nullable|exists:manage_times,id',
            'installment_amount' => 'nullable',
            'profit' => 'nullable',
            'loss' => 'nullable',
        ];

        $message = [
            'property_title.*.required' => __('Title is required'),
            'address_id.required' => __('Address is required'),
            'location.required' => __('Location is required'),
            'location.url' => __('Only embeded url are accepted'),
            'thumbnail.required' => __('Thumbnail field is required'),
            'property_image.*.mimes' => __('This property image must be a file of type: jpg, jpeg, png.'),
            'total_investment_amount.lt' => __('Fixed amount must be less then total invest amount'),
            'minimum_amount.lt' => __('Minimum amount must be less then total invest amount'),
            'maximum_amount.lt' => __('Maximum amount must be less then total invest amount'),
        ];

        if ($is_invest_type == 0 && $isInstallment == 1) {
            $rules['total_installments'] = ['required', 'numeric', 'min:1'];
            $rules['installment_amount'] = ['required'];
            $rules['installment_duration'] = ['required', 'numeric', 'min:1'];
            $rules['fixed_amount'] = ['required', 'numeric', 'min:1'];
        } elseif ($is_invest_type == 0) {
            $rules['fixed_amount'] = ['required', 'numeric', 'min:1'];
        } elseif ($is_invest_type == 1) {
            $rules['minimum_amount'] = ['required', 'numeric', 'min:1'];
            $rules['maximum_amount'] = ['required', 'gt:minimum_amount'];
        }

        if ($is_return_type == 0) {
            $rules['how_many_times'] = ['required', 'numeric', 'min:1'];
        }

        if ($fixed_amount != null && $total_investment_amount < $fixed_amount) {
            $rules['fixed_amount'] = ['lt:total_investment_amount'];
            $validate = Validator::make($reqData, $rules, $message);
            return back()->withInput()->withErrors($validate);
        }

        if ($is_invest_type == 1 && $minimum_amount > $total_investment_amount && $maximum_amount > $total_investment_amount) {
            $rules['maximum_amount'] = ['lt:total_investment_amount'];
            $rules['minimum_amount'] = ['lt:total_investment_amount'];
            $validate = Validator::make($reqData, $rules, $message);
            return back()->withInput()->withErrors($validate);
        } elseif ($is_invest_type == 1 && $minimum_amount > $total_investment_amount) {
            $rules['minimum_amount'] = ['lt:total_investment_amount'];
            $validate = Validator::make($reqData, $rules, $message);
            return back()->withInput()->withErrors($validate);
        } elseif ($is_invest_type == 1 && $maximum_amount > $total_investment_amount) {
            $rules['maximum_amount'] = ['lt:total_investment_amount'];
            $validate = Validator::make($reqData, $rules, $message);
            return back()->withInput()->withErrors($validate);
        }

        $validate = Validator::make($reqData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }
        // validation end

        $how_many_days = $reqData['how_many_days'];
        $how_many_times = ($is_return_type == 0 ? $reqData['how_many_times'] : null);
        $is_installment = ($is_invest_type == 0 ? (int)$reqData['is_installment'] : 0);
        $total_installments = ($is_installment == 1 ? $reqData['total_installments'] : null);
        $requestInstallmentAmount = 0;
        if ($is_invest_type == 0 && $isInstallment == 1) {
            $requestInstallmentAmount += (int)$reqData['fixed_amount'] / (int)$total_installments;
        }
        $installment_amount = ($is_installment == 1 ? $requestInstallmentAmount : null);
        $installment_duration = ($is_installment == 1 ? $reqData['installment_duration'] : null);
        $installment_duration_type = ($is_installment == 1 ? $reqData['installment_duration_type'] : null);
        $installment_late_fee = ($is_installment == 1 ? $reqData['installment_late_fee'] : null);
        $profit = $reqData['profit'];
        $profit_type = $reqData['profit_type'];
        $is_capital_back = (int)$reqData['is_capital_back'];
        $is_investor = (int)$reqData['is_investor'];

        DB::beginTransaction();
        try {
            $manageProperty = new ManageProperty();

            $manageProperty->is_invest_type = $is_invest_type;
            $manageProperty->minimum_amount = $minimum_amount;
            $manageProperty->maximum_amount = $maximum_amount;
            $manageProperty->fixed_amount = $fixed_amount;
            $manageProperty->total_investment_amount = $total_investment_amount;
            $manageProperty->available_funding = $total_investment_amount;
            $manageProperty->is_return_type = $is_return_type;
            $manageProperty->how_many_days = $how_many_days;
            $manageProperty->how_many_times = $how_many_times;
            $manageProperty->is_installment = $is_installment;
            $manageProperty->total_installments = $total_installments;
            $manageProperty->installment_amount = $installment_amount;
            $manageProperty->installment_duration = $installment_duration;
            $manageProperty->installment_duration_type = $installment_duration_type;
            $manageProperty->installment_late_fee = $installment_late_fee;
            $manageProperty->profit = $profit;
            $manageProperty->profit_type = $profit_type;
            $manageProperty->is_capital_back = $is_capital_back;
            $manageProperty->address_id = $reqData['address_id'];
            $manageProperty->location = $reqData['location'];
            $manageProperty->amenity_id = @$reqData['amenity_id'];
            $manageProperty->is_investor = $is_investor;
            $manageProperty->is_featured = $reqData['is_featured'];
            $manageProperty->is_available_funding = $reqData['is_available_funding'];
            $manageProperty->start_date = Carbon::parse($reqData['start_date']);
            $manageProperty->expire_date = Carbon::parse($reqData['expire_date']);
            $manageProperty->status = $reqData['status'];


            if ($request->hasFile('thumbnail')) {
                $manageProperty->thumbnail = $this->uploadImage($request->thumbnail, config('location.propertyThumbnail.path'), config('location.propertyThumbnail.size'));
                throw_if(!$manageProperty->thumbnail, __('Thumbnail could not be uploaded.'));
            }

            $saveProperty = $manageProperty->save();

            throw_if(!$saveProperty, 'Something went wrong while inserting property information!');

            if ($request->hasFile('property_image')) {
                foreach ($request->property_image as $key => $images) {
                    $image = new Image();
                    $image->image = $this->uploadImage($images, config('location.property.path'), config('location.property.size'));
                    if (!$image->image) {
                        DB::rollBack();
                        throw new Exception(__('Image could not be uploaded.'));
                    }

                    $propertyImages = $manageProperty->image()->save($image);
                    if (!$propertyImages) {
                        DB::rollBack();
                        throw new Exception(__('Something went wrong while inserting property images!'));
                    }
                }
            }

            $input_form = [];
            if ($request->has('faq_title')) {
                for ($a = 0; $a < count($request->faq_title); $a++) {
                    $arr = array();
                    $arr['field_name'] = $request->faq_title[$a];
                    $arr['field_value'] = $request->faq_details[$a];
                    $input_form[$arr['field_name']] = $arr;
                }
            }

            $propertyDetails = $manageProperty->details()->create([
                'language_id' => $language,
                'property_title' => $reqData["property_title"][$language],
                'details' => $reqData["details"][$language],
                'faq' => empty($input_form) ? null : $input_form,
            ]);

            if (!$propertyDetails) {
                DB::rollBack();
                throw new Exception(__('Something went wrong while inserting property details!'));
            }

            DB::commit();
            return back()->with('success', 'Property has been Added');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function propertyEdit($id)
    {
        $data['languages'] = Language::all();
        $data['allAmenities'] = Amenity::with('details')->where('status', 1)->latest()->get();
        $data['allSchedule'] = ManageTime::where('status', 1)->get();
        $data['allAddress'] = Address::with('details')->where('status', 1)->get();
        $data['singlePropertyDetails'] = ManagePropertyDetails::with(['manageProperty.image'])->where('manage_property_id', $id)->get()->groupBy('language_id');
        return view('admin.property.edit', $data, compact('id'));
    }

    public function propertyUpdate(Request $request, $id, $language_id)
    {
        $reqData = Purify::clean($request->except('_token', '_method', 'thumbnail', 'property_image'));
        $reqData['thumbnail'] = $request->thumbnail ?? null;
        $reqData['property_image'] = $request->property_image ?? null;

        if ($request->has('is_invest_type')) {
            $is_invest_type = (int)$reqData['is_invest_type'];
            $is_return_type = (int)$reqData['is_return_type'];
            $isInstallment = (int)$reqData['is_installment'];

            $minimum_amount = ($is_invest_type == 0 ? null : $reqData['minimum_amount']);
            $maximum_amount = ($is_invest_type == 0 ? null : $reqData['maximum_amount']);
            $fixed_amount = ($is_invest_type == 0 ? $reqData['fixed_amount'] : null);
            $total_investment_amount = $reqData['total_investment_amount'];
        }

        // validation start
        $rules = [
            'property_title.*' => 'required|string|max:191',
            'address_id' => 'sometimes|required|exists:addresses,id',
            'location' => 'nullable',
            'amenity_id' => 'sometimes|nullable|exists:amenities,id',
            'thumbnail.*' => 'nullable|sometimes|required|mimes:jpg,jpeg,png|max:5120',
            'property_image.*' => 'sometimes|nullable|mimes:jpeg,png,jpg',
            'installment_duration' => 'sometimes|nullable|numeric|min:1',
            'fixed_amount' => 'sometimes|nullable|numeric|min:1',
            'minimum_amount' => 'sometimes|nullable|numeric|min:1',
            'maximum_amount' => 'sometimes|nullable|gt:minimum_amount',
            'total_investment_amount' => 'sometimes|required|nullable|numeric|min:1',
            'how_many_times' => 'sometimes|nullable|numeric|min:1',
            'how_many_days' => 'sometimes|nullable|exists:manage_times,id',
            'installment_amount' => 'sometimes|nullable',
            'profit' => 'sometimes|nullable',
            'loss' => 'sometimes|nullable',
        ];

        $message = [
            'property_title.*.required' => __('Title is required'),
            'address_id.required' => __('Address is required'),
            'location.required' => __('Location is required'),
            'location.url' => __('Only embeded url are accepted'),
            'thumbnail.*.mimes' => __('Property thumbnail must be a file of type: jpg, jpeg, png.'),
            'property_image.*.mimes' => __('This property image must be a file of type: jpg, jpeg, png.'),
            'total_investment_amount.lt' => __('Fixed amount must be less then total invest amount'),
            'minimum_amount.lt' => __('Minimum amount must be less then total invest amount'),
            'maximum_amount.lt' => __('Maximum amount must be less then total invest amount'),
        ];


        if ($request->has('is_invest_type')) {
            if ($is_invest_type == 0 && $isInstallment == 1) {
                $rules['total_installments'] = ['required', 'numeric', 'min:1'];
                $rules['installment_amount'] = ['required'];
                $rules['installment_duration'] = ['required', 'numeric', 'min:1'];
                $rules['fixed_amount'] = ['required', 'numeric', 'min:1'];
            } elseif ($is_invest_type == 0) {
                $rules['fixed_amount'] = ['required', 'numeric', 'min:1'];
            } elseif ($is_invest_type == 1) {
                $rules['minimum_amount'] = ['required', 'numeric', 'min:1'];
                $rules['maximum_amount'] = ['required', 'gt:minimum_amount'];
            }

            if ($is_return_type == 0) {
                $rules['how_many_times'] = ['required', 'numeric', 'min:1'];
            }

            if ($fixed_amount != null && $total_investment_amount < $fixed_amount) {
                $rules['fixed_amount'] = ['lt:total_investment_amount'];
                $validate = Validator::make($reqData, $rules, $message);
                return back()->withInput()->withErrors($validate);
            }

            if ($is_invest_type == 1 && $minimum_amount > $total_investment_amount && $maximum_amount > $total_investment_amount) {
                $rules['maximum_amount'] = ['lt:total_investment_amount'];
                $rules['minimum_amount'] = ['lt:total_investment_amount'];
                $validate = Validator::make($reqData, $rules, $message);
                return back()->withInput()->withErrors($validate);
            } elseif ($is_invest_type == 1 && $minimum_amount > $total_investment_amount) {
                $rules['minimum_amount'] = ['lt:total_investment_amount'];
                $validate = Validator::make($reqData, $rules, $message);
                return back()->withInput()->withErrors($validate);
            } elseif ($is_invest_type == 1 && $maximum_amount > $total_investment_amount) {
                $rules['maximum_amount'] = ['lt:total_investment_amount'];
                $validate = Validator::make($reqData, $rules, $message);
                return back()->withInput()->withErrors($validate);
            }
        }


        $validate = Validator::make($reqData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }
        // validation end

        if ($request->has('is_invest_type')) {
            $how_many_days = $reqData['how_many_days'];
            $how_many_times = ($is_return_type == 0 ? $reqData['how_many_times'] : null);
            $is_installment = ($is_invest_type == 0 ? (int)$reqData['is_installment'] : 0);
            $total_installments = ($is_installment == 1 ? $reqData['total_installments'] : null);
            $requestInstallmentAmount = 0;
            if ($is_invest_type == 0 && $isInstallment == 1) {
                $requestInstallmentAmount += (int)$reqData['fixed_amount'] / (int)$total_installments;
            }
            $installment_amount = ($is_installment == 1 ? $requestInstallmentAmount : null);
            $installment_duration = ($is_installment == 1 ? $reqData['installment_duration'] : null);
            $installment_duration_type = ($is_installment == 1 ? $reqData['installment_duration_type'] : null);
            $installment_late_fee = ($is_installment == 1 ? $reqData['installment_late_fee'] : null);
            $profit = $reqData['profit'];
            $profit_type = $reqData['profit_type'];
            $is_capital_back = (int)$reqData['is_capital_back'];
            $is_investor = (int)$reqData['is_investor'];
        }


        DB::beginTransaction();
        try {

            $manageProperty = ManageProperty::findOrFail($id);
            if ($request->has('is_invest_type')) {
                $manageProperty->is_invest_type = $is_invest_type;
                $manageProperty->minimum_amount = $minimum_amount;
                $manageProperty->maximum_amount = $maximum_amount;
                $manageProperty->fixed_amount = $fixed_amount;
                $manageProperty->total_investment_amount = $total_investment_amount;
                $manageProperty->available_funding = $total_investment_amount;
                $manageProperty->is_return_type = $is_return_type;
                $manageProperty->how_many_days = $how_many_days;
                $manageProperty->how_many_times = $how_many_times;
                $manageProperty->is_installment = $is_installment;
                $manageProperty->total_installments = $total_installments;
                $manageProperty->installment_amount = $installment_amount;
                $manageProperty->installment_duration = $installment_duration;
                $manageProperty->installment_duration_type = $installment_duration_type;
                $manageProperty->installment_late_fee = $installment_late_fee;
                $manageProperty->profit = $profit;
                $manageProperty->profit_type = $profit_type;
                $manageProperty->is_capital_back = $is_capital_back;
                $manageProperty->address_id = $reqData['address_id'];
                $manageProperty->location = $reqData['location'];
                $manageProperty->amenity_id = @$reqData['amenity_id'];
                $manageProperty->is_investor = $is_investor;
                $manageProperty->is_featured = $reqData['is_featured'];
                $manageProperty->is_available_funding = $reqData['is_available_funding'];
                $manageProperty->start_date = Carbon::parse($reqData['start_date']);
                $manageProperty->expire_date = Carbon::parse($reqData['expire_date']);
                $manageProperty->status = $reqData['status'];


                if ($request->hasFile('thumbnail')) {
                    $manageProperty->thumbnail = $this->uploadImage($request->thumbnail, config('location.propertyThumbnail.path'), config('location.propertyThumbnail.size'));
                    throw_if(!$manageProperty->thumbnail, __('Image could not be uploaded.'));
                }

                $saveProperty = $manageProperty->save();

                throw_if(!$saveProperty, 'Something went wrong while Updateing property information!');

                $old_property_image = $request->old_property_image ?? [];
                $dbImages = Image::where('imageable_id', $id)->whereNotIn('id', $old_property_image)->get();
                foreach ($dbImages as $dbImage) {
                    $this->removeFile(config('location.property.path') . '/' . $dbImage->image);
                    $dbImage->delete();
                }

                if ($request->hasFile("property_image")) {
                    foreach ($request->property_image as $key => $images) {
                        $image = new Image();
                        $image->image = $this->uploadImage($images, config('location.property.path'), config('location.property.size'));
                        if (!$image->image) {
                            DB::rollBack();
                            throw new Exception(__('Image could not be uploaded.'));
                        }

                        $propertyImages = $manageProperty->image()->save($image);
                        if (!$propertyImages) {
                            DB::rollBack();
                            throw new Exception(__('Something went wrong while inserting property images!'));
                        }
                    }
                }
            }


            $input_form = [];
            if ($request->has('faq_title')) {
                for ($a = 0; $a < count($request->faq_title); $a++) {
                    $arr = array();
                    $arr['field_name'] = clean($request->faq_title[$a]);
                    $arr['field_value'] = $request->faq_details[$a];
                    $input_form[$arr['field_name']] = $arr;
                }
            }


            $updatePropertyDetails = $manageProperty->details()->updateOrCreate([
                'language_id' => $language_id
            ],
                [
                    'property_title' => $reqData["property_title"][$language_id],
                    'details' => $reqData["details"][$language_id],
                    'faq' => empty($input_form) ? null : $input_form,
                ]
            );

            if (!$updatePropertyDetails) {
                DB::rollBack();
                throw new Exception(__('Something went wrong while updateing property details!'));
            }

            DB::commit();
            return back()->with('success', 'Property has been Updated');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function propertyDelete($id)
    {
        $property = ManageProperty::findOrFail($id);
        $property->delete();
        return back()->with('success', __('Property has been deleted'));
    }

    public function activeMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select ID.');
            return response()->json(['error' => 1]);
        } else {
            ManageProperty::whereIn('id', $request->strIds)->update([
                'status' => 1,
            ]);
            session()->flash('success', 'Property Status Has Been Active');
            return response()->json(['success' => 1]);
        }
    }

    public function inActiveMultiple(Request $request)
    {
        if ($request->strIds == null) {
            session()->flash('error', 'You do not select ID.');
            return response()->json(['error' => 1]);
        } else {
            ManageProperty::whereIn('id', $request->strIds)->update([
                'status' => 0,
            ]);
            session()->flash('success', 'Property Status Has Been Deactive');
            return response()->json(['success' => 1]);
        }
    }


    public function scheduleManage()
    {
        $manageTimes = ManageTime::all();
        return view('admin.property.schedule', compact('manageTimes'));
    }

    public function storeSchedule(Request $request)
    {

        $reqData = Purify::clean($request->except('_token', '_method'));
        $request->validate([
            'time' => 'required|integer|min:1',
            'time_type' => 'required',
            'status' => 'required',
        ], [
            'time.required' => 'Duration time is required',
            'time_type.required' => 'Duration time type is required'
        ]);

        $data = new ManageTime();
        $data->time = $reqData['time'];
        $data->time_type = $reqData['time_type'];
        $data->status = $reqData['status'];

        $data->save();
        return back()->with('success', 'Added Successfully.');
    }

    public function updateSchedule(Request $request, $id)
    {
        $reqData = Purify::clean($request->except('_token', '_method'));
        $request->validate([
            'time' => 'required|integer|min:1',
            'time_type' => 'required',
            'status' => 'required',
        ], [
            'time.required' => 'Duration time is required',
            'time_type.required' => 'Duration time type is required'
        ]);

        $data = ManageTime::findOrFail($id);
        $data->time = $reqData['time'];
        $data->time_type = $reqData['time_type'];
        $data->status = $reqData['status'];
        $data->save();
        return back()->with('success', 'Update Successfully.');
    }


    public function propertyAnalytics(Request $request, $id = null)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['allAnalytics'] = Analytics::with(['getProperty.details', 'lastVisited:manage_property_id,created_at'])->withCount('totalCount')
            ->when(isset($id), function ($query) use ($id) {
                return $query->where('manage_property_id', $id);
            })
            ->when(isset($search['property']), function ($query) use ($search) {
                return $query->whereHas('getProperty', function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search['property']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->latest()->groupBy('manage_property_id')->paginate(config('basic.paginate'));


        return view('admin.property.analytics', $data);
    }

    public function shareInvestment()
    {
        $page_title = 'Property Share Investment';
        $data['control'] = Configure::select('is_share_investment')->first();
        return view('admin.property.shareInvestment', $data, compact('page_title'));
    }

    public function shareInvestmentAction(Request $request)
    {
        $requestData = Purify::clean($request->except('_token', '_method'));
        $data = Configure::first();
        $data->is_share_investment = $requestData['is_share_investment'];
        $data->save();

        config(['basic.is_share_investment' => (int)$requestData['is_share_investment']]);
        $fp = fopen(base_path() . '/config/basic.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
        fclose($fp);
        return back()->with('success', __('Property share investment updated'));
    }

    public function showPropertyAnalytics($id)
    {
        $data['allSinglePropertyAnalytics'] = Analytics::with(['getProperty.details'])->where('manage_property_id', $id)->latest()->paginate(config('basic.paginate'));
        return view('admin.property.showSingleAnalytics', $data);
    }

    public function wishListProperty(Request $request)
    {

        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();
        $data['wishLists'] = Favourite::with(['get_user', 'get_property.details'])
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('get_property.details', function ($query) use ($search) {
                    $query->where('property_title', 'LIKE', "%{$search['name']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q1) use ($fromDate) {
                return $q1->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->latest()->paginate(config('basic.paginate'));

        return view('admin.property.wishList', $data);
    }

    public function wishListDelete($id)
    {
        Favourite::findOrFail($id)->delete();
        return back()->with('success', __('Delete Successfull!'));
    }


}
