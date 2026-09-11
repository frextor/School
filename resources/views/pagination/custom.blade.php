@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        <span class="pagination-info">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} sur {{ $paginator->total() }}
        </span>

        <span class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="pagination-item disabled" aria-hidden="true">&lsaquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-item" rel="prev">&lsaquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-item disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-item active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-item" rel="next">&rsaquo;</a>
            @else
                <span class="pagination-item disabled" aria-hidden="true">&rsaquo;</span>
            @endif
        </span>
    </nav>
@endif
