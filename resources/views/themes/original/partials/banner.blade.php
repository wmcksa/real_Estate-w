
<style>
    .banner-section {
        background: linear-gradient( 170deg, rgb(255,255,255, .93), rgb(255, 255, 255, .2)), url({{getFile(config('location.logo.path').'banner.jpg')}});
    }
</style>

@if(!request()->routeIs('home'))
    <!-- banner section -->
    <section class="banner-section">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>@yield('title')</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
