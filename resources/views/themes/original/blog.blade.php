@extends($theme.'layouts.app')
@section('title', trans($title))

@section('content')
    @if (count($allBlogs) > 0)
        <!-- blog section  -->
        <section class="blog-page blog-details">
            <div class="container">
                <div class="row g-lg-5">
                    <div class="col-lg-8">
                        @forelse ($allBlogs as $blog)
                            <div class="blog-box">
                                <div class="img-box">
                                    <img src="{{ getFile(config('location.blog.path'). @$blog->image) }}"
                                         class="img-fluid" alt="@lang('blog image')"/>
                                </div>
                                <div class="text-box">
                                    <div class="date-author">
                                        <span><i class="fal fa-clock"></i> {{ dateTime(@$blog->created_at, 'M d, Y') }} </span>
                                        <span><i class="fal fa-user-circle"></i> @lang(optional(@$blog->details)->author) </span>
                                    </div>
                                    <a href="{{route('blogDetails',[slug(@$blog->details->title), $blog->id])}}"
                                       class="title">{{ \Illuminate\Support\Str::limit(optional(@$blog->details)->title, 100) }}</a>
                                    <p>
                                        {{Illuminate\Support\Str::limit(strip_tags(optional(@$blog->details)->details),500)}}
                                    </p>
                                    <a href="{{route('blogDetails',[slug(@$blog->details->title), $blog->id])}}"
                                       class="btn-custom mt-3">@lang('Read more')</a>
                                </div>
                            </div>
                        @empty
                        @endforelse

                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                {{ $allBlogs->links() }}
                            </ul>
                        </nav>
                    </div>
                    <div class="col-lg-4">
                        <div class="side-bar">
                            <div class="side-box">
                                <form action="{{ route('blogSearch') }}" method="get">
                                    <h4>@lang('Search here')</h4>

                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" id="search"
                                               placeholder="@lang('search')" value="{{ old('value', request()->search) }}"/>
                                        <button type="submit"><i class="fal fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </form>
                            </div>
                            <div class="side-box">
                                <h4>@lang('Recent Blogs')</h4>
                                @foreach ($allBlogs as $blog)
                                    <div class="blog-box">
                                        <div class="img-box">
                                            <img class="img-fluid" src="{{ getFile(config('location.blog.path'). @$blog->image) }}" alt="@lang('blog image')"/>
                                        </div>
                                        <div class="text-box">
                                            <span class="date">{{ dateTime(@$blog->created_at, 'M d, Y') }}</span>
                                            <a href="{{route('blogDetails',[slug(@$blog->details->title), $blog->id])}}" class="title">{{ \Illuminate\Support\Str::limit(optional(@$blog->details)->title, 40) }}</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="side-box">
                                <h4>@lang('Categories')</h4>
                                <ul class="links">
                                    @foreach ($blogCategory as $category)
                                        <li>
                                            <a href="{{ route('CategoryWiseBlog', [slug(@$category->details->name), $category->id]) }}">@lang(optional(@$category->details)->name)</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <div class="custom-not-found">
            <img src="{{ asset($themeTrue.'img/no_data_found.png') }}" alt="not found"
                 class="img-fluid">
        </div>
    @endif
@endsection
