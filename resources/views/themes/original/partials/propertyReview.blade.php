<span>
    @php
        $isCheck = 0;
        $j = 0;
    @endphp

    @if($property->avgRating() != intval($property->avgRating()))
        @php
            $isCheck = 1;
        @endphp
    @endif
    @for($i = $property->avgRating(); $i > $isCheck; $i--)
        <i class="fas fa-star" aria-hidden="true"></i>
        @php
            $j = $j + 1;
        @endphp
    @endfor
    @if($property->avgRating() != intval($property->avgRating()))
        <i class="fas fa-star-half-alt"></i>
        @php
            $j = $j + 1;
        @endphp
    @endif

    @if($property->avgRating() == 0 || $property->avgRating() != null)
        @for($j; $j < 5; $j++)
            <i class="far fa-star"></i>
        @endfor
    @endif
</span>

<span>({{ $property->get_reviews_count <= 1 ? ($property->get_reviews_count. trans(' review')) : ($property->get_reviews_count. trans(' reviews')) }})</span>
