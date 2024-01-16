<!-- footer section -->
<footer class="footer-section">
    <div class="overlay">
        <div class="container">
            <div class="row gy-5 gy-lg-0">
                <div class="col-lg-3 col-md-6 pe-lg-5">
                    <div class="footer-box">
                        <a class="navbar-brand" href="{{url('/')}}"> <img
                                src="{{getFile(config('location.logoIcon.path').'logo.png')}}"
                                alt="{{config('basic.site_title')}}"/></a>
                        @if(isset($contactUs['contact-us'][0]) && $contact = $contactUs['contact-us'][0])
                            <p class="company-bio">
                                @lang(strip_tags(@$contact->description->footer_short_details))
                            </p>
                        @endif
                        @if(isset($contentDetails['social']))
                            <div class="social-links">
                                @foreach($contentDetails['social'] as $data)
                                    <a href="{{@$data->content->contentMedia->description->link}}" target="_blank" class="facebook">
                                        <i class="{{@$data->content->contentMedia->description->icon}}"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4>{{trans('Useful Links')}}</h4>
                        <ul>
                            <li>
                                <a href="{{route('home')}}">@lang('Home')</a>
                            </li>
                            <li>
                                <a href="{{route('about')}}">@lang('About')</a>
                            </li>

                            <li>
                                <a href="{{route('contact')}}">@lang('Contact')</a>
                            </li>

                            @isset($contentDetails['support'])
                                @foreach($contentDetails['support'] as $data)
                                    <li>
                                        <a href="{{route('getLink', [slug($data->description->title), $data->content_id])}}">@lang(optional($data->description)->title)</a>
                                    </li>
                                @endforeach
                            @endisset
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4>@lang('Contact us')</h4>
                        <ul>
                            <li><i class="fal fa-map-marker-alt"></i> <span>@lang(@$contact->description->address)</span></li>
                            <li><i class="fal fa-envelope"></i> <span>@lang(@$contact->description->email)</span></li>
                            <li><i class="fal fa-phone-alt"></i> <span>@lang(@$contact->description->phone)</span></li>
                        </ul>
                    </div>
                </div>
                @if(isset($templates['news-letter'][0]) && $newsLetter = $templates['news-letter'][0])
                    <div class="col-lg-3 col-md-6">
                    <div class="footer-box">
                        <h4>@lang(@$newsLetter->description->title)</h4>
                        <form action="{{route('subscribe')}}" method="post">
                            @csrf
                            <div class="input-group">
                                <input type="email" name="email" class="form-control" placeholder="@lang('Email Address')">
                                <button type="submit"><i class="fal fa-paper-plane" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            <div class="d-flex flex-wrap copyright justify-content-between">
                <div>
                    <span> @lang('Copyright') &copy; {{date('Y')}} <a href="{{url('/')}}">@lang($basic->site_title)</a> @lang('All Rights Reserved') </span>
                </div>
                @php
                    $languageArray = json_decode($languages, true);
                @endphp
                <div class="language {{(session()->get('rtl') == 1) ? 'text-md-start': 'text-md-end'}}">
                    @foreach ($languageArray as $key => $lang)
                        <a href="{{route('language',$key)}}" class="language">{{$lang}}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
