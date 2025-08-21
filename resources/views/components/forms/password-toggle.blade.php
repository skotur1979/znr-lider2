<div x-data="{ show: false }" class="relative">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lozinka</label>

    <input
        :type="show ? 'text' : 'password'"
        x-model="window.$wire.entangle('password')"
        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white pr-10"
    />

    <button
        type="button"
        @click="show = !show"
        class="absolute inset-y-0 right-0 px-3 flex items-center text-sm text-gray-500"
    >
        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5
                     c4.478 0 8.268 2.943 9.542 7
                     -1.274 4.057-5.064 7-9.542 7
                     C7.523 19 3.732 16.057 2.458 12z"/>
        </svg>

        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19
                     c-4.478 0-8.268-2.943-9.542-7
                     a9.973 9.973 0 013.164-4.407
                     M6.489 6.489A9.973 9.973 0 0112 5
                     c4.477 0 8.267 2.943 9.541 7
                     a9.97 9.97 0 01-4.184 5.222M15 12a3 3 0 11-6 0
                     3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 3l18 18"/>
        </svg>
    </button>
</div>
