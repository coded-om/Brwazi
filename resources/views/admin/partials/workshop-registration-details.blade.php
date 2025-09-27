<div class="space-y-4 text-sm">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-slate-500 text-xs">الاسم</div>
            <div class="font-semibold">{{ $record->name }}</div>
        </div>
        <div>
            <div class="text-slate-500 text-xs">البريد</div>
            <div class="font-mono text-xs ltr:tracking-tight">{{ $record->email }}</div>
        </div>
        <div>
            <div class="text-slate-500 text-xs">الجوال</div>
            <div>{{ $record->phone ?: '—' }}</div>
        </div>
        <div>
            <div class="text-slate-500 text-xs">واتساب</div>
            <div>{{ $record->whatsapp_phone ?: '—' }}</div>
        </div>
        <div>
            <div class="text-slate-500 text-xs">التسجيل</div>
            <div>{{ $record->created_at->diffForHumans() }}</div>
        </div>
    </div>
    @if($record->notes)
        <div>
            <div class="text-slate-500 text-xs mb-1">ملاحظات</div>
            <div class="rounded-md bg-slate-50 p-3 leading-relaxed">{{ $record->notes }}</div>
        </div>
    @endif
</div>