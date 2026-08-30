<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mon pointage
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Messages --}}
            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Employé --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h3 class="text-2xl font-bold text-gray-900">
                        Bonjour {{ $employee->first_name }}
                    </h3>

                    <p class="mt-1 text-gray-600">
                        {{ $employee->first_name }}
                        {{ $employee->last_name }}
                    </p>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Entrée --}}
                        <div class="border rounded-xl p-5">

                            <h4 class="text-lg font-semibold text-gray-900">
                                Entrée
                            </h4>

                            @if ($entry)
                                <p class="mt-3 text-green-600 font-semibold">
                                    ✓ Entrée enregistrée
                                </p>

                                <p class="text-gray-600">
                                    {{ $entry->occurred_at->format('H:i:s') }}
                                </p>
                            @else
                                <form
                                    method="POST"
                                    action="{{ route('my_attendance.entry') }}"
                                    class="mt-4"
                                >
                                    @csrf

                                    @if ($accessPoints->count() > 0)
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Point d'accès
                                        </label>

                                        <select
                                            name="access_point_id"
                                            class="w-full rounded-lg border-gray-300 mb-4"
                                        >
                                            @foreach ($accessPoints as $accessPoint)
                                                <option value="{{ $accessPoint->id }}">
                                                    {{ $accessPoint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    <button
                                        type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg"
                                    >
                                        Pointer mon entrée
                                    </button>
                                </form>
                            @endif

                        </div>

                        {{-- Sortie --}}
                        <div class="border rounded-xl p-5">

                            <h4 class="text-lg font-semibold text-gray-900">
                                Sortie
                            </h4>

                            @if ($exit)
                                <p class="mt-3 text-blue-600 font-semibold">
                                    ✓ Sortie enregistrée
                                </p>

                                <p class="text-gray-600">
                                    {{ $exit->occurred_at->format('H:i:s') }}
                                </p>
                            @elseif ($entry)
                                <form
                                    method="POST"
                                    action="{{ route('my_attendance.exit') }}"
                                    class="mt-4"
                                >
                                    @csrf

                                    @if ($accessPoints->count() > 0)
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Point d'accès
                                        </label>

                                        <select
                                            name="access_point_id"
                                            class="w-full rounded-lg border-gray-300 mb-4"
                                        >
                                            @foreach ($accessPoints as $accessPoint)
                                                <option value="{{ $accessPoint->id }}">
                                                    {{ $accessPoint->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    <button
                                        type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg"
                                    >
                                        Pointer ma sortie
                                    </button>
                                </form>
                            @else
                                <p class="mt-3 text-gray-500">
                                    Vous devez d'abord pointer votre entrée.
                                </p>
                            @endif

                        </div>

                    </div>

                    {{-- Historique du jour --}}
                    <div class="mt-8">

                        <h4 class="text-xl font-semibold text-gray-900 mb-4">
                            Mes mouvements aujourd'hui
                        </h4>

                        @if ($todayMovements->count())
                            <div class="overflow-x-auto">

                                <table class="min-w-full divide-y divide-gray-200">

                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left">
                                                Type
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Heure
                                            </th>

                                            <th class="px-4 py-3 text-left">
                                                Méthode
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-200">

                                        @foreach ($todayMovements as $movement)
                                            <tr>

                                                <td class="px-4 py-3">
                                                    @if ($movement->type === 'entry')
                                                        <span class="text-green-600 font-semibold">
                                                            Entrée
                                                        </span>
                                                    @else
                                                        <span class="text-red-600 font-semibold">
                                                            Sortie
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3">
                                                    {{ $movement->occurred_at->format('H:i:s') }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    {{ $movement->method }}
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>

                            </div>
                        @else
                            <p class="text-gray-500">
                                Aucun mouvement enregistré aujourd'hui.
                            </p>
                        @endif

                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>