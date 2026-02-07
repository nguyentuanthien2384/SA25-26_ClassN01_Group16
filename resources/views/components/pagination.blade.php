@php
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
@endphp

@if ($hasPages)
    @php
        $current = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : 1;
        $edge = 1; // always show first/last
        $around = 2; // show current +/- 2

        $last = $isLengthAware ? $paginator->lastPage() : null;
        $rangeStart = $isLengthAware ? max($edge + 1, $current - $around) : null;
        $rangeEnd = $isLengthAware ? min($last - $edge, $current + $around) : null;
    @endphp

    <nav class="pagination-custom" role="navigation" aria-label="Pagination">
        <ul>
            {{-- Prev --}}
            @if (method_exists($paginator, 'onFirstPage') && $paginator->onFirstPage())
                <li class="disabled pagination-prev"><span>&lt;</span></li>
            @else
                <li class="pagination-prev"><a href="{{ $paginator->previousPageUrl() }}">&lt;</a></li>
            @endif

            {{-- Numbered pagination only for LengthAwarePaginator --}}
            @if ($isLengthAware)
                @if ($current == 1)
                    <li class="active"><span>1</span></li>
                @else
                    <li><a href="{{ $paginator->url(1) }}">1</a></li>
                @endif

                @if ($rangeStart > $edge + 1)
                    <li class="disabled"><span>…</span></li>
                @endif

                @for ($page = $rangeStart; $page <= $rangeEnd; $page++)
                    @if ($page == $current)
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endfor

                @if ($rangeEnd < $last - $edge)
                    <li class="disabled"><span>…</span></li>
                @endif

                @if ($last > 1)
                    @if ($current == $last)
                        <li class="active"><span>{{ $last }}</span></li>
                    @else
                        <li><a href="{{ $paginator->url($last) }}">{{ $last }}</a></li>
                    @endif
                @endif
            @endif

            {{-- Next --}}
            @if (method_exists($paginator, 'hasMorePages') && $paginator->hasMorePages())
                <li class="pagination-next"><a href="{{ $paginator->nextPageUrl() }}">&gt;</a></li>
            @else
                <li class="disabled pagination-next"><span>&gt;</span></li>
            @endif
        </ul>
    </nav>
@endif
