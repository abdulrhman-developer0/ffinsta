<x-admin-layout>
    <x-slot name="title">{{ __('Manage FAQs') }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">{{ __('Manage FAQs') }}</h2>
            <a href="{{ route('admin.faqs.create') }}" class="btn-primary">
                {{ __('Add New FAQ') }}
            </a>
        </div>
    </x-slot>

    <div class="card p-0">
        @if($faqs->isEmpty())
            <div class="p-8 text-center text-muted">
                <p>{{ __('No FAQs found. Create one to get started.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-muted uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 font-semibold">{{ __('Order') }}</th>
                        <th class="px-6 py-4 font-semibold">{{ __('Question') }}</th>
                        <th class="px-6 py-4 font-semibold">{{ __('Status') }}</th>
                        <th class="px-6 py-4 font-semibold text-right">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($faqs as $faq)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">{{ $faq->sort_order }}</td>
                            <td class="px-6 py-4 truncate max-w-xs" title="{{ $faq->question }}">{{ $faq->question }}</td>
                            <td class="px-6 py-4">
                                @if($faq->is_active)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-md">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 rounded-md">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn-secondary text-xs px-3 py-1.5">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this FAQ?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger text-xs px-3 py-1.5">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-admin-layout>
