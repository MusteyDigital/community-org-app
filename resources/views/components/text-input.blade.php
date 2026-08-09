@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-sand-200 text-ink focus:border-teal-700 focus:ring-teal-700 rounded-md shadow-sm']) }}>
