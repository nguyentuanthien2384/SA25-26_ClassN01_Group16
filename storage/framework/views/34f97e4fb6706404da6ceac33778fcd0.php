<?php
    /**
     * Pagination component supports BOTH:
     * - LengthAwarePaginator (paginate)  -> has total(), lastPage()
     * - Paginator (simplePaginate)       -> NO total(), NO lastPage()
     *
     * Avoid calling total()/lastPage() when not available (would forward to Collection and crash).
     */
    $isObject = is_object($paginator);
    $hasPages = $isObject && method_exists($paginator, 'hasPages') ? $paginator->hasPages() : false;
    $isLengthAware = $isObject && method_exists($paginator, 'total') && method_exists($paginator, 'lastPage');
?>

<?php if($hasPages): ?>
    <?php
        $current = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : 1;
        $edge = 1; // always show first/last
        $around = 2; // show current +/- 2

        $last = $isLengthAware ? $paginator->lastPage() : null;
        $rangeStart = $isLengthAware ? max($edge + 1, $current - $around) : null;
        $rangeEnd = $isLengthAware ? min($last - $edge, $current + $around) : null;
    ?>

    <nav class="pagination-custom" role="navigation" aria-label="Pagination">
        <ul>
            
            <?php if(method_exists($paginator, 'onFirstPage') && $paginator->onFirstPage()): ?>
                <li class="disabled pagination-prev"><span>&lt;</span></li>
            <?php else: ?>
                <li class="pagination-prev"><a href="<?php echo e($paginator->previousPageUrl()); ?>">&lt;</a></li>
            <?php endif; ?>

            
            <?php if($isLengthAware): ?>
                <?php if($current == 1): ?>
                    <li class="active"><span>1</span></li>
                <?php else: ?>
                    <li><a href="<?php echo e($paginator->url(1)); ?>">1</a></li>
                <?php endif; ?>

                <?php if($rangeStart > $edge + 1): ?>
                    <li class="disabled"><span>…</span></li>
                <?php endif; ?>

                <?php for($page = $rangeStart; $page <= $rangeEnd; $page++): ?>
                    <?php if($page == $current): ?>
                        <li class="active"><span><?php echo e($page); ?></span></li>
                    <?php else: ?>
                        <li><a href="<?php echo e($paginator->url($page)); ?>"><?php echo e($page); ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if($rangeEnd < $last - $edge): ?>
                    <li class="disabled"><span>…</span></li>
                <?php endif; ?>

                <?php if($last > 1): ?>
                    <?php if($current == $last): ?>
                        <li class="active"><span><?php echo e($last); ?></span></li>
                    <?php else: ?>
                        <li><a href="<?php echo e($paginator->url($last)); ?>"><?php echo e($last); ?></a></li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if(method_exists($paginator, 'hasMorePages') && $paginator->hasMorePages()): ?>
                <li class="pagination-next"><a href="<?php echo e($paginator->nextPageUrl()); ?>">&gt;</a></li>
            <?php else: ?>
                <li class="disabled pagination-next"><span>&gt;</span></li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH D:\Web_Ban_Do_Dien_Tu\resources\views/components/pagination.blade.php ENDPATH**/ ?>