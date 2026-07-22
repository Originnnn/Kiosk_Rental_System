@props(['id', 'label', 'type' => 'text', 'required' => false])

<div class="mb-4">
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    
    <input 
        type="{{ $type }}" 
        id="{{ $id }}" 
        name="{{ $id }}" 
        {{ $attributes->merge(['class' => 'w-full border rounded px-3 py-2 focus:outline-none focus:border-blue-500 ' . ($errors->has($id) ? 'border-red-500' : 'border-gray-300')]) }}
        @if($required) required @endif
    >
    
    @error($id)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>