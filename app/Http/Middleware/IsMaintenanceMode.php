<?php

namespace App\Http\Middleware;

use App\Models\Template;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class IsMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->theme = template();
        $basic = (object)config('basic');

        if ($basic->maintenance_mode == 1){
            $templateSection = ['maintenance-page'];
            $data['templates'] = Template::templateMedia()->whereIn('section_name', $templateSection)->get()->groupBy('section_name');
            return new response(view($this->theme.'site.maintenance', $data));
        }

        return $next($request);
    }
}
