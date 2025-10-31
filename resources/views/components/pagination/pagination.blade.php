@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center mt-5">
        <ul class="flex flex-wrap items-center justify-center gap-2 text-sm font-medium select-none">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li
                    class="px-4 py-2 border border-gray-400 text-gray-400 cursor-not-allowed rounded-md flex items-center justify-center">
                    ‹
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 flex items-center justify-center transition">
                        ‹
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li
                        class="px-4 py-2 border border-gray-400 text-gray-500 rounded-md flex items-center justify-center">
                        {{ $element }}
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li
                                class="px-4 py-2 border border-[#1B3C53] bg-[#1B3C53] text-white rounded-md flex items-center justify-center">
                                {{ $page }}
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                    class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 flex items-center justify-center transition">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="px-4 py-2 border border-gray-400 text-gray-700 rounded-md hover:bg-gray-100 flex items-center justify-center transition">
                        ›
                    </a>
                </li>
            @else
                <li
                    class="px-4 py-2 border border-gray-400 text-gray-400 cursor-not-allowed rounded-md flex items-center justify-center">
                    ›
                </li>
            @endif
        </ul>
    </nav>
@endif
