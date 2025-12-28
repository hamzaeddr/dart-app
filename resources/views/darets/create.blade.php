

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Daret') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('darets.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="contribution_amount" :value="__('Contribution Amount')" />
                            <x-text-input id="contribution_amount" name="contribution_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('contribution_amount')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('contribution_amount')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="period" :value="__('Period')" />
                                <select id="period" name="period" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="weekly" @selected(old('period') === 'weekly')>{{ __('Weekly') }}</option>
                                    <option value="monthly" @selected(old('period') === 'monthly')>{{ __('Monthly') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('period')" />
                            </div>
                            <div>
                                <x-input-label for="total_members" :value="__('Total Members')" />
                                <x-text-input id="total_members" name="total_members" type="number" min="1" max="100" class="mt-1 block w-full" :value="old('total_members')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('total_members')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('darets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
