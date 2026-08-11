<aside class="mb-12 lg:mb-0 lg:col-span-4">
    <div class="sticky top-28 space-y-6" x-data="{ openParent: null }">
        <div class="rounded-3xl border border-slate-100 bg-slate-50/50 p-5">
            <h3 class="flex items-center gap-2 border-b border-slate-200/60 pb-3 text-xs font-black uppercase tracking-[0.2em] text-slate-900">
                <svg class="h-4 w-4 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.024 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Jelajahi Topik
            </h3>
            @if($taxonomyTopics->isNotEmpty() && !$search)
                <div class="mt-4 space-y-2">
                    @foreach($taxonomyTopics as $parentTopic)
                        <div class="rounded-2xl border border-slate-200/70 bg-white/70">
                            <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                                <a href="{{ route('topic.show', $parentTopic->slug) }}" class="min-w-0 text-sm font-bold text-gundar-dark transition-colors hover:text-gundar-primary">
                                    {{ $parentTopic->name }}
                                </a>
                                @if($parentTopic->children->isNotEmpty())
                                    <button type="button" @click="openParent = openParent === {{ $parentTopic->id }} ? null : {{ $parentTopic->id }}" class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-gundar-primary/10 hover:text-gundar-primary" :aria-expanded="openParent === {{ $parentTopic->id }}" title="Tampilkan subtopik">
                                        <svg class="h-4 w-4 transition-transform" :class="openParent === {{ $parentTopic->id }} ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" /></svg>
                                    </button>
                                @endif
                            </div>
                            @if($parentTopic->children->isNotEmpty())
                                <div x-cloak x-show="openParent === {{ $parentTopic->id }}" x-collapse class="space-y-1 border-t border-slate-100 px-3 pb-3 pt-2">
                                    @foreach($parentTopic->children as $childTopic)
                                        <a href="{{ route('topic.show', $childTopic->slug) }}" class="block rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-500 transition hover:bg-gundar-primary/5 hover:text-gundar-primary">
                                            {{ $childTopic->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($popularTopics->isNotEmpty())
            <div class="px-2">
                <h3 class="mb-4 text-xs font-black uppercase tracking-[0.2em] text-slate-400">Topik Terpopuler</h3>
                <div class="space-y-1">
                    @foreach($popularTopics as $topic)
                        <a href="{{ route('topic.show', $topic->slug) }}" class="group flex items-center justify-between gap-3 rounded-xl px-2 py-2 text-sm transition hover:bg-white">
                            <span class="truncate font-semibold text-slate-600 group-hover:text-gundar-primary">{{ $topic->name }}</span>
                            <span class="shrink-0 text-xs font-bold text-slate-400">{{ $topic->publications_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</aside>