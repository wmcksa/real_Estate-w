@section('title', trans('Maintenance'))

<!DOCTYPE html>
<!--[if lt IE 7 ]>
<html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>
<html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>
<html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html class="no-js" lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif >

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/bootstrap.min.css')}}"/>

    <link rel="stylesheet" type="text/css" href="{{asset($themeTrue.'css/style.css')}}">
    <script src="{{asset($themeTrue.'js/fontawesomepro.js')}}"></script>
    <script src="{{asset($themeTrue.'js/modernizr.custom.js')}}"></script>
    @include('partials.seo')
</head>

<body>

    @if(isset($templates['maintenance-page'][0]) && $maintenancePage = $templates['maintenance-page'][0])
        <div class="maintenance">
        <div class="maintenance_contain">
            <img src="{{getFile(config('location.content.path').optional($maintenancePage->templateMedia())->image)}}" alt="@lang('maintenance')">
            <span class="pp-infobox-title-prefix">@lang(optional($maintenancePage->description)->title)</span>
            <div class="pp-infobox-title-wrapper">
                <h3 class="pp-infobox-title">@lang(optional($maintenancePage->description)->sub_title)</h3>
            </div>

            <div class="pp-infobox-description">
                <p>
                    @lang(optional($maintenancePage->description)->short_description)
                </p>
            </div>
            @if(isset($contentDetails['social']))
                <div class="social-links mt-3">
                    <div class="pp-social-icons pp-social-icons-center pp-responsive-center">
                        @foreach($contentDetails['social'] as $data)
                            <span class="pp-social-icon">
                            <link itemprop="url" href="{{optional($data->content->contentMedia->description)->link}}">
                            <a itemprop="sameAs" href="{{optional($data->content->contentMedia->description)->link}}" target="_blank" title="{{optional($data->description)->name}}" aria-label="Facebook" role="button">
                              <i class="{{optional($data->content->contentMedia->description)->icon}}"></i>
                            </a>
                        </span>
                        @endforeach
                    </div>

                </div>
            @endif
        </div>
    </div>
    @endif


<script src="{{asset($themeTrue.'js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset($themeTrue.'js/jquery-3.6.0.min.js')}}"></script>
<!-- custom script -->
<script src="{{asset($themeTrue.'js/script.js')}}"></script>

</body>

</html>
