<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center justify-between">
            <span>{{ $daret->name }}</span>
            <span class="text-xs px-2 py-1 rounded-full {{ $daret->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($daret->status) }}
            </span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Contribution') }}</div>
                            <div class="text-lg font-semibold">{{ number_format($daret->contribution_amount, 2) }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst($daret->period) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Members') }}</div>
                            <div class="text-lg font-semibold">{{ $daret->members->count() }} / {{ $daret->total_members }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Start date') }}</div>
                            <div class="text-lg font-semibold">{{ $daret->start_date->format('Y-m-d') }}</div>
                        </div>
                    </div>

                    @php
                        $totalCycles = max(1, $cycles->count());
                        $completedCycles = $cycles->where('is_completed', true)->count();
                        $progress = $totalCycles > 0 ? intval(($completedCycles / $totalCycles) * 100) : 0;
                    @endphp

                    <div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>{{ __('Overall progress') }}</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Members') }}</h3>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($daret->members as $member)
                            <a href="{{ route('profiles.show', $member->user) }}" class="border border-gray-100 rounded-lg p-3 flex gap-3 items-center hover:border-indigo-200 hover:bg-indigo-50 transition">
                                @php
                                    $profile = $member->user->profile ?? null;
                                    $avatar = $profile ? $profile->getFirstMediaUrl('avatar') : null;
                                @endphp
                                <div>
                                    @if ($avatar)
                                        <img src="{{ $avatar }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover border" />
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold">
                                            {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-sm text-gray-900 truncate">{{ $member->user->name }}</div>
                                    @if ($profile && $profile->city)
                                        <div class="text-xs text-gray-500 truncate">{{ $profile->city }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ __('Position') }} #{{ $member->position_in_cycle }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @if (($user->id === $daret->owner_id || $user->hasRole('admin')) && $daret->status === 'active' && $daret->members->count() < $daret->total_members)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-4">{{ __('Add member by email') }}</h3>

                        <form method="POST" action="{{ route('darets.add-member', $daret) }}" class="space-y-3 max-w-md">
                            @csrf

                            <div>
                                <x-input-label for="member_email" :value="__('Member email')" />
                                <x-text-input
                                    id="member_email"
                                    name="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    :value="old('email')"
                                    required
                                    autocomplete="email"
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div class="flex items-center gap-3">
                                <x-primary-button>
                                    {{ __('Add to daret') }}
                                </x-primary-button>
                                <span class="text-xs text-gray-500">
                                    {{ $daret->members->count() }} / {{ $daret->total_members }} {{ __('spots filled') }}
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">{{ __('Cycles') }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">{{ __('Due date') }}</th>
                                    <th class="py-2 pr-4">{{ __('Recipient') }}</th>
                                    <th class="py-2 pr-4">{{ __('Status') }}</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cycles as $cycle)
                                    <tr class="border-b last:border-b-0">
                                        <td class="py-2 pr-4">{{ $cycle->cycle_number }}</td>
                                        <td class="py-2 pr-4">{{ optional($cycle->due_date)->format('Y-m-d') }}</td>
                                        <td class="py-2 pr-4">{{ $cycle->recipient->name }}</td>
                                        <td class="py-2 pr-4">
                                            <span class="px-2 py-1 rounded-full text-xs {{ $cycle->is_completed ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $cycle->is_completed ? __('Completed') : __('In progress') }}
                                            </span>
                                        </td>
                                        <td class="py-2 pr-4 text-right">
                                            <a href="{{ route('darets.cycles.show', [$daret, $cycle]) }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                                                {{ __('View cycle') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($daret->status === 'active' && ! $daret->members->contains('user_id', auth()->id()) && $daret->members->count() < $daret->total_members)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-lg mb-1">{{ __('Join this daret') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Secure your spot in the rotation.') }}</p>
                        </div>
                        <form method="POST" action="{{ route('darets.join', $daret) }}">
                            @csrf
                            <x-primary-button>
                                {{ __('Join') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Chat Section --}}
            @if ($daret->members->contains('user_id', auth()->id()) || $daret->owner_id === auth()->id())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="font-semibold text-lg mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            {{ __('Group Chat') }}
                        </h3>
                        
                        <div id="chat-container" class="border border-gray-200 rounded-lg">
                            <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-3 bg-gray-50">
                                <div class="text-center text-gray-400 text-sm">{{ __('Loading messages...') }}</div>
                            </div>
                            
                            <div class="border-t border-gray-200 p-3 bg-white rounded-b-lg">
                                <form id="chat-form" class="flex gap-2">
                                    <input 
                                        type="text" 
                                        id="chat-input" 
                                        placeholder="{{ __('Type a message...') }}" 
                                        class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        maxlength="1000"
                                        autocomplete="off"
                                    />
                                    <button 
                                        type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const messagesContainer = document.getElementById('chat-messages');
                        const chatForm = document.getElementById('chat-form');
                        const chatInput = document.getElementById('chat-input');
                        const daretId = {{ $daret->id }};
                        const currentUserId = {{ auth()->id() }};
                        let lastMessageId = 0;

                        function formatTime(dateString) {
                            const date = new Date(dateString);
                            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        }

                        function renderMessages(messages) {
                            if (messages.length === 0) {
                                messagesContainer.innerHTML = '<div class="text-center text-gray-400 text-sm">{{ __("No messages yet. Start the conversation!") }}</div>';
                                return;
                            }

                            messagesContainer.innerHTML = messages.map(msg => {
                                const isOwn = msg.user_id === currentUserId;
                                return `
                                    <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
                                        <div class="max-w-xs lg:max-w-md ${isOwn ? 'bg-indigo-500 text-white' : 'bg-white border border-gray-200'} rounded-lg px-3 py-2 shadow-sm">
                                            ${!isOwn ? `<div class="text-xs font-semibold text-indigo-600 mb-1">${msg.user.name}</div>` : ''}
                                            <div class="text-sm ${isOwn ? 'text-white' : 'text-gray-800'}">${msg.body}</div>
                                            <div class="text-xs ${isOwn ? 'text-indigo-200' : 'text-gray-400'} mt-1">${formatTime(msg.created_at)}</div>
                                        </div>
                                    </div>
                                `;
                            }).join('');

                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            if (messages.length > 0) {
                                lastMessageId = messages[messages.length - 1].id;
                            }
                        }

                        function loadMessages() {
                            fetch(`/darets/${daretId}/messages`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(messages => renderMessages(messages))
                            .catch(err => {
                                messagesContainer.innerHTML = '<div class="text-center text-red-400 text-sm">{{ __("Failed to load messages") }}</div>';
                            });
                        }

                        chatForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const body = chatInput.value.trim();
                            if (!body) return;

                            chatInput.disabled = true;

                            fetch(`/darets/${daretId}/messages`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({ body: body })
                            })
                            .then(response => response.json())
                            .then(msg => {
                                chatInput.value = '';
                                loadMessages();
                            })
                            .catch(err => {
                                alert('{{ __("Failed to send message") }}');
                            })
                            .finally(() => {
                                chatInput.disabled = false;
                                chatInput.focus();
                            });
                        });

                        loadMessages();
                        setInterval(loadMessages, 5000);
                    });
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
