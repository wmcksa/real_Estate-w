<span>
    <?php
        $isCheck = 0;
        $j = 0;
    ?>

    <?php if($average_review != intval($average_review)): ?>
        <?php
            $isCheck = 1;
        ?>
    <?php endif; ?>
    <?php for($i = $average_review; $i > $isCheck; $i--): ?>
        <i class="fas fa-star" aria-hidden="true"></i>
        <?php
            $j = $j + 1;
        ?>
    <?php endfor; ?>
    <?php if($average_review != intval($average_review)): ?>
        <i class="fas fa-star-half-alt"></i>
        <?php
            $j = $j + 1;
        ?>
    <?php endif; ?>

    <?php if($average_review == 0 || $average_review != null): ?>
        <?php for($j; $j < 5; $j++): ?>
            <i class="far fa-star"></i>
        <?php endfor; ?>
    <?php endif; ?>
</span>
<?php /**PATH /home/u643910891/domains/wmc-ksa.com/public_html/real/resources/views/themes/original/partials/review.blade.php ENDPATH**/ ?>