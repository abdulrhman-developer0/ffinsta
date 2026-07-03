<x-admin-layout>
    <x-slot name="title">{{ __('Add New FAQ') }}</x-slot>
    <x-slot name="header">{{ __('Add New FAQ') }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card p-6">
            <form action="{{ route('admin.faqs.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">{{ __('Question') }}</label>
                    <input type="text" name="question" value="{{ old('question') }}" class="form-input" required>
                    @error('question') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">{{ __('Answer') }}</label>
                    <textarea name="answer" class="form-input" rows="5" required>{{ old('answer') }}</textarea>
                    @error('answer') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">{{ __('Sort Order') }}</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-input" required>
                        @error('sort_order') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">{{ __('Status') }}</label>
                        <div class="mt-2 flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                {{ __('Active') }}
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}>
                                {{ __('Inactive') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.faqs.index') }}" class="btn-secondary flex-1 text-center">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn-primary flex-1">{{ __('Create FAQ') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
