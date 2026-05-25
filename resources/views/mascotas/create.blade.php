<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Registrar Nueva Mascota
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-8 text-center">
                    <h3 class="text-2xl font-bold text-indigo-400">Datos de tu Peludito</h3>
                    <p class="text-gray-400 mt-2">Completa la ficha técnica para que podamos brindarle el mejor servicio en el Spa.</p>
                </div>

                <form method="POST" action="{{ route('mascotas.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Información Principal</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Nombre de la Mascota *</label>
                                <input type="text" name="nombre" class="w-full mt-1 bg-gray-900 border-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm" placeholder="Ej: Firulais" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Especie *</label>
                                    <select name="especie" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="" disabled selected>Selecciona...</option>
                                        <option value="Perro">Perro</option>
                                        <option value="Gato">Gato</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Raza (Opcional)</label>
                                    <input type="text" name="raza" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: Golden Retriever">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Tamaño *</label>
                                    <select name="tamano" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="" disabled selected>Selecciona...</option>
                                        <option value="Pequeño">Pequeño</option>
                                        <option value="Mediano">Mediano</option>
                                        <option value="Grande">Grande</option>
                                        <option value="Gigante">Gigante</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Fecha de Nacimiento *</label>
                                    <input type="date" name="fecha_nacimiento" class="w-full mt-1 bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Salud y Comportamiento</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Temperamento *</label>
                                <select name="temperamento" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="" disabled selected>¿Cómo se comporta normalmente?</option>
                                    <option value="Tranquilo">Tranquilo / Dócil</option>
                                    <option value="Nervioso">Nervioso / Asustadizo</option>
                                    <option value="Inquieto">Inquieto / Juguetón</option>
                                    <option value="Agresivo">Agresivo / Reactivo</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Alergias o condiciones médicas (Opcional)</label>
                                <textarea name="alergias" rows="2" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: Alérgico al shampoo de avena..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-amber-500">Carnet de Vacunas (PDF, JPG, PNG)</label>
                                <input type="file" name="carnet_vacunas" accept=".pdf, .jpg, .jpeg, .png" class="w-full mt-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md">
                            </div>
                        </div>

                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition duration-150 ease-in-out transform hover:scale-105">
                            REGISTRAR MASCOTA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>