<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Users') }}</div>
                    <div class="text-2xl font-semibold">{{ $users->total() }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Darets') }}</div>
                    <div class="text-2xl font-semibold">{{ $darets->total() }}</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Pending contributions') }}</div>
                    <div class="text-2xl font-semibold">{{ $pendingContributions->count() }}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">{{ __('Recent Darets') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">{{ __('Name') }}</th>
                                <th class="py-2 pr-4">{{ __('Owner') }}</th>
                                <th class="py-2 pr-4">{{ __('Members') }}</th>
                                <th class="py-2 pr-4">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($darets as $daret)
                                <tr class="border-b last:border-b-0">
                                    <td class="py-2 pr-4">{{ $daret->name }}</td>
                                    <td class="py-2 pr-4">{{ $daret->owner->name }}</td>
                                    <td class="py-2 pr-4">{{ $daret->members_count }} / {{ $daret->total_members }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $daret->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($daret->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $darets->links() }}
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-3">{{ __('Pending Contributions') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">{{ __('Daret') }}</th>
                                <th class="py-2 pr-4">{{ __('Member') }}</th>
                                <th class="py-2 pr-4">{{ __('Cycle') }}</th>
                                <th class="py-2 pr-4">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingContributions as $c)
                                <tr class="border-b last:border-b-0">
                                    <td class="py-2 pr-4">{{ $c->daret->name }}</td>
                                    <td class="py-2 pr-4">{{ $c->user->name }}</td>
                                    <td class="py-2 pr-4">#{{ $c->cycle->cycle_number }}</td>
                                    <td class="py-2 pr-4">{{ number_format($c->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-500">{{ __('No pending contributions.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
