<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

```
    <!-- Nom complet -->
    <div>
        <x-input-label for="name" :value="__('Nom complet')" />

        <x-text-input
            id="name"
            class="block mt-1 w-full"
            type="text"
            name="name"
            :value="old('name')"
            required
            autofocus
            autocomplete="name"
            placeholder="Exemple : Jean Kouassi"
        />

        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Adresse email -->
    <div class="mt-4">
        <x-input-label for="email" :value="__('Adresse e-mail')" />

        <x-text-input
            id="email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            :value="old('email')"
            required
            autocomplete="username"
            placeholder="exemple@email.com"
        />

        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Département -->
    <div class="mt-4">
        <x-input-label for="department_id" :value="__('Département')" />

        <select
            id="department_id"
            name="department_id"
            required
            class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        >
            <option value="">-- Sélectionnez votre département --</option>

            @foreach ($departments as $department)
                <option
                    value="{{ $department->id }}"
                    {{ old('department_id') == $department->id ? 'selected' : '' }}
                >
                    {{ $department->name }}
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
    </div>

    <!-- Mot de passe -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('Mot de passe')" />

        <x-text-input
            id="password"
            class="block mt-1 w-full"
            type="password"
            name="password"
            required
            autocomplete="new-password"
        />

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirmation du mot de passe -->
    <div class="mt-4">
        <x-input-label
            for="password_confirmation"
            :value="__('Confirmer le mot de passe')"
        />

        <x-text-input
            id="password_confirmation"
            class="block mt-1 w-full"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
        />

        <x-input-error
            :messages="$errors->get('password_confirmation')"
            class="mt-2"
        />
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end mt-4">
        <a
            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            href="{{ route('login') }}"
        >
            {{ __('Déjà inscrit ?') }}
        </a>

        <x-primary-button class="ms-4">
            {{ __('Créer mon compte') }}
        </x-primary-button>
    </div>
</form>
```

</x-guest-layout>
