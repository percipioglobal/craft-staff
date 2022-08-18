module.exports = {
    content: [
        './src/**/*.{vue,js,ts}',
    ],
    safelist: [
        'border',
        'border-b-0',
        'border-l-0',
        'border-r-0',
        'border-gray-200',
        'box-border',
        'col-span-3',
        'col-span-4',
        'gap-x-4',
        'gap-y-2',
        'grid-cols-3',
        'italic',
        'items-top',
        'line-through',
        'mb-2',
        'mb-6',
        'mt-1',
        'rounded-lg',
        'text-blue-600',
        'pt-6',
        'pt-10',
        'py-6'
    ],
    theme: {

        // Extend the default Tailwind config here
        extend: {
            display: {
                'hide': 'display:none'
            },
        },

    },
    important: true,
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/aspect-ratio'),
    ],
};