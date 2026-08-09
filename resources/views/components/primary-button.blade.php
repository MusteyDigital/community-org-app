<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gold-500 border border-transparent rounded-md font-semibold text-xs text-teal-900 uppercase tracking-widest hover:bg-gold-600 focus:bg-gold-600 active:bg-gold-700 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
