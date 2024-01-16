@if (count($popularBlogs) > 0)
    <section class="blog-section">
        <div class="container">
            @if(isset($templates['blog'][0]) && $blog = $templates['blog'][0])
                <div class="row">
                    <div class="col-12">
                        <div class="header-text text-center">
                            <h5>@lang(optional($blog->description)->title)</h5>
                            <h2>@lang(optional($blog->description)->sub_title)</h2>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row g-4 g-lg-5">
                @foreach ($popularBlogs as $blog)
                    <div class="col-lg-4 col-md-6">
                        <div
                            class="blog-box"
                            data-aos="fade-up"
                            data-aos-duration="1000"
                            data-aos-anchor-placement="center-bottom"
                        >
                            <div class="img-box">
                                <img class="img-fluid"
                                     src="{{ getFile(config('location.blog.path'). $blog->image) }}"
                                     alt="@lang('not found')"/>
                            </div>
                            <div class="text-box">
                                <div class="date">
                                    <span>{{ dateTime($blog->created_at, 'M d, Y') }}</span>
                                </div>
                                <div class="author">
                                    <span><i class="fal fa-user-circle"></i> @lang(optional($blog->details)->author) </span>
                                </div>
                                <a href="{{route('blogDetails',[slug(optional($blog->details)->title), $blog->id])}}" class="title">{{ \Illuminate\Support\Str::limit(optional($blog->details)->title, 80) }}</a>
                                <p>{{ \Illuminate\Support\Str::limit(optional($blog->details)->description, 80) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
