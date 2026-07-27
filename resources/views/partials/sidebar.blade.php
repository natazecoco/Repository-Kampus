<aside class="mb-12 lg:mb-0 lg:col-span-4">
    <div class="sticky top-28 space-y-10">
        
        <!-- Taksonomi / Kategori Utama dengan Count -->
        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 border-b border-slate-200 pb-2">Topik Populer</h3>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($topics->take(10) as $topic)
                    <a href="{{ route('topic.show', $topic->slug) }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-gundar-primary hover:text-gundar-primary shadow-sm">
                        <span>{{ $topic->name }}</span>
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] text-slate-400">{{ $topic->publications_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Hierarki Topik Bertingkat (Parent & Child) -->
        @if($taxonomyTopics->isNotEmpty() && !$search)
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 mb-4">Hierarki Topik</h3>
                <div class="space-y-4">
                    @foreach($taxonomyTopics as $parentTopic)
                        <div>
                            <a href="{{ route('topic.show', $parentTopic->slug) }}" class="text-sm font-bold text-gundar-dark hover:text-gundar-primary transition">{{ $parentTopic->name }}</a>
                            @if($parentTopic->children->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-1.5 border-l-2 border-slate-100 pl-3">
                                    @foreach($parentTopic->children as $childTopic)
                                        <a href="{{ route('topic.show', $childTopic->slug) }}" class="rounded bg-white border border-slate-200 px-2 py-1 text-[10px] font-bold text-slate-500 hover:border-gundar-primary hover:text-gundar-primary transition shadow-sm">
                                            {{ $childTopic->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</aside>