@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination"
     class="flex items-center justify-between px-1 py-3 select-none">

    {{-- Result count --}}
    <p class="text-sm text-gray-500">
        Showing
        @if ($paginator->firstItem())
            <span class="font-medium text-gray-700">{{ $paginator->firstItem() }}</span>
            to
            <span class="font-medium text-gray-700">{{ $paginator->lastItem() }}</span>
        @else
            {{ $paginator->count() }}
        @endif
        of
        <span class="font-medium text-gray-700">{{ $paginator->total() }}</span>
        results
    </p>

    {{-- Page buttons --}}
    <div class="flex items-center">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="relative inline-flex items-center px-2 py-2 rounded-l-lg border border-gray-200 bg-white text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="relative inline-flex items-center px-4 py-2 -ml-px border border-gray-200 bg-white text-sm text-gray-400 cursor-default">
                    {{ $element }}
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="relative inline-flex items-center px-4 py-2 -ml-px border border-blue-500 bg-blue-500 text-sm font-semibold text-white cursor-default z-10">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="relative inline-flex items-center px-4 py-2 -ml-px border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-300 transition-all duration-150">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="relative inline-flex items-center px-2 py-2 -ml-px rounded-r-lg border border-gray-200 bg-white text-gray-400 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition-all duration-150">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="relative inline-flex items-center px-2 py-2 -ml-px rounded-r-lg border border-gray-200 bg-white text-gray-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif

    </div>
</nav>
@endif