@extends($theme.'layouts.app')
@section('title',trans('Blog Details'))

@section('content')
    <!-- blog section  -->
    <section class="blog-page blog-details">
        <div class="container">
            <div class="row g-lg-5">
                <div class="col-lg-8">
                    <div class="blog-box">
                        <div class="img-box">
                            <img src="{{ getFile(config('location.blog.path'). @$singleBlog->image) }}"
                                 alt="@lang('not found')" class="img-fluid"/>
                        </div>
                        <div class="text-box">
                            <div class="date-author">
                                <span><i class="fal fa-clock"></i> {{ dateTime($singleBlog->created_at, 'M d, Y') }} </span>
                                <span><i class="fal fa-user-circle"></i> @lang(@$singleBlog->details->author) </span>
                            </div>
                            <h5 class="title">@lang(@$singleBlog->details->title)</h5>
                            <p>
                                @lang(@$singleBlog->details->details)
                            </p>
                        </div>
                    </div>
                    <div id="shareBlock"><h4>@lang('Share now')</h4></div>
                    <div class="all-comment">
                        <h4>@lang('Comments') ({{$totlaComments}})</h4>
                        @foreach($allComments as $Key => $value)
                            <div class="comment-box">
                                <div class="img-box">
                                    <img class="img-fluid"
                                         src="{{getFile(config('location.user.path').@$value->user->image)}}" alt="@lang('not found')"/>
                                </div>
                                <div class="text-box">
                                    <h5 class="name">@lang(@$value->user->fullname)</h5>
                                    <p class="date">{{ dateTime($value->created_at) }}</p>
                                    <p class="mt-3">
                                        {{ $value->comment }}
                                    </p>
                                    <button class="reply-btn replyBtn" data-id="{{ $value->id }}"><i
                                            class="fal fa-reply"></i> @lang('Reply')</button>
                                    @foreach($value->replies as $key => $reply)
                                        <div class="comment-box">
                                            <div class="img-box">
                                                <img class="img-fluid" src="{{getFile(config('location.user.path').@$reply->user->image)}}" alt="@lang('not found')"/>
                                            </div>
                                            <div class="text-box">
                                                <h5 class="name">@lang(@$reply->user->fullname)</h5>
                                                <p class="date">{{ dateTime($reply->created_at) }}</p>
                                                <p class="mt-3">
                                                    @lang(@$reply->comment)
                                                </p>
                                                <button class="reply-btn SecondReplyBtn" data-id="{{ $reply->id }}"><i class="fal fa-reply"></i> @lang('Reply') </button>

                                                @foreach($reply->replies as $key => $senconReply)
                                                    <div class="comment-box">
                                                        <div class="img-box">
                                                            <img class="img-fluid" src="{{getFile(config('location.user.path').@$senconReply->user->image)}}" alt="@lang('not found')"/>
                                                        </div>
                                                        <div class="text-box">
                                                            <h5 class="name">@lang(@$senconReply->user->fullname)</h5>
                                                            <p class="date">{{ dateTime($senconReply->created_at) }}</p>
                                                            <p class="mt-3">
                                                                @lang(@$senconReply->comment)
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="secondReplyForm{{ $reply->id }} d-none">
                                                    <form action="{{ route('user.sendReply', $reply->id) }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="blog_id" value="{{ $value->blog_id }}">
                                                        <div class="input-box mb-4">
                                                 <textarea
                                                     class="form-control"
                                                     name="reply"
                                                     id="exampleFormControlTextarea1"
                                                     rows="5"
                                                     placeholder=""
                                                 ></textarea>
                                                        </div>
                                                        <button class="btn-custom" type="submit">@lang('Send Reply')</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach


                                    <div class="replyForm{{ $value->id }} d-none" id="replyForm">
                                        <form action="{{ route('user.sendReply', $value->id) }}" method="post">
                                            @csrf
                                            <input type="hidden" name="blog_id" value="{{ $value->blog_id }}">
                                            <div class="input-box mb-4">
                                                 <textarea
                                                     class="form-control"
                                                     name="reply"
                                                     id="exampleFormControlTextarea1"
                                                     rows="5"
                                                     placeholder=""
                                                 ></textarea>
                                            </div>
                                            <button class="btn-custom" type="submit">@lang('Send Reply')</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="comment-section">
                        <form action="{{ route('user.sendComment', $singleBlog->id) }}" method="post">
                            @csrf
                            <h4>@lang('Leave a comment')</h4>
                            <div class="row g-3">

                                <div class="input-box col-12">
                                    <textarea class="form-control" name="comment" cols="30" rows="3"
                                              placeholder="@lang('Write Message')"></textarea>
                                    @error('name')
                                    <span class="text-danger">{{ $message  }}</span>
                                    @enderror
                                </div>
                                <div class="input-box col-12">
                                    <button class="btn-custom" type="submit">@lang('Post Comment')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="side-bar">
                        <div class="side-box">
                            <form action="{{ route('blogSearch') }}" method="get">
                                <h4>@lang('Search here')</h4>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" id="search"
                                           placeholder="@lang('search')"/>
                                    <button type="submit"><i class="fal fa-search" aria-hidden="true"></i></button>
                                </div>
                            </form>
                        </div>
                        @if (count($relatedBlogs) > 0)
                            <div class="side-box">
                                <h4>@lang('Related Posts')</h4>
                                @foreach ($relatedBlogs as $blog)
                                    <div class="blog-box">
                                        <div class="img-box">
                                            <img class="img-fluid"
                                                 src="{{ getFile(config('location.blog.path'). @$blog->image) }}"
                                                 alt="@lang('not found')"/>
                                        </div>
                                        <div class="text-box">
                                            <span class="date">{{ dateTime(@$blog->created_at, 'M d, Y') }}</span>
                                            <a href="{{route('blogDetails',[slug(@$blog->details->title), $blog->id])}}"
                                               class="title"
                                            >{{ \Illuminate\Support\Str::limit(optional(@$blog->details)->title, 100) }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="side-box">
                            <h4>@lang('categories')</h4>
                            <ul class="links">
                                @foreach ($allBlogCategory as $category)
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

@endsection


@push('script')
    <script>
        'use strict'
            $('.replyBtn').on('click', function () {
                let commentId = $(this).data("id");

                $('.replyForm'+commentId).removeClass('d-none');
            });

            $('.SecondReplyBtn').on('click', function () {
                    let replyId = $(this).data("id");

                    $('.secondReplyForm'+replyId).removeClass('d-none');
            });
    </script>
@endpush
