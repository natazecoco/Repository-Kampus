<aside class="mb-12 lg:mb-0 lg:col-span-4">
    <div class="sticky top-28 space-y-10">
        
        <!-- Taksonomi / Kategori Utama dengan Count -->
        <div class="rounded-3xl bg-slate-50/50 p-6 border border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-900 border-b border-slate-200/60 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gundar-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Topik Populer
            </h3>
            <div class="mt-5 flex flex-wrap gap-2.5">
                @foreach($topics->take(10) as $topic)
                    <a href="{{ route('topic.show', $topic->slug) }}" class="group inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white pl-3 pr-1.5 py-1.5 text-xs font-semibold text-slate-600 transition-all hover:border-gundar-primary/40 hover:shadow-[0_4px_12px_rgba(118,58,151,0.06)] hover:-translate-y-0.5">
                        <span class="group-hover:text-gundar-primary transition-colors">{{ $topic->name }}</span>
                        <span class="flex items-center justify-center min-w-[20px] rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-500 group-hover:bg-gundar-primary/10 group-hover:text-gundar-primary transition-colors">{{ $topic->publications_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Hierarki Topik Bertingkat (Parent & Child) -->
        @if($taxonomyTopics->isNotEmpty() && !$search)
            <div class="px-2 pt-2">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-5">Hierarki Direktori</h3>
                <div class="space-y-5">
                    @foreach($taxonomyTopics as $parentTopic)
                        <div class="group">
                            <a href="{{ route('topic.show', $parentTopic->slug) }}" class="flex items-center gap-2 text-sm font-bold text-gundar-dark hover:text-gundar-primary transition-colors">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 group-hover:bg-gundar-primary transition-colors"></span>
                                {{ $parentTopic->name }}
                            </a>
                            @if($parentTopic->children->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2 border-l-2 border-slate-100 ml-0.5 pl-4">
                                    @foreach($parentTopic->children as $childTopic)
                                        <a href="{{ route('topic.show', $childTopic->slug) }}" class="rounded-lg bg-white border border-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500 hover:border-gundar-primary/40 hover:bg-gundar-primary/5 hover:text-gundar-primary transition-all shadow-sm">
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