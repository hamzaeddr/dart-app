<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $profile->phone ?? '')" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $profile->city ?? '')" autocomplete="address-level2" />
                <x-input-error class="mt-2" :messages="$errors->get('city')" />
            </div>
        </div>

        <div>
            <x-input-label for="bio" :value="__('Short Bio')" />
            <textarea id="bio" name="bio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3">{{ old('bio', $profile->bio ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="revolut_link" :value="__('Revolut Link')" />
            <x-text-input id="revolut_link" name="revolut_link" type="url" class="mt-1 block w-full" :value="old('revolut_link', $profile->revolut_link ?? '')" placeholder="https://revolut.me/yourname" />
            <x-input-error class="mt-2" :messages="$errors->get('revolut_link')" />
            <p class="mt-1 text-xs text-gray-500">{{ __('Your Revolut.me payment link (e.g., https://revolut.me/yourname)') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="avatar" :value="__('Avatar')" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

                @php
                    $avatarMedia = isset($profile) ? $profile->getFirstMediaUrl('avatar') : null;
                @endphp

                @if ($avatarMedia)
                    <div class="mt-3">
                        <span class="text-sm text-gray-600 block mb-1">{{ __('Current avatar') }}</span>
                        <img src="{{ $avatarMedia }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover border" />
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="revolut_qr" :value="__('Revolut QR Image')" />
                <input id="revolut_qr" name="revolut_qr" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer" />
                <x-input-error class="mt-2" :messages="$errors->get('revolut_qr')" />

                @php
                    $revolutMedia = isset($profile) ? $profile->getFirstMediaUrl('revolut_qr') : null;
                @endphp

                @if ($revolutMedia)
                    <div class="mt-3">
                        <span class="text-sm text-gray-600 block mb-1">{{ __('Current Revolut QR') }}</span>
                        <img src="{{ $revolutMedia }}" alt="Revolut QR" class="h-24 w-24 object-contain border bg-white" />
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
