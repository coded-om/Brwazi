<div class="space-y-4 text-sm">
    <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-800">{{ $workshop->title }}</h3>
        <span
            class="inline-flex items-center gap-1 rounded-full bg-indigo-100 text-indigo-700 px-3 py-1 text-xs">{{ $registrations->count() }}
            مشارك</span>
    </div>
    <div class="overflow-hidden rounded-lg border border-slate-200">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-600">
                <tr class="text-right">
                    <th class="p-2">#</th>
                    <th class="p-2">الاسم</th>
                    <th class="p-2">البريد</th>
                    <th class="p-2">الجوال</th>
                    <th class="p-2">واتساب</th>
                    <th class="p-2">منذ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($registrations as $r)
                    <tr>
                        <td class="p-2 font-mono text-[11px]">{{ $r->id }}</td>
                        <td class="p-2">{{ $r->name }}</td>
                        <td class="p-2 font-mono ltr:tracking-tight">{{ $r->email }}</td>
                        <td class="p-2">{{ $r->phone ?: '—' }}</td>
                        <td class="p-2">{{ $r->whatsapp_phone ?: '—' }}</td>
                        <td class="p-2 text-slate-500">{{ $r->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-slate-500">لا يوجد مسجلون بعد</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>