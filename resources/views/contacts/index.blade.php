<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Contactos
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto p-x-4 sm:px-6 lg:px-8 py-12 ">
        <div class="flex justify-end mb-4 ">
            <a class="text-white px-6 py-2 bg-green-400 rounded-md font-semibold"
                href="{{ route('contacts.create') }}">Crear contacto</a>
        </div>


        @if ($contacts->count())
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 "">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50  "">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Nombre
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Correo
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr class="bg-white  hover:bg-gray-50 ">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $contact->name }}
                                </th>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $contact->user->email }}
                                </th>
                                <td class=" py-4 text-right">
                                    <div class="flex justify-center items-center  space-x-1">
                                        <a href="{{ route('contacts.edit', $contact) }}"
                                            class="bg-blue-500 hover:bg-blue-600 transition-colors duration-300 text-white px-4 py-1.5 rounded-md">Editar</a>
                                        <form action="{{ route('contacts.destroy', $contact) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-600 transition-colors duration-300 text-white px-4 py-1.5 rounded-md">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>
                Aun no tienes contactos
            </p>
        @endif
    </div>
</x-app-layout>
