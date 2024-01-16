<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\ManageProperty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Traits\Notify;
use App\Http\Traits\Upload;

class FavouriteController extends Controller
{
    use Notify, Upload;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $this->user = auth()->user();
            return $next($request);
        });
        $this->theme = template();
    }

    public function wishList(Request $request)
    {
        $userId = $this->user->id;
        $property = ManageProperty::with('getFavourite')->find($request->property_id);

        if ($property->getFavourite) {
            $stage='remove';
            $favourite = Favourite::where('property_id',$request->property_id)->where('client_id', $this->user->id)->first();
            $favourite->delete();

        } else {
            $stage ='added';
            $data = new Favourite();
            $data->client_id = $this->user->id;
            $data->property_id =$request->property_id;
            $data->save();
        }
        return response()->json([
            'data' => $stage
        ]);
    }

    public function wishListProperty(Request $request)
    {
        $search = $request->all();
        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date)->addDay();

        $data['favourite_properties'] = Favourite::with(['get_property.details'])
            ->when(isset($search['name']), function ($query) use ($search) {
                return $query->whereHas('get_property.details', function ($query) use ($search) {
                    $query->where('property_title', 'LIKE', "%{$search['name']}%");
                });
            })
            ->when(isset($search['from_date']), function ($q2) use ($fromDate) {
                return $q2->whereDate('created_at', '>=', $fromDate);
            })
            ->when(isset($search['to_date']), function ($q2) use ($fromDate, $toDate) {
                return $q2->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('client_id', $this->user->id)
            ->latest()
            ->paginate(config('basic.paginate'));
        return view($this->theme . 'user.wishList', $data);
    }

    public function favouriteListingDelete($id)
    {

        Favourite::where('client_id', $this->user->id)->findOrfail($id)->delete();
        return back()->with('success', __('Delete Successful!'));
    }
}
