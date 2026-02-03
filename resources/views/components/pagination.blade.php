@if ($paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $edge = 1; // always show first/last
        $around = 2; // show current +/- 2

        $rangeStart = max($edge + 1, $current - $around);
        $rangeEnd = min($last - $edge, $current + $around);
    @endphp
    <nav class="pagination-custom" role="navigation" aria-label="Pagination">
        <ul>
            @if ($paginator->onFirstPage())
                <li class="disabled pagination-prev"><span>&lt;</span></li>
            @else
                <li class="pagination-prev"><a href="{{ $paginator->previousPageUrl() }}">&lt;</a></li>
            @endif

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

            @if ($paginator->hasMorePages())
                <li class="pagination-next"><a href="{{ $paginator->nextPageUrl() }}">&gt;</a></li>
            @else
                <li class="disabled pagination-next"><span>&gt;</span></li>
            @endif
        </ul>
    </nav>
@endif
