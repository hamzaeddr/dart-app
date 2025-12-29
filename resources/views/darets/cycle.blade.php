<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ $daret->name }}
                </h2>
                <p class="text-sm text-gray-500">{{ __('Cycle') }} #{{ $cycle->cycle_number }} of {{ $daret->total_members }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Progress Bar --}}
            @php
                $contributingMembers = $members->filter(fn($m) => $m->user_id !== $cycle->recipient_id);
                $paidCount = $cycle->contributions->where('status', 'confirmed')->count();
                $totalContributing = $contributingMembers->count();
                $progress = $totalContributing > 0 ? ($paidCount / $totalContributing) * 100 : 0;
            @endphp
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">{{ __('Cycle Progress') }}</span>
                        <span class="text-sm font-bold text-indigo-600">{{ $paidCount }}/{{ $totalContributing }} {{ __('confirmed') }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Cycle Details Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Due Date Card --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Due Date') }}</p>
                                <p class="text-lg font-bold text-gray-800">{{ $cycle->due_date->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $cycle->due_date->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recipient Card --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-shadow duration-300 {{ $cycle->recipient_id === auth()->id() ? 'ring-2 ring-green-400' : '' }}">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Recipient') }}</p>
                                @if ($cycle->recipient)
                                    <p class="text-lg font-bold text-gray-800">{{ $cycle->recipient->name }}</p>
                                    @if ($cycle->recipient_id === auth()->id())
                                        <p class="text-xs font-semibold text-green-600">{{ __('You receive this cycle!') }}</p>
                                    @endif
                                @else
                                    <p class="text-lg font-medium text-gray-400">{{ __('Not assigned') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Amount Card --}}
                <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100 hover:shadow-2xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Contribution') }}</p>
                                <p class="text-lg font-bold text-gray-800">{{ number_format($daret->contribution_amount, 2) }} MAD</p>
                                <p class="text-xs text-gray-500">{{ __('per member') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cycle Status Badge --}}
            @if ($cycle->is_completed)
                <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl p-4 shadow-lg">
                    <div class="flex items-center gap-3 text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-bold text-lg">{{ __('Cycle Completed!') }}</p>
                            <p class="text-green-100 text-sm">{{ __('Completed on') }} {{ $cycle->completed_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Member Contributions --}}
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800">{{ __('Member Contributions') }}</h3>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-100">
                    @php
                        $contributionsByUser = $cycle->contributions->keyBy('user_id');
                        $isRecipient = $cycle->recipient_id === auth()->id();
                        $canModerate = auth()->user()->hasRole('admin') || $daret->owner_id === auth()->id() || $isRecipient;
                    @endphp
                    
                    @foreach ($members as $member)
                        @php
                            $c = $contributionsByUser->get($member->user_id);
                            $isCycleRecipient = $cycle->recipient_id === $member->user_id;
                        @endphp
                        
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200 {{ $isCycleRecipient ? 'bg-gradient-to-r from-emerald-50 to-teal-50' : '' }}">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                {{-- Member Info --}}
                                <div class="flex items-center gap-3 lg:w-1/4">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $isCycleRecipient ? 'from-emerald-400 to-teal-500' : 'from-gray-300 to-gray-400' }} flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $member->user->name }}</p>
                                        @if ($isCycleRecipient)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ __('Recipient') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="lg:w-1/6">
                                    @if ($isCycleRecipient)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                            </svg>
                                            {{ __('Receives') }}
                                        </span>
                                    @elseif ($c)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium
                                            @if ($c->status === 'confirmed') bg-green-100 text-green-700
                                            @elseif ($c->status === 'rejected') bg-red-100 text-red-700
                                            @else bg-yellow-100 text-yellow-700 @endif">
                                            @if ($c->status === 'confirmed')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif ($c->status === 'rejected')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('Pending') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Paid At --}}
                                <div class="lg:w-1/6 text-sm text-gray-500">
                                    @if ($c && $c->paid_at)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $c->paid_at->format('M d, H:i') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Receipt --}}
                                <div class="lg:w-1/6">
                                    @if ($c && $c->getFirstMedia('receipt'))
                                        @if ($isRecipient || $canModerate || $c->user_id === auth()->id())
                                            <a href="{{ route('contributions.receipt', $c) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                {{ __('View Receipt') }}
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                {{ __('Uploaded') }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="lg:w-1/4 flex flex-wrap gap-2">
                                    @if (auth()->id() === $member->user_id && !$isCycleRecipient)
                                        <form method="POST" action="{{ route('contributions.upload', [$daret, $cycle]) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                            @csrf
                                            <label class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 cursor-pointer transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                </svg>
                                                <span>{{ __('Choose PDF') }}</span>
                                                <input type="file" name="receipt" accept="application/pdf" class="hidden" required onchange="this.form.submit()" />
                                            </label>
                                        </form>
                                    @endif

                                    @if ($c && $canModerate)
                                        @if ($c->status !== 'confirmed')
                                            <form method="POST" action="{{ route('contributions.confirm', $c) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg hover:from-green-600 hover:to-emerald-600 shadow-md hover:shadow-lg transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    {{ __('Confirm') }}
                                                </button>
                                            </form>
                                        @endif
                                        @if ($c->status !== 'rejected')
                                            <form method="POST" action="{{ route('contributions.reject', $c) }}" class="flex items-center gap-1">
                                                @csrf
                                                <input type="text" name="reason" placeholder="{{ __('Reason...') }}" class="w-24 px-2 py-1 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-200 focus:border-red-400" required />
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-red-500 to-rose-500 rounded-lg hover:from-red-600 hover:to-rose-600 shadow-md hover:shadow-lg transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    {{ __('Reject') }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Rejection Reason --}}
                            @if ($c && $c->status === 'rejected' && $c->rejection_reason)
                                <div class="mt-3 p-3 bg-red-50 rounded-lg border border-red-100">
                                    <p class="text-xs text-red-600">
                                        <span class="font-semibold">{{ __('Rejection reason:') }}</span> {{ $c->rejection_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Back Button --}}
            <div class="flex justify-start">
                <a href="{{ route('darets.show', $daret) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to Daret') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
