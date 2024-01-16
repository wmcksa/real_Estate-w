<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\Upload;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogCategoryDetails;
use App\Models\BlogDetails;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Traits\Notify;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;

class BlogController extends Controller
{
    use Upload, Notify;
    public function categoryList()
    {
        $manageBlogCategory = BlogCategory::with('details')->latest()->get();
        return view('admin.blog.categoryList', compact('manageBlogCategory'));
    }

    public function blogCategoryCreate()
    {
        $languages = Language::all();
        return view('admin.blog.blogCategoryCreate', compact('languages'));
    }

    public function blogCategoryStore(Request $request, $language)
    {
        $purifiedData = Purify::clean($request->except('_token', '_method'));

        $rules = [
            'name.*' => 'required|max:100',
        ];

        $message = [
            'name.*.required' => __('Category name field is required'),
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $blogCategory = new BlogCategory();
        if ($request->has('status')){
            $blogCategory->status = $request->status;
        }

        $blogCategory->save();

        $blogCategory->details()->create([
            'language_id' => $language,
            'name' => $purifiedData["name"][$language],
        ]);

        return back()->with('success', __('Blog Category Successfully Saved'));

    }

    public function blogCategoryEdit($id)
    {
        $languages = Language::all();
        $blogCategoryDetails = BlogCategoryDetails::with('category')->where('blog_category_id', $id)->get()->groupBy('language_id');
        return view('admin.blog.blogCategoryEdit', compact('languages', 'blogCategoryDetails', 'id'));
    }

    public function blogCategoryUpdate(Request $request, $id, $language_id)
    {

        $purifiedData = Purify::clean($request->except('_token', '_method'));

        $rules = [
            'name.*' => 'required|max:100',
        ];

        $message = [
            'name.*.required' => __('Category name field is required'),
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $blogCategory = BlogCategory::findOrFail($id);
        if ($request->has('status')){
            $blogCategory->status = $request->status;
        }
        $blogCategory->save();

        $blogCategory->details()->updateOrCreate([
            'language_id' => $language_id
        ],
            [
                'name' => $purifiedData["name"][$language_id],
            ]
        );

        return back()->with('success', __('Blog Category Successfully Updated'));
    }

    public function blogCategoryDelete($id)
    {
        $blogCategory = BlogCategory::findOrFail($id);
        $blogCategory->delete();
        return back()->with('success', __('Blog Category has been deleted'));
    }

    public function blogList()
    {
        $data['blogs'] = Blog::with('details', 'category.details')->latest()->get();
        return view('admin.blog.blogList', $data);
    }

    public function blogCreate()
    {

        $languages = Language::all();
        $data['blogCategory'] = BlogCategory::with('details')->where('status', 1)->get();
        return view('admin.blog.blogCreate', $data, compact('languages'));
    }


    public function blogStore(Request $request, $language = null)
    {

        $purifiedData = Purify::clean($request->except('image', '_token', '_method'));

        $rules = [
            'blog_category_id' => 'required',
            'author.*' => 'required|max:50',
            'title.*' => 'required|max:191',
            'details.*' => 'required',
            'image' => 'sometimes|required|mimes:jpg,jpeg,png,bmp,gif,svg,avif,webp'
        ];
        $message = [
            'blog_category_id.required' => __('Please select blog category'),
            'author.*.max' => __('This field may not be greater than :max characters'),
            'author.*.required' => __('Author field is required'),
            'title.*.required' => __('Title field is required'),
            'details.*.required' => __('Details field is required'),
            'image.required' => __('Image is required')
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $blog = new Blog();
        if ($request->has('status')){
            $blog->status = $request->status;
        }
        $blog->blog_category_id = $request->blog_category_id;


        if ($request->hasFile('image')) {
            try {
                $blog->image = $this->uploadImage($request->image, config('location.blog.path'), config('location.blog.size'), $blog->image);
            } catch (\Exception $exp) {
                return back()->with('error', __('Image could not be uploaded.'));
            }
        }

        $blog->save();
        $blog->details()->create([

            'language_id' => $language,
            'author' => $purifiedData["author"][$language],
            'title' => $purifiedData["title"][$language],
            'details' => $purifiedData["details"][$language],
        ]);

        return back()->with('success', __('Blog Successfully Saved'));
    }


    public function blogEdit($id)
    {
        $languages = Language::all();
        $blogDetails = BlogDetails::with('blog')->where('blog_id', $id)->get()->groupBy('language_id');
        $blogCategory = BlogCategory::with('details')->where('status', 1)->get();

        return view('admin.blog.blogEdit', compact('languages', 'blogDetails', 'blogCategory', 'id'));
    }


    public function blogUpdate(Request $request, $id, $language_id)
    {
        $purifiedData = Purify::clean($request->except('_token', 'image', '_method'));

        $rules = [
            'blog_category_id' => 'sometimes|required',
            'author.*' => 'required|max:50',
            'title.*' => 'required|max:191',
            'details.*' => 'required',
            'image' => 'mimes:jpg,jpeg,png,svg,vector'
        ];
        $message = [
            'blog_category_id.required' => __('Please select blog category'),
            'author.*.max' => __('Author field may not be greater than :max characters'),
            'author.*.required' => __('Author field is required'),
            'title.*.required' => __('Title field is required'),
            'details.*.required' => __('Details field is required'),
            'image.required' => __('Image is required')
        ];

        $validate = Validator::make($purifiedData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $blog = Blog::findOrFail($id);
        if ($request->has('status')){
            $blog->status = $request->status;
        }

        if ($request->has('blog_category_id')) {
            $blog->blog_category_id = $request->blog_category_id;
        }

        if ($request->hasFile('image')) {
            try {
                $blog->image = $this->uploadImage($request->image, config('location.blog.path'), config('location.blog.size'));
            } catch (\Exception $exp) {
                return back()->with('error', __('Image could not be uploaded.'));
            }
        }

        $blog->save();

        $blog->details()->updateOrCreate([
            'language_id' => $language_id
        ],
            [
                'author' => $purifiedData["author"][$language_id],
                'title' => $purifiedData["title"][$language_id],
                'details' => $purifiedData["details"][$language_id],
            ]
        );
        return back()->with('success', __('Blog Successfully Updated'));
    }

    public function blogDelete($id)
    {
        $blog = Blog::findOrFail($id);
        $old_image = $blog->image;
        $location = config('location.blog.path');

        if (!empty($old_image)) {
            unlink($location . '/' . $old_image);
        }

        $blog->delete();
        return back()->with('success', __('Blog has been deleted'));
    }
}
