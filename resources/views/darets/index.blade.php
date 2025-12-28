<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Darets') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Overview of the darets you own or participate in.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex justify-end">
                @role('admin')
                <a href="{{ route('darets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold tracking-wide bg-indigo-600 text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Create Daret') }}
                </a>
                @else
                <button type="button" onclick="alert('{{ __('Only administrators can create new darets. Please contact an admin.') }}')" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold tracking-wide bg-gray-400 text-white shadow cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Create Daret') }}
                </button>
                @endrole
            </div>

            @if ($darets->isEmpty())
                <div class="bg-white/90 overflow-hidden shadow-sm sm:rounded-2xl border border-dashed border-slate-200">
                    <div class="p-8 text-center text-gray-600 text-sm">
                        <p>{{ __('You are not part of any darets yet.') }}</p>
                        @role('admin')
                        <p class="mt-3">
                            <a href="{{ route('darets.create') }}" class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold tracking-wide bg-indigo-600 text-white shadow hover:bg-indigo-500">
                                {{ __('Create your first daret') }}
                            </a>
                        </p>
                        @else
                        <p class="mt-3 text-gray-500">
                            {{ __('Ask an administrator to add you to a daret.') }}
                        </p>
                        @endrole
                    </div>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($darets as $daret)
                        <a href="{{ route('darets.show', $daret) }}" class="block bg-white/90 rounded-2xl shadow-sm hover:shadow-lg transition border border-slate-100 overflow-hidden">
                            <div class="px-4 pt-4 pb-3 flex items-center justify-between">
                                <h3 class="font-semibold text-base text-slate-900 truncate">{{ $daret->name }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $daret->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($daret->status) }}
                                </span>
                            </div>
                            <div class="px-4 pb-4 space-y-1 text-sm text-slate-600">
                                <p>
                                    {{ __('Contribution') }}:
                                    <span class="font-semibold">{{ number_format($daret->contribution_amount, 2) }}</span>
                                    <span class="text-xs text-slate-500">({{ $daret->period }})</span>
                                </p>
                                <p>
                                    {{ __('Members') }}:
                                    <span class="font-semibold">{{ $daret->members_count }} / {{ $daret->total_members }}</span>
                                </p>
                                @php
                                    $totalCycles = max(1, $daret->cycles_count);
                                    $completedCycles = $daret->cycles()->where('is_completed', true)->count();
                                    $progress = $totalCycles > 0 ? intval(($completedCycles / $totalCycles) * 100) : 0;
                                @endphp
                                <div class="pt-2">
                                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                                        <span>{{ __('Progress') }}</span>
                                        <span>{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $darets->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
