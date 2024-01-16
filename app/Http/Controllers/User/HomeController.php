<?php

namespace App\Http\Controllers\User;

use App\Events\ChatEvent;
use App\Helper\GoogleAuthenticator;
use App\Helper\SystemInfo;
use App\Http\Controllers\Controller;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\IdentifyForm;
use App\Models\Investment;
use App\Models\KYC;
use App\Models\Language;
use App\Models\ManageProperty;
use App\Models\MoneyTransfer;
use App\Models\OfferLock;
use App\Models\OfferReply;
use App\Models\PayoutLog;
use App\Models\PayoutMethod;
use App\Models\PayoutSetting;
use App\Models\PropertyOffer;
use App\Models\PropertyShare;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSocial;
use Facades\App\Services\InvestmentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Stevebauman\Purify\Facades\Purify;
use Facades\App\Services\BasicService;

use App\Console\Commands\PayoutCurrencyUpdateCron;

class HomeController extends Controller
{
    use Upload, Notify;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['walletBalance'] = getAmount($this->user->balance);
        $data['interestBalance'] = getAmount($this->user->interest_balance);
        $data['totalDeposit'] = getAmount($this->user->funds()->whereStatus(1)->sum('amount'));
        $data['totalPayout'] = getAmount($this->user->payout()->whereStatus(2)->sum('amount'));
        $data['depositBonus'] = getAmount($this->user->referralBonusLog()->where('type', 'deposit')->sum('amount'));
        $data['investBonus'] = getAmount($this->user->referralBonusLog()->where('type', 'invest')->sum('amount'));
        $data['lastBonus'] = getAmount(optional($this->user->referralBonusLog()->orderBy('id', 'DESC')->first())->amount);
        $data['totalBadgeBonus'] = getAmount($this->user->total_badge_bonous);

        $data['totalInterestProfit'] = getAmount($this->user->transaction()->where('balance_type', 'interest_balance')->where('trx_type', '+')->sum('amount'));


        $data['investment'] = collect(Investment::with('property')->where('user_id', $this->user->id)
            ->selectRaw('SUM( amount ) AS totalInvestAmount')
            ->selectRaw('SUM(CASE WHEN invest_status = 1 AND status = 0 AND is_active = 1 THEN amount END) AS runningInvestAmount')
            ->selectRaw('COUNT(id) AS totalInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 1 AND status = 0 AND is_active = 1 THEN id END) AS runningInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 0 AND status = 0 AND is_active = 1 THEN id END) AS dueInvestment')
            ->selectRaw('COUNT(CASE WHEN invest_status = 1 AND status = 1 AND is_active = 1 THEN id END) AS completedInvestment')
            ->get()->makeHidden('nextPayment')->toArray())->collapse();

        $data['ticket'] = Ticket::where('user_id', $this->user->id)->count();

        $monthlyInvestment = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);
        Investment::where('user_id', $this->user->id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->makeHidden('nextPayment')->map(function ($item) use ($monthlyInvestment) {
                $monthlyInvestment->put($item['months'], round($item['totalAmount'], 2));
            });
        $monthly['investment'] = $monthlyInvestment;


        $monthlyPayout = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);
        $this->user->payout()->whereStatus(2)
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->map(function ($item) use ($monthlyPayout) {
                $monthlyPayout->put($item['months'], round($item['totalAmount'], 2));
            });
        $monthly['payout'] = $monthlyPayout;


        $monthlyFunding = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);
        $this->user->funds()->whereNull('plan_id')->whereStatus(1)
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->map(function ($item) use ($monthlyFunding) {
                $monthlyFunding->put($item['months'], round($item['totalAmount'], 2));
            });
        $monthly['funding'] = $monthlyFunding;

        $monthlyReferralInvestBonus = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);
        $this->user->referralBonusLog()->where('type', 'invest')
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->map(function ($item) use ($monthlyReferralInvestBonus) {
                $monthlyReferralInvestBonus->put($item['months'], round($item['totalAmount'], 2));
            });

        $monthly['referralInvestBonus'] = $monthlyReferralInvestBonus;


        $monthlyReferralFundBonus = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);

        $this->user->referralBonusLog()->where('type', 'deposit')
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('sum(amount) as totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->map(function ($item) use ($monthlyReferralFundBonus) {
                $monthlyReferralFundBonus->put($item['months'], round($item['totalAmount'], 2));
            });
        $monthly['referralFundBonus'] = $monthlyReferralFundBonus;


        $monthlyProfit = collect(['January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0]);

        $this->user->transaction()->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear(),
            ])
            ->select(
                DB::raw('SUM((CASE WHEN remarks LIKE "%Interest From%" THEN amount END)) AS totalAmount'),
                DB::raw("DATE_FORMAT(created_at,'%M') as months")
            )
            ->groupBy(DB::raw("MONTH(created_at)"))
            ->get()->map(function ($item) use ($monthlyProfit) {
                $monthlyProfit->put($item['months'], round($item['totalAmount'], 2));
            });
        $monthly['monthlyGaveProfit'] = $monthlyProfit;



        $latestRegisteredUser = User::where('referral_id', $this->user->id)->latest()->first();
        $data['allBadges'] = Badge::with('details')->where('status', 1)->orderBy('sort_by', 'ASC')->get();
        $user = $this->user;

        $data['investorBadge'] = BasicService::getInvestorCurrentBadge($user);
        if ($data['investorBadge'] != null){
            $data['lastInvestorBadge'] = $data['investorBadge']->with('details')->first();
        }else{
            $data['lastInvestorBadge'] = null;
        }

        return view($this->theme . 'user.dashboard', $data, compact('monthly', 'latestRegisteredUser'));
    }

    public function transaction()
    {
        $transactions = $this->user->transaction()->paginate(config('basic.paginate'));
        return view($this->theme . 'user.transaction.index', compact('transactions'));
    }

    public function transactionSearch(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $transaction = Transaction::where('user_id', $this->user->id)->with('user')
            ->when(@$search['transaction_id'], function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', "%{$search['transaction_id']}%");
            })
            ->when(@$search['remark'], function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', "%{$search['remark']}%");
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->paginate(config('basic.paginate'));
        $transactions = $transaction->appends($search);

        return view($this->theme . 'user.transaction.index', compact('transactions'));
    }

    public function fundHistory()
    {
        $funds = Fund::where('user_id', $this->user->id)->where('status', '!=', 0)->where('plan_id', null)->orderBy('id', 'DESC')->with('gateway')->paginate(config('basic.paginate'));
        return view($this->theme . 'user.transaction.fundHistory', compact('funds'));
    }

    public function fundHistorySearch(Request $request)
    {
        $search = $request->all();
        $dateSearch = $request->date_time;
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $funds = Fund::orderBy('id', 'DESC')->where('user_id', $this->user->id)->where('status', '!=', 0)
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('transaction', 'LIKE', $search['name']);
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->with('gateway')
            ->paginate(config('basic.paginate'));
        $funds->appends($search);

        return view($this->theme . 'user.transaction.fundHistory', compact('funds'));

    }

    public function addFund()
    {
        $data['totalPayment'] = null;
        $data['gateways'] = Gateway::where('status', 1)->orderBy('sort_by', 'ASC')->get();

        return view($this->theme . 'user.addFund', $data);
    }

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        $data['user'] = $this->user;
        $data['languages'] = Language::all();
        $data['social_links'] = UserSocial::where('user_id', $data['user']->id)->get();
        $data['identityFormList'] = IdentifyForm::where('status', 1)->get();
        if ($request->has('identity_type')) {
            $validator->errors()->add('identity', '1');
            $data['identity_type'] = $request->identity_type;
            $data['identityForm'] = IdentifyForm::where('slug', trim($request->identity_type))->where('status', 1)->firstOrFail();
            return view($this->theme . 'user.profile.myprofile', $data)->withErrors($validator);
        }

        return view($this->theme . 'user.profile.myprofile', $data);
    }

    public function updateProfile(Request $request)
    {
        $allowedExtensions = array('jpg', 'png', 'jpeg');

        $image = $request->image;
        $this->validate($request, [
            'image' => [
                'required',
                'max:4096',
                function ($fail) use ($image, $allowedExtensions) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    if (($image->getSize() / 1000000) > 2) {
                        return $fail("Images MAX  2MB ALLOW!");
                    }
                    if (!in_array($ext, $allowedExtensions)) {
                        return $fail("Only png, jpg, jpeg images are allowed");
                    }
                }
            ]
        ]);

        $user = $this->user;
        if ($request->hasFile('image')) {
            $path = config('location.user.path');
            try {
                $user->image = $this->uploadImage($image, $path);
            } catch (\Exception $exp) {
                return back()->with('error', 'Could not upload your ' . $image)->withInput();
            }
        }
        $user->save();

        $msg = [
            'name' => $user->fullname,
        ];

        $adminAction = [
            "link" => route('admin.user-edit', $user->id),
            "icon" => "fas fa-user text-white"
        ];
        $userAction = [
            "link" => route('user.profile'),
            "icon" => "fas fa-user text-white"
        ];

        $this->adminPushNotification('ADMIN_NOTIFY_USER_PROFILE_UPDATE', $msg, $adminAction);
        $this->userPushNotification($user, 'USER_NOTIFY_HIS_PROFILE_UPDATE', $msg, $userAction);

        $currentDate = dateTime(Carbon::now());
        $this->sendMailSms($user, $type = 'USER_MAIL_HIS_PROFILE_UPDATE', [
            'name' => $user->fullname,
            'date' => $currentDate,
        ]);

        $this->mailToAdmin($type = 'ADMIN_MAIL_USER_PROFILE_UPDATE', [
            'name' => $user->fullname,
            'date' => $currentDate,
        ]);

        return back()->with('success', 'Updated Successfully.');
    }

    public function profileImageUpdate(Request $request)
    {

        $allowedExtensions = array('jpg', 'png', 'jpeg');
        $image = $request->profile_image;

        $this->validate($request, [
            'profile_image' => [
                'required',
                'max:4096',
                function ($fail) use ($image, $allowedExtensions) {
                    $ext = strtolower($image->getClientOriginalExtension());
                    if (($image->getSize() / 1000000) > 2) {
                        return $fail("Images MAX  2MB ALLOW!");
                    }
                    if (!in_array($ext, $allowedExtensions)) {
                        return $fail("Only png, jpg, jpeg images are allowed");
                    }
                }
            ]
        ]);

        $user = $this->user;

        if ($request->hasFile('profile_image')) {
            $user->image = $this->uploadImage($request->profile_image, config('location.user.path'), config('location.user.size'), $user->image);
            $user->save();
        }

        $src = asset('assets/uploads/users/' . $user->image);

        return response()->json(['src' => $src]);
    }

    public function updateInformation(Request $request)
    {
        $languages = Language::all()->map(function ($item) {
            return $item->id;
        });

        $req = Purify::clean($request->all());
        $user = $this->user;
        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => "sometimes|required|alpha_dash|min:5|unique:users,username," . $user->id,
            'language_id' => Rule::in($languages),
        ];
        $message = [
            'firstname.required' => 'First Name field is required',
            'lastname.required' => 'Last Name field is required',
        ];

        $validator = Validator::make($req, $rules, $message);
        if ($validator->fails()) {
            $validator->errors()->add('profile', '1');
            return back()->withErrors($validator)->withInput();
        }
        $user->language_id = $req['language_id'];
        $user->firstname = $req['firstname'];
        $user->lastname = $req['lastname'];
        $user->username = $req['username'];
        $user->address = $req['address'];
        $user->bio = $req['bio'];
        $user->save();

        if ($request->social_icon) {
            UserSocial::where('user_id', $user->id)->delete();
            foreach ($request->social_icon as $key => $value) {
                UserSocial::create([
                    'user_id' => $user->id,
                    'social_icon' => $request->social_icon[$key],
                    'social_url' => $request->social_url[$key],
                    'updated_at' => \Illuminate\Support\Carbon::now(),
                ]);
            }
        }

        $msg = [
            'name' => $user->fullname,
        ];

        $adminAction = [
            "link" => route('admin.user-edit', $user->id),
            "icon" => "fas fa-user text-white"
        ];
        $userAction = [
            "link" => route('user.profile'),
            "icon" => "fas fa-user text-white"
        ];

        $this->adminPushNotification('ADMIN_NOTIFY_USER_PROFILE_INFORMATION_UPDATE', $msg, $adminAction);
        $this->userPushNotification($user, 'USER_NOTIFY_HIS_PROFILE_INFORMATION_UPDATE', $msg, $userAction);

        $currentDate = dateTime(Carbon::now());
        $this->sendMailSms($user, $type = 'USER_MAIL_HIS_PROFILE_INFORMATION_UPDATE', [
            'name' => $user->fullname,
            'date' => $currentDate,
        ]);

        $this->mailToAdmin($type = 'ADMIN_MAIL_USER_PROFILE_INFORMATION_UPDATE', [
            'name' => $user->fullname,
            'date' => $currentDate,
        ]);
        return back()->with('success', 'Updated Successfully.');
    }

    public function updatePassword(Request $request)
    {

        $rules = [
            'current_password' => "required",
            'password' => "required|min:5|confirmed",
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->errors()->add('password', '1');
            return back()->withErrors($validator)->withInput();
        }
        $user = $this->user;
        try {
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();

                $msg = [
                    'name' => $user->fullname,
                ];

                $adminAction = [
                    "link" => route('admin.user-edit', $user->id),
                    "icon" => "fas fa-user text-white"
                ];
                $userAction = [
                    "link" => route('user.profile'),
                    "icon" => "fas fa-user text-white"
                ];

                $this->adminPushNotification('ADMIN_NOTIFY_USER_PROFILE_PASSWORD_UPDATE', $msg, $adminAction);
                $this->userPushNotification($user, 'USER_NOTIFY_HIS_PROFILE_PASSWORD_UPDATE', $msg, $userAction);

                $currentDate = dateTime(Carbon::now());
                $this->sendMailSms($user, $type = 'USER_MAIL_HIS_PROFILE_PASSWORD_UPDATE', [
                    'name' => $user->fullname,
                    'date' => $currentDate,
                ]);

                $this->mailToAdmin($type = 'ADMIN_MAIL_USER_PROFILE_PASSWORD_UPDATE', [
                    'name' => $user->fullname,
                    'date' => $currentDate,
                ]);

                return back()->with('success', 'Password Changes successfully.');
            } else {
                throw new \Exception('Current password did not match');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function twoStepSecurity()
    {
        $basic = (object)config('basic');
        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($this->user->username . '@' . $basic->site_title, $secret);
        $previousCode = $this->user->two_fa_code;

        $previousQR = $ga->getQRCodeGoogleUrl($this->user->username . '@' . $basic->site_title, $previousCode);
        return view($this->theme . 'user.twoFA.index', compact('secret', 'qrCodeUrl', 'previousCode', 'previousQR'));
    }

    public function twoStepEnable(Request $request)
    {
        $user = $this->user;
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);

        $userCode = $request->code;
        if ($oneCode == $userCode) {
            $user['two_fa'] = 1;
            $user['two_fa_verify'] = 1;
            $user['two_fa_code'] = $request->key;
            $user->save();
            $this->mail($user, 'TWO_STEP_ENABLED', [
                'action' => 'Enabled',
                'code' => $user->two_fa_code,
                'ip' => request()->ip(),
                'browser' => SystemInfo::get_browsers() . ', ' . SystemInfo::get_os(),
                'time' => date('d M, Y h:i:s A'),
            ]);
            return back()->with('success', 'Google Authenticator Has Been Enabled.');
        } else {
            return back()->with('error', 'Wrong Verification Code.');
        }


    }

    public function twoStepDisable(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        $user = $this->user;
        $ga = new GoogleAuthenticator();

        $secret = $user->two_fa_code;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {
            $user['two_fa'] = 0;
            $user['two_fa_verify'] = 1;
            $user['two_fa_code'] = null;
            $user->save();
            $this->mail($user, 'TWO_STEP_DISABLED', [
                'action' => 'Disabled',
                'ip' => request()->ip(),
                'browser' => SystemInfo::get_browsers() . ', ' . SystemInfo::get_os(),
                'time' => date('d M, Y h:i:s A'),
            ]);

            return back()->with('success', 'Google Authenticator Has Been Disabled.');
        } else {
            return back()->with('error', 'Wrong Verification Code.');
        }
    }

    public function investProperty(Request $request, $id)
    {
        $rules = [
            'balance_type' => ['required','string','max:50',Rule::in(['balance', 'interest_balance'])],
            'amount' => 'required|numeric',
        ];

        $message = [
            'balance_type.*.required' => __('Please select your wallet'),
            'amount.required' => __('Amount field is required'),
        ];

        $validate = Validator::make($rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $user = $this->user;

        $property = ManageProperty::with('details')->where('id', $id)->where('status', 1)->where('expire_date', '>', now())->first();

        if (!$property) {
            return back()->with('error', 'Invalid Invest Request');
        }


        $balance_type = $request->balance_type;
        $pay_installment = $request->pay_installment;

        if (!in_array($balance_type, ['balance', 'interest_balance', 'checkout'])) {
            return back()->with('error', 'Invalid Wallet Type');
        }

        $amount = $request->amount;
        $basic = (object)config('basic');

        if ($property->fixed_amount == null && $amount < $property->minimum_amount) {
            return back()->with('error', "Minimum Invest Limit " . $property->minimum_amount);
        } elseif ($property->fixed_amount == null && $amount > $property->maximum_amount) {

            return back()->with('error', "Maximum Invest Limit " . $property->maximum_amount);
        } elseif (($property->fixed_amount != null && $amount != $property->fixed_amount) && ($pay_installment == null && $property->available_funding != $amount)) {
            return back()->with('error', "Please invest " . $property->fixed_amount);
        } elseif ($pay_installment != null && $amount != $property->installment_amount) {

            return back()->with('error', "Please invest " . $property->installment_amount);
        }

        if ($amount > $user->$balance_type) {
            return back()->with('error', 'Insufficient Balance');
        }


        $new_balance = getAmount($user->$balance_type - $amount); // new balance = user new balance
        $user->$balance_type = $new_balance;
        $user->total_invest += $request->amount;
        $user->save();

        $trx = strRandom();
        $remarks = 'Invested On ' . optional($property->details)->property_title;

        BasicService::makeTransaction($user, $amount, 0, '-', $balance_type, $trx, $remarks, $property);

        InvestmentService::profitLossCalculate($request, $property);
        InvestmentService::forAllInvest($user, $property, $amount, $pay_installment, $trx, $request);


        if ($basic->investment_commission == 1) {
            BasicService::setBonus($user, getAmount($request->amount),  'invest');
        }



        $currentDate = Carbon::now();
        $msg = [
            'username' => $user->username,
            'amount' => getAmount($amount),
            'currency' => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title
        ];
        $action = [
            "link" => route('admin.user.plan-purchaseLog', $user->id),
            "icon" => "fa fa-money-bill-alt"
        ];
        $userAction = [
            "link" => route('user.invest-history'),
            "icon" => "fa fa-money-bill-alt"
        ];

        $this->adminPushNotification('PROPERTY_INVEST_NOTIFY_TO_ADMIN', $msg, $action);
        $this->userPushNotification($user, 'PROPERTY_INVEST_NOTIFY_TO_USER', $msg, $userAction);

        $this->sendMailSms($user, $type = 'PROPERTY_INVEST_MAIL_TO_USER', [
            'username' => $user->username,
            'amount' => getAmount($amount),
            'currency' => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $trx,
            'date' => $currentDate,
        ]);

        $this->mailToAdmin($type = 'PROPERTY_INVEST_MAIL_TO_ADMIN', [
            'username' => $user->username,
            'amount' => getAmount($amount),
            'currency' => $basic->currency_symbol,
            'property_name' => optional($property->details)->property_title,
            'transaction_id' => $trx,
            'date' => $currentDate,
        ]);

        return back()->with('success', '`' . optional($property->details)->property_title . '`' . ' has been invested successfully.');
    }

    public function investHistory(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $investments = $this->user->invests()->with('property.details', 'propertyShare')
            ->when(isset($search['property']), function ($query) use ($search) {
                return $query->whereHas('property.details', function ($q) use ($search) {
                    $q->where('property_title', 'LIKE', '%' . $search['property'] . '%');
                });
            })
            ->when(isset($search['invest_status']), function ($query) use ($search) {
                return $query->where('invest_status', $search['invest_status']);
            })
            ->when(isset($search['return_status']), function ($query) use ($search) {
                return $query->where('status', $search['return_status']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->withTrashed()->latest()->paginate(config('basic.paginate'));

        return view($this->theme . 'user.transaction.investLog', compact('investments'));
    }

    public function investHistoryDetails($id)
    {
        $singleInvestDetails = Investment::with('property.details')->where('user_id', Auth::id())->withTrashed()->findOrFail($id);
        return view($this->theme . 'user.transaction.investDetails', compact('singleInvestDetails'));
    }

    public function completeDuePayment(Request $request, $id)
    {
        $this->validate($request, [
            'balance_type' => 'required|string|max:50',
            'amount' => 'required|numeric',
        ]);

        $user = $this->user;
        $investment = Investment::findOrFail($id);
        $amount = $request->amount;

        $balance_type = $request->balance_type;
        $pay_installment = $request->pay_installment;

        $basic = (object)config('basic');

        if (!in_array($balance_type, ['balance', 'interest_balance', 'checkout'])) {
            return back()->with('error', 'Invalid Wallet Type');
        }

        if ($amount > $user->$balance_type) {
            return back()->with('error', 'Insufficient Balance');
        }

        InvestmentService::dueInstallmentPayment($user, $request, $pay_installment, $investment, $amount, $balance_type);

        $trx = strRandom();
        $remarks = 'Due make payment On ' . optional($investment->property->details)->property_title;

        BasicService::makeTransaction($user, $amount, 0, $trx_type = '-', $balance_type, $trx, $remarks, $investment->property, $investment);

        if ($basic->investment_commission == 1) {
            BasicService::setBonus($user, getAmount($request->amount), $type = 'invest');
        }

        return back()->with('success', '`' . optional($investment->property->details)->property_title . '`' . ' due investment payment successfull.');
    }

    public function propertyShareStore(Request $request, $id)
    {
        $investment = Investment::select(['id', 'user_id', 'property_id'])->where('user_id', $this->user->id)->findOrFail($id);

        $reqData = Purify::clean($request->except('_token', '_method'));
        $rules = [
            'amount' => 'required',
        ];

        $message = [
            'amount.required' => __('Amount field is required!'),
        ];

        $validate = Validator::make($reqData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $propertyShare = new PropertyShare();
        $propertyShare->amount = $request->amount;
        $propertyShare->investment_id = $investment->id;
        $propertyShare->investor_id = $investment->user_id;
        $propertyShare->property_id = $investment->property_id;
        $propertyShare->save();
        return redirect()->route('user.propertyMarket', 'my-shared-properties')->with('success', 'Share successfully saved!');
    }

    public function propertyShareUpdate(Request $request, $id)
    {
        $reqData = Purify::clean($request->except('_token', '_method'));
        $rules = [
            'amount' => 'required',
        ];

        $message = [
            'amount.required' => __('Amount field is required!'),
        ];

        $validate = Validator::make($reqData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $propertyShare = PropertyShare::where('investor_id', $this->user->id)->findOrFail($id);
        $propertyShare->amount = $request->amount;
        $propertyShare->save();
        return back()->with('success', 'Share successfully updated!');
    }

    public function propertyShareRemove($id)
    {
        $propertyShare = PropertyShare::where('investor_id', $this->user->id)->findOrFail($id);
        $propertyShare->delete();
        return back()->with('success', 'Share remove successfully!');
    }

    public function propertyOfferUpdate(Request $request, $id)
    {
        $reqData = Purify::clean($request->except('_token', '_method'));
        $propertyOffer = PropertyOffer::where('offered_from', $this->user->id)->findOrFail($id);
        $propertyOffer->amount = $reqData['amount'];
        $propertyOffer->description = $reqData['description'];
        $propertyOffer->save();
        return back()->with('success', 'Offer successfully updated!');
    }

    public function propertyOfferRemove($id)
    {
        $propertyOffer = PropertyOffer::where('offered_from', $this->user->id)->find($id);
        if ($propertyOffer == null) {
            $propertyOffer = PropertyOffer::where('offered_to', $this->user->id)->findOrFail($id);
            $propertyOffer->delete();
        } else {
            $propertyOffer->delete();
        }

        return back()->with('success', 'Offer remove successfully!');
    }

    public function propertyMakeOfferStore(Request $request, $id)
    {
        $reqData = Purify::clean($request->except('_token', '_method'));

        $rules = [
            'amount' => 'required|numeric',
            'description' => 'nullable',
        ];

        $message = [
            'amount.required' => __('Amount field is required!'),
        ];

        $validate = Validator::make($reqData, $rules, $message);

        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

        $propertyShare = PropertyShare::findOrFail($id);
        $propertyOffer = new PropertyOffer();

        $propertyOffer->property_share_id = $propertyShare->id;
        $propertyOffer->offered_from = $this->user->id;
        $propertyOffer->offered_to = $propertyShare->investor_id;
        $propertyOffer->investment_id = $propertyShare->investment_id;
        $propertyOffer->property_id = $propertyShare->property_id;
        $propertyOffer->sell_amount = $propertyShare->amount;
        $propertyOffer->amount = $request->amount;
        $propertyOffer->description = $request->description;
        $propertyOffer->save();
        return redirect()->route('user.propertyMarket', 'my-offered-properties')->with('success', 'Offer successfully Send!');

    }


    /*
     * User payout Operation
     */
    public function payoutMoney()
    {
        $data['title'] = "Payout Money";
        $data['gateways'] = PayoutMethod::whereStatus(1)->get();
        $data['payoutSettings'] = PayoutSetting::first();
        $data['today'] = Str::lower(Carbon::now()->format('l'));
        return view($this->theme . 'user.payout.money', $data);
    }

    public function payoutMoneyRequest(Request $request)
    {
        $this->validate($request, [
            'wallet_type' => ['required', Rule::in(['balance', 'interest_balance'])],
            'gateway' => 'required|integer',
            'amount' => ['required', 'numeric']
        ]);


        $basic = (object)config('basic');
        $method = PayoutMethod::where('id', $request->gateway)->where('status', 1)->firstOrFail();
        $authWallet = $this->user;

        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);

        $finalAmo = $request->amount + $charge;

        if ($request->amount < $method->minimum_amount) {
            session()->flash('error', 'Minimum payout Amount ' . round($method->minimum_amount, 2) . ' ' . $basic->currency);
            return back();
        }
        if ($request->amount > $method->maximum_amount) {
            session()->flash('error', 'Maximum payout Amount ' . round($method->maximum_amount, 2) . ' ' . $basic->currency);
            return back();
        }

        if (getAmount($finalAmo) > $authWallet[$request->wallet_type]) {
            session()->flash('error', 'Insufficient ' . snake2Title($request->wallet_type) . ' For Withdraw.');
            return back();
        } else {
            $trx = strRandom();
            $withdraw = new PayoutLog();
            $withdraw->user_id = $authWallet->id;
            $withdraw->method_id = $method->id;
            $withdraw->amount = getAmount($request->amount);
            $withdraw->charge = $charge;
            $withdraw->net_amount = $finalAmo;
            $withdraw->trx_id = $trx;
            $withdraw->status = 0;
            $withdraw->balance_type = $request->wallet_type;
            $withdraw->save();
            session()->put('wtrx', $trx);
            session()->put('wallet_type', $request->wallet_type);
            return redirect()->route('user.payout.preview');
        }
    }


    public function payoutPreview()
    {
        $withdraw = PayoutLog::latest()->where('trx_id', session()->get('wtrx'))->where('status', 0)->latest()->with('method', 'user')->firstOrFail();
        $payoutMethod = $withdraw->method;
        $title = "Payout Form";
        $wallet_type = session()->get('wallet_type');


        $layout = 'layouts.user';
        $remaining = getAmount(auth()->user()->balance - $withdraw->net_amount);

        if ($payoutMethod->code == 'flutterwave') {
            return view($this->theme . 'user.payout.gateway.' . $payoutMethod->code, compact('withdraw', 'title', 'remaining', 'layout', 'payoutMethod', 'wallet_type'));
        } elseif ($payoutMethod->code == 'paystack') {
            return view($this->theme . 'user.payout.gateway.' . $payoutMethod->code, compact('withdraw', 'title', 'remaining', 'layout', 'payoutMethod', 'wallet_type'));
        }
        return view($this->theme . 'user.payout.preview', compact('withdraw', 'title', 'remaining', 'layout', 'payoutMethod', 'wallet_type'));
    }

    public function payoutRequestSubmit(Request $request)
    {

        $basic = (object)config('basic');
        $withdraw = PayoutLog::latest()->where('trx_id', session()->get('wtrx'))->where('status', 0)->with('method', 'user')->firstOrFail();
        $rules = [];
        $inputField = [];
        $balence_type = $request->wallet_type ? $request->wallet_type : 'withdraw';

        if (optional($withdraw->method)->input_form != null) {
            foreach (optional($withdraw->method)->input_form as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        if (optional($withdraw->method)->is_automatic == 1) {
            $rules['currency_code'] = 'required';
            if (optional($withdraw->method)->code == 'paypal') {
                $rules['recipient_type'] = 'required';
            }
        }

        $this->validate($request, $rules);

        $user = $this->user;

        if (getAmount($withdraw->net_amount) > $user->balance) {
            session()->flash('error', 'Insufficient balance For Payout.');
            return redirect()->route('user.payout.money');
        } else {
            $collection = collect($request);
            $reqField = [];
            if (optional($withdraw->method)->input_form != null) {
                foreach ($collection as $k => $v) {
                    foreach ($withdraw->method->input_form as $inKey => $inVal) {
                        if ($k != $inKey) {
                            continue;
                        } else {
                            if ($inVal->type == 'file') {
                                if ($request->hasFile($inKey)) {
                                    $image = $request->file($inKey);
                                    $filename = time() . uniqid() . '.jpg';
                                    $location = config('location.withdrawLog.path');
                                    $reqField[$inKey] = [
                                        'fieldValue' => $filename,
                                        'type' => $inVal->type,
                                    ];
                                    try {
                                        $this->uploadImage($image, $location, $size = null, $old = null, $thumb = null, $filename);
                                    } catch (\Exception $exp) {
                                        return back()->with('error', 'Image could not be uploaded.');
                                    }

                                }
                            } else {
                                $reqField[$inKey] = $v;
                                $reqField[$inKey] = [
                                    'fieldValue' => $v,
                                    'type' => $inVal->type,
                                ];
                            }
                        }
                    }
                }
                if (optional($withdraw->method)->is_automatic == 1) {
                    $reqField['amount'] = [
                        'fieldValue' => $withdraw->amount * convertRate($request->currency_code, $withdraw),
                        'type' => 'text',
                    ];
                }
                if (optional($withdraw->method)->code == 'paypal') {
                    $reqField['recipient_type'] = [
                        'fieldValue' => $request->recipient_type,
                        'type' => 'text',
                    ];
                }
                $withdraw['information'] = $reqField;
            } else {
                $withdraw['information'] = null;
            }

            $withdraw->currency_code = @$request->currency_code;
            $withdraw->status = 1;
            $withdraw->save();

            $user['balance'] -= $withdraw->net_amount;
            $user->save();


            $remarks = 'Withdraw Via ' . optional($withdraw->method)->name;
            BasicService::makeTransaction($user, $withdraw->amount, $withdraw->charge, '-', $balence_type, $withdraw->trx_id, $remarks);

            $this->userNotify($user, $withdraw);

            session()->flash('success', 'Payout request Successfully Submitted. Wait For Confirmation.');

            return redirect()->route('user.payout.money');
        }
    }

    public function userNotify($user, $withdraw)
    {
        $basic = (object)config('basic');

        $this->sendMailSms($user, $type = 'PAYOUT_REQUEST', [
            'method_name' => optional($withdraw->method)->name,
            'amount' => getAmount($withdraw->amount),
            'charge' => getAmount($withdraw->charge),
            'currency' => $basic->currency,
            'trx' => $withdraw->trx_id,
        ]);


        $msg = [
            'username' => $user->username,
            'amount' => getAmount($withdraw->amount),
            'currency' => $basic->currency,
        ];

        $action = [
            "link" => route('admin.user.withdrawal', $user->id),
            "icon" => "fa fa-money-bill-alt "
        ];

        $this->adminPushNotification('PAYOUT_REQUEST', $msg, $action);
    }

    public function payoutHistory()
    {
        $user = $this->user;
        $data['payoutLog'] = PayoutLog::whereUser_id($user->id)->where('status', '!=', 0)->latest()->with('user', 'method')->paginate(config('basic.paginate'));
        $data['title'] = "Payout Log";
        return view($this->theme . 'user.payout.log', $data);
    }

    public function payoutHistorySearch(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $payoutLog = PayoutLog::orderBy('id', 'DESC')->where('user_id', $this->user->id)->where('status', '!=', 0)
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->where('trx_id', 'LIKE', $search['name']);
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->with('user', 'method')->paginate(config('basic.paginate'));
        $payoutLog->appends($search);

        $title = "Payout Log";
        return view($this->theme . 'user.payout.log', compact('title', 'payoutLog'));
    }


    public function paystackPayout(Request $request, $trx_id)
    {
        $payout = PayoutLog::where('trx_id', $trx_id)->firstOrFail();
        $payoutMethod = PayoutMethod::find($payout->method_id);
        $user = $this->user;
        $balence_type = $request->wallet_type ? $request->wallet_type : 'withdraw';
        $purifiedData = Purify::clean($request->all());

        if (empty($purifiedData['bank'])) {
            return back()->with('alert', 'Bank field is required')->withInput();
        }

        $rules = [];
        $inputField = [];
        if ($payoutMethod->input_form != null) {
            foreach ($payoutMethod->input_form as $key => $cus) {

                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $rules['type'] = 'required';
        $rules['currency'] = 'required';

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        if (getAmount($payout->net_amount) > $user->balance) {
            session()->flash('error', 'Insufficient balance For Payout.');
            return redirect()->route('user.payout.money');
        }

        $collection = collect($purifiedData);
        $reqField = [];
        if ($payoutMethod->input_form != null) {
            foreach ($collection as $k => $v) {
                foreach ($payoutMethod->input_form as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->file($inKey) && $request->file($inKey)->isValid()) {
                                $extension = $request->$inKey->extension();
                                $fileName = strtolower(strtotime("now") . '.' . $extension);
                                $storedPath = config('location.withdrawLog.path');
                                $imageMake = Image::make($purifiedData[$inKey]);
                                $imageMake->save($storedPath);

                                $reqField[$inKey] = [
                                    'fieldValue' => $fileName,
                                    'type' => $inVal->type,
                                ];
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'fieldValue' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $reqField['type'] = [
                'fieldValue' => $request->type,
                'type' => 'text',
            ];
            $reqField['bank_code'] = [
                'fieldValue' => $request->bank,
                'type' => 'text',
            ];
            $reqField['amount'] = [
                'fieldValue' => $payout->amount * convertRate($request->currency, $payout),
                'type' => 'text',
            ];
            $payout->information = $reqField;
        } else {
            $payout->information = null;
        }
        $payout->currency_code = $request->currency_code;
        $payout->status = 1;
        $payout->save();

        $user->balance = $user->balance - $payout->net_amount;
        $user->save();

        $remarks = 'Withdraw Via ' . optional($payout->method)->name;
        BasicService::makeTransaction($user, $payout->amount, $payout->charge, '-', $balence_type, $payout->trx_id, $remarks);

        $this->userNotify($user, $payout);

        return redirect(route('user.payout.money'))->with('success', 'Withdraw request Successfully Submitted. Wait For Confirmation.');
    }


    public function flutterwavePayout(Request $request, $trx_id)
    {
        $payout = PayoutLog::where('trx_id', $trx_id)->first();
        $payoutMethod = PayoutMethod::find($payout->method_id);
        $user = $this->user;

        $balence_type = $request->wallet_type ? $request->wallet_type : 'withdraw';

        $purifiedData = Purify::clean($request->all());

        if (empty($purifiedData['transfer_name'])) {
            return back()->with('alert', 'Transfer field is required');
        }
        $validation = config('banks.' . $purifiedData['transfer_name'] . '.validation');

        $rules = [];
        $inputField = [];
        if ($validation != null) {
            foreach ($validation as $key => $cus) {
                $rules[$key] = 'required';
                $inputField[] = $key;
            }
        }

        if (getAmount($payout->net_amount) > $user->balance) {
            session()->flash('error', 'Insufficient balance For Withdraw.');
            return redirect()->route('user.payout.money');
        }

        if ($request->transfer_name == 'NGN BANK' || $request->transfer_name == 'NGN DOM' || $request->transfer_name == 'GHS BANK'
            || $request->transfer_name == 'KES BANK' || $request->transfer_name == 'ZAR BANK' || $request->transfer_name == 'ZAR BANK') {
            $rules['bank'] = 'required';
        }

        $rules['currency_code'] = 'required';

        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $collection = collect($purifiedData);
        $reqField = [];
        $metaField = [];

        if (config('banks.' . $purifiedData['transfer_name'] . '.input_form') != null) {
            foreach ($collection as $k => $v) {
                foreach (config('banks.' . $purifiedData['transfer_name'] . '.input_form') as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {

                        if ($inVal == 'meta') {
                            $metaField[$inKey] = $v;
                            $metaField[$inKey] = [
                                'fieldValue' => $v,
                                'type' => 'text',
                            ];
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'fieldValue' => $v,
                                'type' => 'text',
                            ];
                        }
                    }
                }
            }

            if ($request->transfer_name == 'NGN BANK' || $request->transfer_name == 'NGN DOM' || $request->transfer_name == 'GHS BANK'
                || $request->transfer_name == 'KES BANK' || $request->transfer_name == 'ZAR BANK' || $request->transfer_name == 'ZAR BANK') {

                $reqField['account_bank'] = [
                    'fieldValue' => $request->bank,
                    'type' => 'text',
                ];
            } elseif ($request->transfer_name == 'XAF/XOF MOMO') {
                $reqField['account_bank'] = [
                    'fieldValue' => 'MTN',
                    'type' => 'text',
                ];
            } elseif ($request->transfer_name == 'FRANCOPGONE' || $request->transfer_name == 'mPesa' || $request->transfer_name == 'Rwanda Momo'
                || $request->transfer_name == 'Uganda Momo' || $request->transfer_name == 'Zambia Momo') {
                $reqField['account_bank'] = [
                    'fieldValue' => 'MPS',
                    'type' => 'text',
                ];
            }

            if ($request->transfer_name == 'Barter') {
                $reqField['account_bank'] = [
                    'fieldValue' => 'barter',
                    'type' => 'text',
                ];
            } elseif ($request->transfer_name == 'flutterwave') {
                $reqField['account_bank'] = [
                    'fieldValue' => 'barter',
                    'type' => 'text',
                ];
            }


            $reqField['amount'] = [
                'fieldValue' => $payout->amount * convertRate($request->currency_code, $payout),
                'type' => 'text',
            ];

            $payout->information = $reqField;
            $payout->meta_field = $metaField;
        } else {
            $payout->information = null;
            $payout->meta_field = null;
        }

        $payout->status = 1;
        $payout->currency_code = $request->currency_code;
        $payout->save();

        $user->balance = $user->balance - $payout->net_amount;
        $user->save();

        $remarks = 'Withdraw Via ' . optional($payout->method)->name;
        BasicService::makeTransaction($user, $payout->amount, $payout->charge, '-', $balence_type, $payout->trx_id, $remarks);

        $this->userNotify($user, $payout);

        return redirect(route('user.payout.money'))->with('success', 'Payout request Successfully Submitted. Wait For Confirmation.');
    }

    public function getBankList(Request $request)
    {
        $currencyCode = $request->currencyCode;
        $methodObj = 'App\\Services\\Payout\\paystack\\Card';
        $data = $methodObj::getBank($currencyCode);
        return $data;
    }

    public function getBankForm(Request $request)
    {
        $bankName = $request->bankName;
        $bankArr = config('banks.' . $bankName);

        if ($bankArr['api'] != null) {

            $methodObj = 'App\\Services\\Payout\\flutterwave\\Card';
            $data = $methodObj::getBank($bankArr['api']);
            $value['bank'] = $data;
        }
        $value['input_form'] = $bankArr['input_form'];
        return $value;
    }

    public function referral()
    {
        $title = "My Referral";
        $referrals = getLevelUser($this->user->id);
        return view($this->theme . 'user.referral', compact('title', 'referrals'));
    }

    public function referralBonus()
    {
        $title = "Referral Bonus";
        $user = $this->user;
        $transactions = $this->user->referralBonusLog()->orderBy('id', 'DESC')->with('bonusBy:id,firstname,lastname')->get();

        $totalReferralTransaction = [];
        foreach ($transactions as $trx) {

            $type = $trx->type;
            if (empty($totalReferralTransaction[$type])) {
                $totalReferralTransaction[$type] = 0;
            }
            $totalReferralTransaction[$type] += $trx->amount;
        }

        $totalReferralTransaction['total_referral_bonous'] = array_sum($totalReferralTransaction);
        $transactions = paginate($transactions, config('basic.paginate'), $page = null, $options = ["path" => route('user.referral.bonus')]);
        return view($this->theme . 'user.transaction.referral-bonus', compact('title', 'transactions', 'totalReferralTransaction', 'user'));
    }

    public function referralBonusSearch(Request $request)
    {
        $title = "Referral Bonus";
        $user = $this->user;
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $transactions = $this->user->referralBonusLog()->latest()
            ->with('bonusBy:id,firstname,lastname')
            ->when(isset($search['search_user']), function ($query) use ($search) {
                return $query->whereHas('bonusBy', function ($q) use ($search) {
                    $q->where(DB::raw('concat(firstname, " ", lastname)'), 'LIKE', "%{$search['search_user']}%")
                        ->orWhere('firstname', 'LIKE', '%' . $search['search_user'] . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search['search_user'] . '%')
                        ->orWhere('username', 'LIKE', '%' . $search['search_user'] . '%');
                });
            })
            ->when(isset($search['type']), function ($query) use ($search) {
                return $query->where('type', 'LIKE', '%' . $search['type'] . '%');
            })
            ->when(isset($search['remark']), function ($query) use ($search) {
                return $query->where('remarks', 'LIKE', '%' . $search['remark'] . '%');
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })->get();

        $totalReferralTransaction = [];
        foreach ($transactions as $trx) {
            $type = $trx->type;
            if (empty($totalReferralTransaction[$type])) {
                $totalReferralTransaction[$type] = 0;
            }
            $totalReferralTransaction[$type] += $trx->amount;
        }

        $totalReferralTransaction['total_referral_bonous'] = array_sum($totalReferralTransaction);
        $transactions = paginate($transactions, config('basic.paginate'), $page = null, $options = ["path" => route('user.referral.bonus')]);

        $transactions = $transactions->appends($search);

        return view($this->theme . 'user.transaction.referral-bonus', compact('title', 'transactions', 'totalReferralTransaction', 'user'));
    }

    public function moneyTransfer()
    {
        $page_title = "Balance Transfer";
        return view($this->theme . 'user.money-transfer', compact('page_title'));
    }

    public function moneyTransferConfirm(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'amount' => 'required',
            'wallet_type' => ['required', Rule::in(['balance', 'interest_balance'])],
            'password' => 'required'
        ], [
            'wallet_type.required' => 'Please Select a wallet'
        ]);

        $basic = (object)config('basic');
        $email = trim($request->email);

        $receiver = User::where('email', $email)->first();


        if (!$receiver) {
            session()->flash('error', 'This Email could not Found!');
            return back();
        }
        if ($receiver->id == Auth::id()) {
            session()->flash('error', 'This Email could not Found!');
            return back()->withInput();
        }

        if ($receiver->status == 0) {
            session()->flash('error', 'Invalid User!');
            return back()->withInput();
        }


        if ($request->amount < $basic->min_transfer) {
            session()->flash('error', 'Minimum Transfer Amount ' . $basic->min_transfer . ' ' . $basic->currency);
            return back()->withInput();
        }
        if ($request->amount > $basic->max_transfer) {
            session()->flash('error', 'Maximum Transfer Amount ' . $basic->max_transfer . ' ' . $basic->currency);
            return back()->withInput();
        }

        $transferCharge = ($request->amount * $basic->transfer_charge) / 100;

        $user = Auth::user();
        $wallet_type = $request->wallet_type;
        if ($user[$wallet_type] >= ($request->amount + $transferCharge)) {

            if (Hash::check($request->password, $user->password)) {


                $sendMoneyCheck = MoneyTransfer::where('sender_id', $user->id)->where('receiver_id', $receiver->id)->latest()->first();

                if (isset($sendMoneyCheck) && Carbon::parse($sendMoneyCheck->send_at) > Carbon::now()) {

                    $time = $sendMoneyCheck->send_at;
                    $delay = $time->diffInSeconds(Carbon::now());
                    $delay = gmdate('i:s', $delay);

                    session()->flash('error', 'You can send money to this user after  delay ' . $delay . ' minutes');
                    return back()->withInput();
                } else {

                    $user[$wallet_type] = round(($user[$wallet_type] - ($transferCharge + $request->amount)), 2);
                    $user->save();

                    $receiver[$wallet_type] += round($request->amount, 2);
                    $receiver->save();

                    $trans = strRandom();

                    $sendTaka = new MoneyTransfer();
                    $sendTaka->sender_id = $user->id;
                    $sendTaka->receiver_id = $receiver->id;
                    $sendTaka->amount = round($request->amount, 2);
                    $sendTaka->charge = $transferCharge;
                    $sendTaka->trx = $trans;
                    $sendTaka->send_at = Carbon::parse()->addMinutes(1);
                    $sendTaka->save();

                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = round($request->amount, 2);
                    $transaction->charge = $transferCharge;
                    $transaction->trx_type = '-';
                    $transaction->balance_type = $wallet_type;
                    $transaction->remarks = 'Balance Transfer to  ' . $receiver->email;
                    $transaction->trx_id = $trans;
                    $transaction->final_balance = $user[$wallet_type];
                    $transaction->save();


                    $transaction = new Transaction();
                    $transaction->user_id = $receiver->id;
                    $transaction->amount = round($request->amount, 2);
                    $transaction->charge = 0;
                    $transaction->trx_type = '+';
                    $transaction->balance_type = $wallet_type;
                    $transaction->remarks = 'Balance Transfer From  ' . $user->email;
                    $transaction->trx_id = $trans;
                    $transaction->final_balance = $receiver[$wallet_type];
                    $transaction->save();

                    $currentDate = dateTime(Carbon::now());
                    $msg = [
                        'send_user' => $user->fullname,
                        'to_user' => $receiver->fullname,
                        'amount' => $request->amount,
                        'currency' => $basic->currency,
                    ];
                    $action = [
                        "link" => "#",
                        "icon" => "fa fa-money-bill-alt text-white"
                    ];

                    $userAction = [
                        "link" => route('user.home'),
                        "icon" => "fa fa-money-bill-alt text-white"
                    ];

                    $this->adminPushNotification('ADMIN_NOTIFY_BALANCE_TRANSFER', $msg, $action);
                    $this->userPushNotification($user, 'SENDER_NOTIFY_BALANCE_TRANSFER', $msg, $userAction);
                    $this->userPushNotification($receiver, 'RECEIVER_NOTIFY_BALANCE_TRANSFER', $msg, $userAction);

                    $this->mailToAdmin($type = 'ADMIN_MAIL_BALANCE_TRANSFER', [
                        'send_user' => $user->fullname,
                        'to_user' => $receiver->fullname,
                        'amount' => $request->amount,
                        'currency' => $basic->currency,
                        'date' => $currentDate
                    ]);

                    $this->sendMailSms($user, 'SENDER_MAIL_BALANCE_TRANSFER', [
                        'send_user' => $user->fullname,
                        'to_user' => $receiver->fullname,
                        'amount' => $request->amount,
                        'currency' => $basic->currency,
                        'date' => $currentDate
                    ]);

                    $this->sendMailSms($receiver, 'RECEIVER_MAIL_BALANCE_TRANSFER', [
                        'send_user' => $user->fullname,
                        'to_user' => $receiver->fullname,
                        'amount' => $request->amount,
                        'currency' => $basic->currency,
                        'date' => $currentDate
                    ]);

                    session()->flash('success', 'Balance Transfer  has been Successful');
                    return redirect()->route('user.money-transfer');
                }
            } else {
                session()->flash('error', 'Password Do Not Match!');
                return back()->withInput();
            }
        } else {
            session()->flash('error', 'Insufficient Balance!');
            return back()->withInput();
        }
    }

    public function verificationSubmit(Request $request)
    {
        $identityFormList = IdentifyForm::where('status', 1)->get();
        $rules['identity_type'] = ["required", Rule::in($identityFormList->pluck('slug')->toArray())];
        $identity_type = $request->identity_type;
        $identityForm = IdentifyForm::where('slug', trim($identity_type))->where('status', 1)->firstOrFail();

        $params = $identityForm->services_form;

        $rules = [];
        $inputField = [];
        $verifyImages = [];

        if ($params != null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                    array_push($verifyImages, $key);
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->errors()->add('identity', '1');

            return back()->withErrors($validator)->withInput();
        }


        $path = config('location.kyc.path') . date('Y') . '/' . date('m') . '/' . date('d');
        $collection = collect($request);

        $reqField = [];
        if ($params != null) {
            foreach ($collection as $k => $v) {
                foreach ($params as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $this->uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    session()->flash('error', 'Could not upload your ' . $inKey);
                                    return back()->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
        }

        try {

            DB::beginTransaction();

            $user = $this->user;
            $kyc = new KYC();
            $kyc->user_id = $user->id;
            $kyc->kyc_type = $identityForm->slug;
            $kyc->details = $reqField;
            $kyc->save();

            $user->identity_verify = 1;
            $user->save();

            if (!$kyc) {
                DB::rollBack();
                $validator->errors()->add('identity', '1');
                return back()->withErrors($validator)->withInput()->with('error', "Failed to submit request");
            }
            DB::commit();

            $msg = [
                'name' => $user->fullname,
            ];

            $adminAction = [
                "link" => route('admin.kyc.users.pending'),
                "icon" => "fas fa-user text-white"
            ];
            $userAction = [
                "link" => route('user.profile'),
                "icon" => "fas fa-user text-white"
            ];

            $this->adminPushNotification('ADMIN_NOTIFY_USER_KYC_REQUEST', $msg, $adminAction);
            $this->userPushNotification($user, 'USER_NOTIFY_HIS_KYC_REQUEST_SEND', $msg, $userAction);

            $currentDate = dateTime(Carbon::now());
            $this->sendMailSms($user, $type = 'USER_MAIL_HIS_KYC_REQUEST_SEND', [
                'name' => $user->fullname,
                'date' => $currentDate,
            ]);

            $this->mailToAdmin($type = 'ADMIN_MAIL_USER_KYC_REQUEST', [
                'name' => $user->fullname,
                'date' => $currentDate,
            ]);

            return redirect()->route('user.profile')->withErrors($validator)->with('success', 'KYC request has been submitted.');

        } catch (\Exception $e) {
            return redirect()->route('user.profile')->withErrors($validator)->with('error', $e->getMessage());
        }
    }

    public function addressVerification(Request $request)
    {
        $rules = [];
        $rules['addressProof'] = ['image', 'mimes:jpeg,jpg,png', 'max:2048'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->errors()->add('addressVerification', '1');
            return back()->withErrors($validator)->withInput();
        }

        $path = config('location.kyc.path') . date('Y') . '/' . date('m') . '/' . date('d');

        $reqField = [];
        try {
            if ($request->hasFile('addressProof')) {
                $reqField['addressProof'] = [
                    'field_name' => $this->uploadImage($request['addressProof'], $path),
                    'type' => 'file',
                ];
            } else {
                $validator->errors()->add('addressVerification', '1');

                session()->flash('error', 'Please select a ' . 'address Proof');
                return back()->withInput();
            }
        } catch (\Exception $exp) {
            session()->flash('error', 'Could not upload your ' . 'address Proof');
            return redirect()->route('user.profile')->withInput();
        }

        try {

            DB::beginTransaction();
            $user = $this->user;
            $kyc = new KYC();
            $kyc->user_id = $user->id;
            $kyc->kyc_type = 'address-verification';
            $kyc->details = $reqField;
            $kyc->save();
            $user->address_verify = 1;
            $user->save();

            if (!$kyc) {
                DB::rollBack();
                $validator->errors()->add('addressVerification', '1');
                return redirect()->route('user.profile')->withErrors($validator)->withInput()->with('error', "Failed to submit request");
            }
            DB::commit();

            $msg = [
                'name' => $user->fullname,
            ];

            $adminAction = [
                "link" => route('admin.kyc.users.pending'),
                "icon" => "fas fa-user text-white"
            ];
            $userAction = [
                "link" => route('user.profile'),
                "icon" => "fas fa-user text-white"
            ];

            $this->adminPushNotification('ADMIN_NOTIFY_USER_ADDRESS_VERIFICATION_REQUEST', $msg, $adminAction);
            $this->userPushNotification($user, 'USER_NOTIFY_ADDRESS_VERIFICATION_REQUEST_SEND', $msg, $userAction);

            $currentDate = dateTime(Carbon::now());
            $this->sendMailSms($user, $type = 'USER_MAIL_ADDRESS_VERIFICATION_REQUEST_SEND', [
                'name' => $user->fullname,
                'date' => $currentDate,
            ]);

            $this->mailToAdmin($type = 'ADMIN_MAIL_USER_ADDRESS_VERIFICATION_REQUEST', [
                'name' => $user->fullname,
                'date' => $currentDate,
            ]);

            return redirect()->route('user.profile')->withErrors($validator)->with('success', 'Your request has been submitted.');

        } catch (\Exception $e) {
            $validator->errors()->add('addressVerification', '1');
            return redirect()->route('user.profile')->with('error', $e->getMessage())->withErrors($validator);
        }
    }

    public function badges()
    {
        $data['allBadges'] = Badge::with('details')->where('status', 1)->orderBy('sort_by', 'ASC')->get();
        return view($this->theme . 'user.badge.index', $data);
    }

    public function propertyMarket($type = null)
    {
        $data['properties'] = ManageProperty::with(['details', 'getInvestment', 'getAddress.details', 'getReviews'])
            ->withCount('getReviews')
            ->withCount('getFavourite')
            ->where('expire_date', '>', now())
            ->where('status', 1)
            ->latest()->paginate(config('basic.paginate'));

        $data['sharedProperties'] = PropertyShare::with(['getInvestment', 'property.getReviews', 'propertyOffer.offerlock'])->where('status', 1)->latest()->paginate(config('basic.paginate'));

        $data['myProperties'] = Investment::with(['property.getReviews', 'propertyShare', 'user'])->where('user_id', $this->user->id)->latest()->paginate(config('basic.paginate'));

        $data['mySharedProperties'] = PropertyShare::with(['getInvestment.propertyShare', 'property.getReviews'])->where('investor_id', $this->user->id)->where('status', 1)->latest()->paginate(config('basic.paginate'));

        $data['myOfferedProperties'] = PropertyOffer::with(['getInvestment.propertyShare', 'property.getReviews', 'propertyShare'])->where('offered_from', $this->user->id)->latest()->paginate(config('basic.paginate'));

        $data['receivedOfferedList'] = PropertyOffer::with(['getInvestment.propertyShare', 'property.getReviews', 'propertyShare', 'owner', 'user'])->where('offered_to', $this->user->id)->withCount('propertyShare')->groupBy('property_share_id')->latest()->paginate(config('basic.paginate'));
        return view($this->theme . 'user.property.index', $data, compact('type'));
    }

    public function offerList(Request $request, $id)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['allOfferList'] = PropertyOffer::with(['getInvestment.propertyShare', 'property.details', 'user'])
            ->when(isset($search['property']), function ($query) use ($search) {
                return $query->whereHas('property.details', function ($q) use ($search) {
                    $q->where('property_title', 'LIKE', '%' . $search['property'] . '%');
                });
            })
            ->when((isset($search['sort_by']) && $search['sort_by'] == 1), function ($query) use ($search) {
                return $query->orderBy('id', 'DESC');
            })
            ->when((isset($search['sort_by']) && $search['sort_by'] == 2), function ($query) use ($search) {
                return $query->orderBy('id', 'ASC');
            })
            ->when((isset($search['sort_by']) && $search['sort_by'] == 3), function ($query) use ($search) {
                return $query->orderBy('amount', 'DESC');
            })
            ->when((isset($search['sort_by']) && $search['sort_by'] == 4), function ($query) use ($search) {
                return $query->orderBy('amount', 'ASC');
            })
            ->when(isset($search['status']), function ($query) use ($search) {
                return $query->where('status', $search['status']);
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('property_share_id', $id)->where('offered_to', $this->user->id)->latest()->paginate(config('basic.paginate'));

        return view($this->theme . 'user.property.offerList', $data);
    }

    public function offerAccept($id)
    {
        $propertyOffer = PropertyOffer::where('offered_to', $this->user->id)->findOrFail($id);
        $propertyOffer->status = 1;
        $propertyOffer->save();
        return back()->with('success', 'Offer Accepted');
    }

    public function offerReject($id)
    {
        $propertyOffer = PropertyOffer::with('offerlock')->where('offered_to', $this->user->id)->findOrFail($id);

        if ($propertyOffer->offerlock && $propertyOffer->lockInfo() != null) {
            $lockOffer = $propertyOffer->lockInfo();
            $lockOffer->status = 2;
            $lockOffer->save();
        }

        $propertyOffer->status = 2;
        $propertyOffer->save();
        return back()->with('success', 'Offer Rejected');
    }

    public function offerRemove($id)
    {
        $propertyOffer = PropertyOffer::where('offered_to', $this->user->id)->findOrFail($id);
        $propertyOffer->delete();
        return back()->with('success', 'offer deleted successfully!');
    }

    public function offerConversation($id)
    {
        $data['singlePropertyOffer'] = PropertyOffer::with(['property.details', 'propertyShare', 'owner', 'user', 'offerlock', 'receiveMyOffer'])->findOrFail($id);
        return view($this->theme . 'user.property.offerConversation', $data, compact('id'));
    }

    public function offerReplyMessageRender(Request $request)
    {
        $messages = OfferReply::with('get_sender:id,firstname,lastname,username,image', 'get_receiver:id,firstname,lastname,username,image')->where('property_offer_id', $request->offerId)->orderBy('id', 'ASC')
            ->get()
            ->map(function ($item) {
                $image = getFile(config('location.user.path') . $item->get_sender->image);
                $item['sender_image'] = $image;
                return $item;
            })
            ->map(function ($item) {
                $image = getFile(config('location.user.path') . $item->get_receiver->image);
                $item['receiver_image'] = $image;
                return $item;
            })
            ->map(function ($item) {
                if (isset($item->file)) {
                    $file = getFile(config('location.message.path') . $item->file);
                    $item['fileImage'] = $file;
                }
                return $item;
            });

        $messages->push(auth()->user());

        return response()->json($messages);
    }

    public function offerReplyMessage(Request $request)
    {
        $this->validate($request, [
            'file' => 'nullable|mimes:jpg,png,jpeg,PNG|max:10000',
        ]);

        $offerReply = new OfferReply();
        $offerReply->sender_id = $this->user->id;
        $offerReply->receiver_id = $request->client_id;
        $offerReply->property_offer_id = $request->property_offer_id;
        $offerReply->reply = $request->reply;

        if ($request->hasFile('file')) {
            $messageFile = $this->uploadImage($request->file, config('location.message.path'));
            $offerReply->file = $messageFile;
            $fileImage = getFile(config('location.message.path') . $messageFile);
        } else {
            $fileImage = null;
        }

        $offerReply->save();

        $sender_image = getFile(config('location.user.path') . $this->user->image);

        $response = [
            'user_id' => $offerReply->sender_id,
            'client_id' => $offerReply->receiver_id,
            'property_offer_id' => $offerReply->property_offer_id,
            'reply' => $offerReply->reply,
            'fileImage' => $fileImage,
            'sender_image' => $sender_image,
        ];

        ChatEvent::dispatch((object)$response);

        return response()->json($response);
    }

    public function paymentLock(Request $request, $id)
    {

        $this->validate($request, [
            'amount' => 'required',
            'duration' => 'required',
        ]);

        $propertyOffer = PropertyOffer::where('offered_to', $this->user->id)->findOrFail($id);
        if ($propertyOffer->status == 2 || $propertyOffer->status == 0) {
            return back()->with('error', "Please accept the offer first. Then lock the payment");
        }

        $offerLock = new OfferLock();
        $offerLock->property_offer_id = $id;
        $offerLock->property_share_id = $propertyOffer->property_share_id;
        $offerLock->offer_amount = $propertyOffer->amount;
        $offerLock->lock_amount = $request->amount;
        $offerLock->duration = $request->duration;

        $offerLock->save();
        return back()->with('success', "Offer is locked successfully");
    }

    public function paymentLockUpdate(Request $request, $id)
    {
        $offerLock = OfferLock::findOrFail($id);
        $offerLock->lock_amount = $request->amount;
        $offerLock->duration = $request->duration;
        $offerLock->save();
        return back()->with('success', 'Payment lock successfully Updated');
    }

    public function paymentLockCancel(Request $request, $id)
    {
        $offerLock = OfferLock::findOrFail($id);
        $offerLock->status = 2;
        $offerLock->save();
        return back()->with('success', 'Payment lock successfully cancelled');
    }


    public function paymentLockConfirm(Request $request, $id)
    {
        $this->validate($request, [
            'balance_type' => 'required|string|max:50',
            'amount' => 'required|numeric',
        ]);

        $balance_type = $request->balance_type;
        if (!in_array($balance_type, ['balance', 'interest_balance', 'checkout'])) {
            return back()->with('error', 'Invalid Wallet Type');
        }

        if ($request->expired_time == 'expired'){
            return back()->with('error', 'Payment duration time is expired');
        }


        $user = $this->user;
        $offerLock = OfferLock::with('propertyOffer.property.details', 'propertyOffer.getInvestment', 'propertyOffer.propertyShare')->findOrFail($id);
        $amount = $offerLock->lock_amount;

        if ($amount > $user->$balance_type) {
            return back()->with('error', 'Insufficient Balance');
        }

        $new_balance = getAmount($user->$balance_type - $amount); // new balance = user new balance
        $user->$balance_type = $new_balance;
        $user->total_invest += $request->amount;
        $user->save();
        $trx = strRandom();
        $remarks = 'Share purchesed on ' . optional(optional(optional($offerLock->propertyOffer)->property)->details)->property_title;

        BasicService::makeTransaction($user, $amount, 0, $trx_type = '-', $balance_type, $trx, $remarks);

        $offerLock->status = 1;
        $offerLock->save(); // payment completed

        $propertyOffer = $offerLock->propertyOffer;
        $propertyOffer->status = 1; // offer completed
        $propertyOffer->payment_status = 1; // offer completed
        $propertyOffer->save();

        $investment = (optional($offerLock->propertyOffer)->getInvestment);
        $investment->user_id = $user->id; // transfer share
        $investment->save();

        $propertyShare = (optional($offerLock->propertyOffer)->propertyShare);
        $propertyShare->status = 0;
        $propertyShare->save(); // remove share market

        return back()->with('success', 'A share of' . '`' . optional(optional(optional($offerLock->propertyOffer)->property)->details)->property_title . '`' . 'property has been successfully purchased.');
    }


    public function sendComment(Request $request, $id)
    {
        $user = $this->user;
        $comment = new Comment();

        $comment->blog_id = $id;
        $comment->user_id = $user->id;
        $comment->comment = $request->comment;
        $comment->save();
        return back()->with('success', 'Comment added');
    }

    public function sendReply(Request $request, $id)
    {
        $user = $this->user;
        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->blog_id = $request->blog_id;
        $comment->parent_id = $id;
        $comment->comment = $request->reply;
        $comment->save();
        return back()->with('success', 'Reply Success');
    }

    public function BuyShare(Request $request, $id)
    {
        $this->validate($request, [
            'balance_type' => 'required|string|max:50',
            'amount' => 'required|numeric',
        ]);

        $balance_type = $request->balance_type;
        if (!in_array($balance_type, ['balance', 'interest_balance', 'checkout'])) {
            return back()->with('error', 'Invalid Wallet Type');
        }

        $user = $this->user;
        $propertyShare = PropertyShare::with('property.details', 'getInvestment')->findOrFail($id);

        $amount = $propertyShare->amount;

        if ($amount > $user->$balance_type) {
            return back()->with('error', 'Insufficient Balance');
        }

        $new_balance = getAmount($user->$balance_type - $amount); // new balance = user new balance
        $user->$balance_type = $new_balance;
        $user->total_invest += $request->amount;
        $user->save();
        $trx = strRandom();
        $remarks = 'Share purchesed on ' . optional(optional($propertyShare->property)->details)->property_title;

        BasicService::makeTransaction($user, $amount, 0, $trx_type = '-', $balance_type, $trx, $remarks);

        $propertyShare->status = 0;
        $propertyShare->save();

        $investment = $propertyShare->getInvestment;
        $investment->user_id = $user->id; // transfer share
        $investment->save();

        return redirect()->route('user.propertyMarket', 'my-investment-properties')->with('success', 'A share of' . '`' . optional(optional($propertyShare->property)->details)->property_title . '`' . 'property has been successfully purchased.');
    }

}
