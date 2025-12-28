<x-app-layout>
	<x-slot name="header">
			<div class="flex items-center justify-between">
				<div>
					<h2 class="font-semibold text-xl text-gray-800 leading-tight">
						{{ __('Dashboard') }}
					</h2>
					<p class="mt-1 text-sm text-gray-500">
						{{ __('Overview of your darets and activity.') }}
					</p>
				</div>
			</div>
	</x-slot>

	<div class="py-10">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="grid gap-6 md:grid-cols-3">
				<div class="md:col-span-2 bg-white/90 shadow-sm sm:rounded-2xl border border-slate-100">
					<div class="p-6 sm:p-8">
						<h3 class="text-lg font-semibold text-slate-900">
							{{ __('Get started with your darets') }}
						</h3>
						<p class="mt-2 text-sm text-slate-600">
							{{ __('Create a new rotating savings circle or manage the darets you are already part of.') }}
						</p>

						<div class="mt-5 flex flex-wrap gap-3">
							<a href="{{ route('darets.index') }}" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-indigo-600 text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
								{{ __('View my darets') }}
							</a>
							<a href="{{ route('darets.create') }}" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white text-indigo-700 border border-indigo-100 hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
								{{ __('Create a new daret') }}
							</a>
						</div>
					</div>
				</div>

				<div class="bg-gradient-to-br from-indigo-500 to-violet-500 text-white shadow-sm sm:rounded-2xl p-6 flex flex-col justify-between">
					<div>
						<h3 class="text-lg font-semibold">
							{{ __('Complete your profile') }}
						</h3>
						<p class="mt-2 text-sm text-indigo-100">
							{{ __('Add your phone, city, and Revolut QR so other members can easily send you money when it is your turn.') }}
						</p>
					</div>
					<div class="mt-4">
						<a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/10 border border-indigo-200/60 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/70">
							{{ __('Go to profile settings') }}
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
