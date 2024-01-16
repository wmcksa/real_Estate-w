<span>
    <?php
        $isCheck = 0;
        $j = 0;
    ?>

    <?php if($property->avgRating() != intval($property->avgRating())): ?>
        <?php
            $isCheck = 1;
        ?>
    <?php endif; ?>
    <?php for($i = $property->avgRating(); $i > $isCheck; $i--): ?>
        <i class="fas fa-star" aria-hidden="true"></i>
        <?php
            $j = $j + 1;
        ?>
    <?php endfor; ?>
    <?php if($property->avgRating() != intval($property->avgRating())): ?>
        <i class="fas fa-star-half-alt"></i>
        <?php
            $j = $j + 1;
        ?>
    <?php endif; ?>

    <?php if($property->avgRating() == 0 || $property->avgRating() != null): ?>
        <?php for($j; $j < 5; $j++): ?>
            <i class="far fa-star"></i>
        <?php endfor; ?>
    <?php endif; ?>
</span>

<span>(<?php echo e($property->get_reviews_count <= 1 ? ($property->get_reviews_count. trans(' review')) : ($property->get_reviews_count. trans(' reviews'))); ?>)</span>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/partials/propertyReview.blade.php ENDPATH**/ ?>