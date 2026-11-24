@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 1);
        $end = min($last, $current + 1);
        if ($current === 1) $end = min($last, 3);
        if ($current === $last) $start = max(1, $last - 2);
    @endphp
    <nav aria-label="Pagination">
        <div class="eh-pagination">
            @if ($paginator->onFirstPage())
                <span class="is-disabled" aria-disabled="true" aria-label="Previous page">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&laquo;</a>
            @endif

            @for ($page = $start; $page <= $end; $page++)
                @if ($page === $current)
                    <span class="is-active" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($end < $last - 0)
                @if ($end < $last - 1)
                    <span class="is-ellipsis" aria-hidden="true">...</span>
                @endif
                @if ($last > $end)
                    @if ($last === $current)
                        <span class="is-active" aria-current="page">{{ $last }}</span>
                    @else
                        <a href="{{ $paginator->url($last) }}">{{ $last }}</a>
                    @endif
                @endif
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&raquo;</a>
            @else
                <span class="is-disabled" aria-disabled="true" aria-label="Next page">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
