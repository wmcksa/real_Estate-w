<?php

namespace App\Http\Controllers;

use App\Helper\SystemInfo;
use App\Http\Traits\Notify;
use App\Models\Address;
use App\Models\Amenity;
use App\Models\Analytics;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogCategoryDetails;
use App\Models\Comment;
use App\Models\Content;
use App\Models\ContentDetails;
use App\Models\Gateway;
use App\Models\InvestorReview;
use App\Models\Language;
use App\Models\ManageProperty;
use App\Models\Subscriber;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;
use App\Console\Commands\MainCron;


class FrontendController extends Controller
{
    use Notify;

    public function __construct()
    {
        $this->theme = template();
    }

    public function index()
    {

        $templateSection = ['hero', 'about-us', 'why-chose-us', 'how-it-work', 'how-we-work', 'know-more-us', 'deposit-withdraw', 'news-letter', 'news-letter-referral', 'testimonial', 'request-a-call', 'investor', 'blog', 'faq', 'we-accept', 'plan', 'property', 'coin', 'latest-property', 'statistics', 'referral'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $contentSection = ['feature', 'why-chose-us', 'how-it-work', 'how-we-work', 'know-more-us', 'testimonial', 'investor', 'blog', 'faq', 'statistics'];
        $data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
            ->whereHas('content', function ($query) use ($contentSection) {
                return $query->whereIn('name', $contentSection);
            })
            ->with(['content:id,name',
                'content.contentMedia' => function ($q) {
                    $q->select(['content_id', 'description']);
                }])
            ->get()->groupBy('content.name');
        $data['gateways'] = Gateway::all();
        $data['popularBlogs'] = Blog::with(['details', 'blogCategory.details'])->where('status', 1)->take(3)->latest()->get();


        $data['featureProperties'] = ManageProperty::with(['details', 'getAddress.details', 'getInvestment'])->withCount('getReviews')->where('status', 1)->where('is_featured', 1)->whereDate('expire_date','>',now())->inRandomOrder()->limit(3)->orderBy('start_date')->get();
        $data['latestProperties'] = ManageProperty::with(['details', 'getAddress.details', 'getInvestment'])->withCount('getReviews')->where('status', 1)->whereDate('expire_date','>',now())->latest()->limit(6)->orderBy('start_date')->get();

        return view($this->theme . 'home', $data);
    }


    public function about()
    {
        $templateSection = ['about-us', 'investor', 'faq', 'we-accept', 'how-it-work', 'how-we-work', 'know-more-us', 'why-chose-us', 'testimonial', 'news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

        $contentSection = ['feature', 'why-chose-us', 'investor', 'faq', 'how-it-work', 'how-we-work', 'know-more-us', 'testimonial'];
        $data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
            ->whereHas('content', function ($query) use ($contentSection) {
                return $query->whereIn('name', $contentSection);
            })
            ->with(['content:id,name',
                'content.contentMedia' => function ($q) {
                    $q->select(['content_id', 'description']);
                }])
            ->get()->groupBy('content.name');
        $data['gateways'] = Gateway::all();
        return view($this->theme . 'about', $data);
    }


    public function blog()
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['title'] = "Blog";
        $data['allBlogs'] = Blog::with(['details', 'blogCategory.details'])->where('status', 1)->latest()->paginate(3);
        $data['blogCategory'] = BlogCategory::with('details')->where('status', 1)->latest()->get();
        return view($this->theme . 'blog', $data);
    }

    public function blogSearch(Request $request)
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['title'] = "Blog";
        $search = $request->search;

        $data['blogCategory'] = BlogCategory::with('details')->where('status', 1)->latest()->get();

        $data['allBlogs'] = Blog::with('details', 'blogCategory.details')
            ->whereHas('blogCategory.details', function ($qq) use ($search) {
                $qq->where('name', 'Like', '%' . $search . '%');
            })
            ->orWhereHas('details', function ($qq2) use ($search) {
                $qq2->where('title', 'Like', '%' . $search . '%');
                $qq2->orWhere('author', 'Like', '%' . $search . '%');
                $qq2->orWhere('details', 'Like', '%' . $search . '%');
            })
            ->where('status', 1)
            ->latest()->paginate(3);

        return view($this->theme . 'blog', $data);

    }

    public function blogDetails($slug, $id)
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['title'] = "Blog Details";
        $data['singleBlog'] = Blog::with('details')->where('status', 1)->findOrFail($id);
        $data['blogCategory'] = BlogCategoryDetails::where('blog_category_id', $data['singleBlog']->blog_category_id)->first();
        $data['allBlogCategory'] = BlogCategory::with('details')->where('status', 1)->latest()->get();
        $data['relatedBlogs'] = Blog::with(['details', 'blogCategory.details'])->where('id', '!=', $id)->where('blog_category_id', $data['singleBlog']->blog_category_id)->where('status', 1)->latest()->paginate(3);

        $data['allComments'] = Comment::with(['user', 'replies.user', 'replies.replies.user'])->where('blog_id', $id)->whereNull('parent_id')->get();
        $data['totlaComments'] = Comment::where('blog_id', $id)->count();

        return view($this->theme . 'blogDetails', $data);
    }

    public function CategoryWiseBlog($slug = 'category-blog', $id)
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['title'] = "Blog";
        $data['allBlogs'] = Blog::with(['details', 'blogCategory.details'])->where('blog_category_id', $id)->where('status', 1)->latest()->paginate(3);
        $data['blogCategory'] = BlogCategory::with('details')->where('status', 1)->latest()->get();

        return view($this->theme . 'blog', $data);
    }


    public function propertyDetails($title = null, $id = null)
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['singlePropertyDetails'] = ManageProperty::with('details', 'getAddress.details', 'getInvestment.user')->where('status', 1)->findOrFail($id);

        $investor = [];
        foreach ($data['singlePropertyDetails']->getInvestment as $investment) {
            $investor [] = $investment->user_id;
        }

        $data['latestProperties'] = ManageProperty::with('details', 'getAddress.details')->where('status', 1)->whereDate('expire_date','>',now())->inRandomOrder()->limit(3)->orderBy('start_date')->get();
        if (Auth::check() == true) {
            $data['reviewDone'] = InvestorReview::where('property_id', $id)->where('user_id', Auth::user()->id)->count();
        } else {
            $data['reviewDone'] = '0';
        }

        $data['all_reviews'] = InvestorReview::with('review_user_info')->where('property_id', $id)->get();
        $data['totalReview'] = $data['all_reviews']->count();
        $data['average_review'] = $data['all_reviews']->avg('rating2');



        $viewer_ip = SystemInfo::get_ip();
        $browserInfo = json_decode(json_encode(getIpInfo($viewer_ip)), true);
        $propertyAnalytics = new Analytics();
        $propertyAnalytics->manage_property_id = $id;
        $propertyAnalytics->visitor_ip = SystemInfo::get_ip();
        $propertyAnalytics->country = (!empty($browserInfo['country'])) ? implode($browserInfo['country']):null;
        $propertyAnalytics->city = (!empty($browserInfo['city'])) ? implode($browserInfo['city']):null;
        $propertyAnalytics->code = (!empty($browserInfo['code'])) ? implode($browserInfo['code']):null;
        $propertyAnalytics->lat = (!empty($browserInfo['lat'])) ? implode($browserInfo['lat']):null;
        $propertyAnalytics->long = (!empty($browserInfo['long'])) ? implode($browserInfo['long']):null;
        $propertyAnalytics->os_platform = SystemInfo::get_os();
        $propertyAnalytics->browser = SystemInfo::get_browsers();
        $propertyAnalytics->save();

        return view($this->theme . 'propertyDetails', $data, compact('investor'));
    }

    public function property(Request $request, $type = null, $id = null)
    {
        $min = ManageProperty::min('available_funding');
        $max = ManageProperty::max('available_funding');

        $minRange = $min;
        $maxRange = $max;

        if ($request->has('my_range')) {
            $range = explode(';', $request->my_range);
            $minRange = $range[0];
            $maxRange = $range[1];
        }


        $search = $request->all();

        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['allAddress'] = Address::with('details')->where('status', 1)->get();
        $data['allAmenities'] = Amenity::with('details')->where('status', 1)->get();

        $data['properties'] = ManageProperty::with(['details', 'getAddress.details', 'getInvestment', 'getReviews'])->withCount('getReviews')
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('details', function ($query2) use ($search) {
                    $query2->whereRaw("property_title REGEXP '[[:<:]]{$search['name']}[[:>:]]'");
                });
            })
            ->when(isset($search['location']), function ($query) use ($search) {
                return $query->whereHas('getAddress', function ($query2) use ($search) {
                    $query2->where('id', 'LIKE', "%{$search['location']}%");
                });
            })
            ->when(isset($search['amenity_id']), function ($query) use ($search) {
                return $query->whereJsonContains('amenity_id', $search['amenity_id']);
            })
            ->when(!empty($search['rating']), function ($query) use ($search) {
                return $query->whereHas('getReviews', function ($query2) use ($search) {
                    $query2->whereIn('rating2', $search['rating']);
                });
            })
            ->when(isset($search['my_range']), function ($query) use ($search, $minRange, $maxRange) {
                $query->whereBetween('total_investment_amount', [$minRange, $maxRange]);
            })
            ->withCount('getFavourite')
            ->where('status', 1)
            ->whereDate('expire_date','>',now())
            ->orderBy('start_date')->paginate(6);

        return view($this->theme . 'property', $data, compact('min', 'max', 'minRange', 'maxRange'));
    }


    public function investorProfile($username = null, $id = null)
    {
        $templateSection = ['news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $data['investorInfo'] = User::with('invests', 'invests.property.details', 'invests.property.getAddress.details')->findOrFail($id);

        $data['properties'] = ManageProperty::with('getInvestment', 'getInvestment.user.get_social_links_user', 'details', 'getAddress.details', 'getReviews')
            ->withCount('getFavourite')
            ->withCount('getReviews')
            ->whereHas('getInvestment', function ($query) use ($id){
                $query->where('user_id', $id);
            })->where('expire_date', '>', now())
            ->where('status', 1)
            ->latest()
            ->paginate(config('basic.paginate'));


        return view($this->theme . 'investorProfile', $data);
    }

    public function faq()
    {
        $templateSection = ['faq', 'news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

        $contentSection = ['faq'];
        $data['contentDetails'] = ContentDetails::select('id', 'content_id', 'description', 'created_at')
            ->whereHas('content', function ($query) use ($contentSection) {
                return $query->whereIn('name', $contentSection);
            })
            ->with(['content:id,name',
                'content.contentMedia' => function ($q) {
                    $q->select(['content_id', 'description']);
                }])
            ->get()->groupBy('content.name');

        $data['increment'] = 1;
        return view($this->theme . 'faq', $data);
    }

    public function contact()
    {
        $templateSection = ['contact-us', 'news-letter'];
        $data['templates'] = $templates = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
        $title = 'Contact Us';
        $contact = @$templates['contact-us'][0]->description;
        return view($this->theme . 'contact', $data, compact('title', 'contact'));
    }

    public function contactSend(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|max:91',
            'subject' => 'required|max:100',
            'message' => 'required|max:1000',
        ]);
        $requestData = Purify::clean($request->except('_token', '_method'));

        $basic = (object)config('basic');
        $basicEmail = $basic->sender_email;

        $name = $requestData['name'];
        $email_from = $requestData['email'];
        $subject = $requestData['subject'];
        $message = $requestData['message'] . "<br>Regards<br>" . $name;
        $from = $email_from;

        $headers = "From: <$from> \r\n";
        $headers .= "Reply-To: <$from> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $to = $basicEmail;

        if (@mail($to, $subject, $message, $headers)) {
            // echo 'Your message has been sent.';
        } else {
            //echo 'There was a problem sending the email.';
        }

        return back()->with('success', 'Mail has been sent');
    }

    public function getLink($getLink = null, $id)
    {
        $getData = Content::findOrFail($id);

        $templateSection = [ 'news-letter'];
        $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');

        $contentSection = [$getData->name];
        $contentDetail = ContentDetails::select('id', 'content_id', 'description', 'created_at')
            ->where('content_id', $getData->id)
            ->whereHas('content', function ($query) use ($contentSection) {
                return $query->whereIn('name', $contentSection);
            })
            ->with(['content:id,name',
                'content.contentMedia' => function ($q) {
                    $q->select(['content_id', 'description']);
                }])
            ->get()->groupBy('content.name');

        $title = @$contentDetail[$getData->name][0]->description->title;
        $description = @$contentDetail[$getData->name][0]->description->description;
        return view($this->theme . 'getLink', $data, compact('contentDetail', 'title', 'description'));
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'email' => 'required|email|max:255|unique:subscribers'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect(url()->previous() . '#subscribe')->withErrors($validator);
        }
        $data = new Subscriber();
        $data->email = $request->email;
        $data->save();

        $msg = [
            'email' => $data->email
        ];

        $action = [
            "link" => route('admin.subscriber.index'),
            "icon" => "fas fa-user text-white"
        ];

        $this->adminPushNotification('SUBSCRIBE_NEWSLETTER', $msg, $action);
        $this->mailToAdmin($type = 'SUBSCRIBE_NEWSLETTER', [
            'email' => $data->email,
        ]);

        return redirect(url()->previous() . '#subscribe')->with('success', 'Subscribed Successfully');
    }

    public function language($code)
    {
        $language = Language::where('short_name', $code)->first();
        if (!$language) $code = 'US';
        session()->put('trans', $code);
        session()->put('rtl', $language ? $language->rtl : 0);
        return redirect()->back();
    }

    public function reviewPush(Request $request)
    {
        $review = new InvestorReview();
        $review->property_id = $request->propertyId;
        $review->user_id = auth()->id();
        $review->rating2 = $request->rating;
        $review->review = $request->feedback;
        $review->save();

        $data['review'] = $review->review;
        $data['review_user_info'] = $review->review_user_info;
        $data['rating2'] = $review->rating2;
        $data['date_formatted'] = dateTime($review->created_at, 'd M, Y h:i A');

        return response([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getReview($id)
    {
        $data = InvestorReview::with('review_user_info')->where('property_id', $id)->latest()->paginate(10);
        return response([
            'data' => $data
        ]);
    }

}
