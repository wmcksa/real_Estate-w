<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    use Upload;

    public function index()
    {
        $existLang =  Language::where('short_name', 'US')->first();
        if(!$existLang){
            $language = new Language();
            $language->name = 'English';
            $language->short_name = 'US';
            $language->is_active = 1;
            $language->rtl = 0;
            $language->save();
            $data = file_get_contents(resource_path('lang/') . 'US.json');
            $json_file = strtoupper($language->short_name) . '.json';
            $path = resource_path('lang/') . $json_file;
            File::put($path, $data);
        }
        $languages = Language::all();

        return view('admin.language.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.language.create');
    }

    public function store(Request $request)
    {
        $purifiedData = Purify::clean($request->except('_token'));
        $validationRules = [
            'name' => 'required|min:2|max:100',
            'short_name' => 'required|size:2|unique:languages',
        ];
        $validate = Validator::make($purifiedData, $validationRules);
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }
        $purifiedData = (object) $purifiedData;

        $language = new Language();
        $language->name = $purifiedData->name;
        $language->short_name = strtoupper($purifiedData->short_name);
        $language->is_active = $purifiedData->is_active;
        $language->rtl = $purifiedData->rtl??0;
        if ($request->file('flag') && $request->file('flag')->isValid()) {
            $extension = $request->flag->extension();
            $flagName = strtoupper($purifiedData->short_name . '.' . $extension);
            $this->uploadImage($request->flag, config('location.language.path'), config('location.language.size'), null, null, $flagName);
            $language->flag = $flagName;
        }
        $language->save();

        $data = file_get_contents(resource_path('lang/') . 'US.json');
        $json_file = strtoupper($language->short_name) . '.json';
        $path = resource_path('lang/') . $json_file;
        File::put($path, $data);
        return redirect()->route('admin.language.index')->with('success', 'Language Successfully Saved');
    }

    public function edit(language $language)
    {
        return view('admin.language.edit', compact('language'));
    }

    public function update(Request $request, language $language)
    {
        $purifiedData = Purify::clean($request->all());
        $validationRules = [
            'name' => 'required|min:2|max:100',
            'short_name' => 'required|size:2|unique:languages,short_name,' . $language->id,
        ];

        $validate = Validator::make($purifiedData, $validationRules);
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }
        $purifiedData = (object) $purifiedData;

        $language->name = $purifiedData->name;
        $language->short_name = strtoupper($purifiedData->short_name);
        $language->is_active = $purifiedData->is_active;
        $language->rtl = $purifiedData->rtl;
        $language->default_lang = $purifiedData->default_lang == 1 ? 1 : 0;
        if ($request->file('flag') && $request->file('flag')->isValid()) {
            $extension = $request->flag->extension();
            $flagName = strtoupper($purifiedData->short_name . '.' . $extension);
            $this->uploadImage($request->flag, config('location.language.path'), config('location.language.size'), $language->flag, null, $flagName);
            $language->flag = $flagName;
        }
        $language->save();

        $data = file_get_contents(resource_path('lang/') . 'US.json');
        $json_file = strtoupper($language->short_name) . '.json';
        $path = resource_path('lang/') . $json_file;
        File::put($path, $data);

        if ($language->default_lang == 1) {
            session()->put('trans', $language->short_name);
            session()->put('rtl', $language ? $language->rtl : 0);
            Language::whereNotIn('id', [$language->id])->get()->map(function ($item) {
                $item->default_lang = 0;
                $item->save();
            });
        }
        return redirect(route('admin.language.index'))->with('success', 'Language Successfully Saved');
    }

    public function delete(language $language)
    {
        try {
            $this->removeFile(config('location.language.path') . '/' . $language->flag);

            if ($language->short_name . '.json' != 'US.json') {
                $this->removeFile(resource_path('lang/') . $language->short_name . '.json');
            }
            $language->mailTemplates()->delete();
            $language->notifyTemplates()->delete();
            $language->contentDetails()->delete();
            $language->templateDetails()->delete();

            $language->amenityDetails()->delete();
            $language->addressDetails()->delete();

            $language->badgeDetails()->delete();
            $language->blogCategoryDetails()->delete();
            $language->blogDetails()->delete();
            $language->managePropertyDetails()->delete();
            $language->rankingDetails()->delete();

            $language->delete();
        }catch (\Exception $e) {

            DB::statement('ALTER TABLE `email_templates` DROP FOREIGN KEY `email_templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `notify_templates` DROP FOREIGN KEY `notify_templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `content_details` DROP FOREIGN KEY `content_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `templates` DROP FOREIGN KEY `templates_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');

            DB::statement('ALTER TABLE `amenity_details` DROP FOREIGN KEY `amenity_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `address_details` DROP FOREIGN KEY `address_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `badge_details` DROP FOREIGN KEY `badge_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `blog_category_details` DROP FOREIGN KEY `blog_category_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `blog_details` DROP FOREIGN KEY `blog_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `manage_property_details` DROP FOREIGN KEY `manage_property_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('ALTER TABLE `ranking_details` DROP FOREIGN KEY `ranking_details_language_id_foreign`, MODIFY `language_id` SMALLINT  UNSIGNED');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

            return back()->with('error', $e->getMessage());
        }


        return back()->with('success', 'Language Has been Deleted');
    }


    public function keywordEdit($id)
    {
        $lang = Language::findOrFail($id);
        $page_title = "Update " . $lang->name . " Keywords";
        $json = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
        $list_lang = Language::all();

        if (empty($json)) {
            return back()->with('error', 'File Not Found.');
        }
        $json = json_decode($json,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return view('admin.language.keyword', compact('page_title', 'json', 'lang', 'list_lang'));
    }

    public function keywordUpdate(Request $request, $id)
    {
        $lang = Language::findOrFail($id);
        $content = json_encode($request->keys,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($content === 'null') {
            return back()->with('error', 'At Least One Field Should Be Fill-up');
        }
        file_put_contents(resource_path('lang/') . $lang->short_name . '.json', $content);
        return back()->with('success', 'Update Successfully');
    }

    public function importJson(Request $request)
    {
        $myLang = Language::findOrFail($request->myLangid);
        $lang = Language::findOrFail($request->id);
        $json = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
        $jsonArray = json_decode($json, true);
        file_put_contents(resource_path('lang/') . $myLang->short_name . '.json', json_encode($jsonArray,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return 'success';
    }





    public function storeKey(Request $request, $id)
    {
        $lang = Language::findOrFail($id);
        $rules = [
            'key' => 'required',
            'value' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $items = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
        $requestKey = trim($request->key);
        if (array_key_exists($requestKey, json_decode($items, true))) {
            return back()->with('error', "`$requestKey` Already Exist");
        } else {
            $newArr[$requestKey] = trim($request->value);
            $itemsss = json_decode($items, true);
            $result = array_merge($itemsss, $newArr);
            file_put_contents(resource_path('lang/') . $lang->short_name . '.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return back()->with('success',"`$requestKey` has been added");
        }
    }

    public function deleteKey(Request $request, $id)
    {
        $rules = [
            'key' => 'required',
            'value' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $requestKey = $request->key;
        $requestValue = $request->value;
        $lang = Language::findOrFail($id);
        $data = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
        $jsonArray = json_decode($data, true);
        unset($jsonArray[$requestKey]);
        file_put_contents(resource_path('lang/'). $lang->short_name . '.json', json_encode($jsonArray,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return back()->with('success', "`$requestKey` has been removed");
    }


    public function updateKey(Request $request, $id)
    {
        $rules = [
            'key' => 'required',
            'value' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $requestKey = trim($request->key);
        $requestValue = $request->value;
        $lang = Language::findOrFail($id);

        $data = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
        $jsonArray = json_decode($data, true);
        $jsonArray[$requestKey] = $requestValue;
        file_put_contents(resource_path('lang/'). $lang->short_name . '.json', json_encode($jsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return back()->with('success', "Update successfully");
    }

}
