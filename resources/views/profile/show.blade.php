<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $user->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Member profile') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white/90 overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">

                <div class="p-6 sm:p-8 text-gray-900 flex gap-4 items-start">
                    @php
                        $avatar = $profile->getFirstMediaUrl('avatar');
                    @endphp

                    <div>
                        @if ($avatar)
                            <img src="{{ $avatar }}" alt="Avatar" class="h-20 w-20 rounded-full object-cover border" />
                        @else
                            <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 space-y-2">
                        <div class="text-lg font-semibold text-gray-900">{{ $user->name }}</div>

                        @if ($profile->city)
                            <div class="text-sm text-gray-600">{{ $profile->city }}</div>
                        @endif

                        @if ($profile->phone)
                            <div class="text-sm text-gray-600">{{ $profile->phone }}</div>
                        @endif

                        @if ($profile->bio)
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $profile->bio }}</p>
                        @endif

                    </div>
                </div>
            </div>

            @php
                $revolutQr = $profile->getFirstMediaUrl('revolut_qr');
            @endphp

            @if ($revolutQr || $profile->revolut_link)
                <div class="bg-white/90 overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                    <div class="p-6 sm:p-8 text-gray-900">
                        <h3 class="font-semibold text-lg mb-3">{{ __('Revolut Payment') }}</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('Send money to this member via Revolut.') }}
                        </p>
                        
                        @if ($revolutQr)
                            <div class="border rounded-xl inline-block bg-white p-3 mb-4">
                                <img src="{{ $revolutQr }}" alt="Revolut QR" class="h-56 w-56 object-contain" />
                            </div>
                        @endif

                        @if ($profile->revolut_link)
                            @php
                                $revolutUrl = $profile->revolut_link;
                                if (!preg_match('/^https?:\/\//i', $revolutUrl)) {
                                    $revolutUrl = 'https://' . $revolutUrl;
                                }
                            @endphp
                            <div class="mt-3">
                                <a href="{{ $revolutUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-3 bg-white border border-gray-300 text-black text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors shadow-md">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    {{ __('Pay with Revolut') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
