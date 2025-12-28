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

            @if ($revolutQr)
                <div class="bg-white/90 overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                    <div class="p-6 sm:p-8 text-gray-900">
                        <h3 class="font-semibold text-lg mb-3">{{ __('Revolut QR Code') }}</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('Scan this QR code to send money to this member.') }}
                        </p>
                        <div class="border rounded-xl inline-block bg-white p-3">
                            <img src="{{ $revolutQr }}" alt="Revolut QR" class="h-56 w-56 object-contain" />
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
