<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Badge;
use App\Models\BadgeDetails;
use App\Models\Configure;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class BadgeController extends Controller
{
    use Upload, Notify;
    public function badgeList()
    {
        $data['allBadges'] = Badge::with('details')->orderBy('sort_by', 'asc')->get();
        $data['page_title'] = 'Badge List';
        return view('admin.badge.index', $data);
    }

    public function badgeCreate()
    {
        $languages = Language::all();
        return view('admin.badge.create', compact('languages'));
    }

    public function badgeStore(Request $request, $language = null)
    {
        $reqData = Purify::clean($request->except('_token', '_method', 'badge_icon'));
        $reqData['badge_icon'] = $request->badge_icon ?? null;
        $rules = [
            'rank_name.*'   => 'required|string|max:50',
            'rank_level.*'  => 'required|max:50',
            'min_invest'  => 'nullable|min:0|not_in:0',
            'min_deposit' => 'nullable|min:0|not_in:0',
            'min_earning' => 'nullable|min:0|not_in:0',
            'badge_icon' => 'required|mimes:jpg,png,jpeg',
        ];

        $message = [
            'rank_name.*.required' => __('rank name field is required'),
            'rank_level.*.required' => __('rank level field is required'),
            'badge_icon.mimes' => __('this Badge icon must be a file of type: jpg, jpeg, png.'),
            'min_invest.not_in' => __('minimum invest must be gratter then 0'),
            'min_deposit.not_in' => __('minimum deposit must be gratter then 0'),
            'min_earning.not_in' => __('minimum earning must be gratter then 0')
        ];

        $validate = Validator::make($reqData, $rules, $message);
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $badge = new Badge();
        $badge->min_invest  = isset($reqData['min_invest']) ? $reqData['min_invest'] : 0;
        $badge->min_deposit = isset($reqData['min_deposit']) ? $reqData['min_deposit'] : 0;
        $badge->min_earning = isset($reqData['min_earning']) ? $reqData['min_earning'] : 0;
        $badge->bonus       = isset($reqData['bonus']) ? $reqData['bonus'] : 0;
        $badge->status      = $reqData['status'];

        if ($request->hasFile('badge_icon')) {
            try {
                $badge->badge_icon = $this->uploadImage($reqData['badge_icon'], config('location.badge.path'), config('location.badge.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Icon could not be uploaded.');
            }
        }

        $badge->save();

        $badgeDetails = $badge->details()->create([
            'language_id' => $language,
            'rank_name' => $reqData["rank_name"][$language],
            'rank_level' => $reqData["rank_level"][$language],
            'description' => $reqData["description"][$language],
        ]);

        return redirect()->route('admin.badgeList')->with('success', 'Badge created successfully');
    }


    public function badgeEdit($id)
    {
        $data['languages'] = Language::all();
        $data['singleBadgeDetails'] = BadgeDetails::with(['badges'])->where('badge_id', $id)->get()->groupBy('language_id');
        return view('admin.badge.edit', $data, compact('id'));
    }

    public function badgeUpdate(Request $request, $id, $language_id){

        $reqData = Purify::clean($request->except('_token', '_method', 'badge_icon'));
        $reqData['badge_icon'] = $request->badge_icon ?? null;
        $rules = [
            'rank_name.*'   => 'required|string|max:50',
            'rank_level.*'  => 'required|max:50',
            'min_invest'  => 'nullable|min:0|not_in:0',
            'min_deposit' => 'nullable|min:0|not_in:0',
            'min_earning' => 'nullable|min:0|not_in:0',
            'badge_icon.*' => 'sometimes|required|mimes:jpg,png,jpeg',
        ];

        $message = [
            'rank_name.*.required' => __('rank name field is required'),
            'rank_level.*.required' => __('rank level field is required'),
            'badge_icon.*.mimes' => __('this Badge icon must be a file of type: jpg, jpeg, png.'),
            'min_invest.not_in' => __('minimum invest must be gratter then 0'),
            'min_deposit.not_in' => __('minimum deposit must be gratter then 0'),
            'min_earning.not_in' => __('minimum earning must be gratter then 0')
        ];

        $validate = Validator::make($reqData, $rules, $message);
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }



        $badge = Badge::findOrFail($id);
        $badge->min_invest  = isset($reqData['min_invest']) ? $reqData['min_invest'] : 0;
        $badge->min_deposit = isset($reqData['min_deposit']) ? $reqData['min_deposit'] : 0;
        $badge->min_earning = isset($reqData['min_earning']) ? $reqData['min_earning'] : 0;
        $badge->bonus       = isset($reqData['bonus']) ? $reqData['bonus'] : 0;
        $badge->status      = $reqData['status'];


        if ($request->hasFile('badge_icon') && $reqData['badge_icon'] != null) {
            try {
                $badge->badge_icon = $this->uploadImage($reqData['badge_icon'], config('location.badge.path'), config('location.badge.size'));
            } catch (\Exception $exp) {
                return back()->with('error', 'Icon could not be uploaded.');
            }
        }

        $badge->save();

        $updatePropertyDetails = $badge->details()->updateOrCreate([
            'language_id' => $language_id
        ],
            [
                'rank_name' => $reqData["rank_name"][$language_id],
                'rank_level' => $reqData["rank_level"][$language_id],
                'description' => $reqData["description"][$language_id],
            ]
        );

        return redirect()->route('admin.badgeList')->with('success', 'Badge Update successfully');
    }

    public function sortBadges(Request $request){
        $data = $request->all();
        foreach ($data['sort'] as $key => $value) {

            Badge::where('id', $value)->update([
                'sort_by' => $key + 1
            ]);
        }

    }

    public function badgeSettings(){
        $page_title = 'Badge Bonus';
        $data['control'] = Configure::select('bonus')->first();
        return view('admin.badge.settings', $data, compact('page_title'));
    }

    public function badgeSettingsAction(Request $request){
        $requestData = Purify::clean($request->except('_token', '_method'));
        $data = Configure::first();
        $data->bonus = $requestData['bonus'];
        $data->save();

        config(['basic.bonus' => (int)$requestData['bonus']]);
        $fp = fopen(base_path() . '/config/basic.php', 'w');
        fwrite($fp, '<?php return ' . var_export(config('basic'), true) . ';');
        fclose($fp);
        return back()->with('success', __('Badge Settings Updated!'));
    }
}
