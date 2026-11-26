<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    cita: {
        id: number;
        servicio_id: number;
        fecha_hora: string;
        notas: string | null;
    };
    servicios: Array<{
        id: number;
        nombre: string;
        duracion_minutos: number;
    }>;
}>();

const form = useForm({
    servicio_id: props.cita.servicio_id,
    fecha_hora: props.cita.fecha_hora,
    notas: props.cita.notas ?? '',
});

function actualizar() {
    form.transform((data) => ({
        ...data,
        fecha_hora: data.fecha_hora.replace('T', ' ') + ':00',
    })).put(`/citas/${props.cita.id}`);
}
</script>

<template>
    <div class="mx-auto max-w-xl p-6 text-white">
        <h1 class="mb-6 text-2xl font-bold">Editar cita</h1>

        <form class="space-y-4" @submit.prevent="actualizar">
            <div>
                <label class="mb-2 block">Servicio médico</label>

                <select
                    v-model="form.servicio_id"
                    class="w-full rounded border bg-black p-2 text-white [color-scheme:dark]"
                    required
                >
                    <option
                        v-for="servicio in servicios"
                        :key="servicio.id"
                        :value="servicio.id"
                    >
                        {{ servicio.nombre }}
                        ({{ servicio.duracion_minutos }} min)
                    </option>
                </select>

                <p class="text-sm text-red-500">
                    {{ form.errors.servicio_id }}
                </p>
            </div>

            <div>
                <label class="mb-2 block">Fecha y hora</label>

                <input
                    v-model="form.fecha_hora"
                    type="datetime-local"
                    class="w-full rounded border bg-black p-2 text-white [color-scheme:dark]"
                    required
                />

                <p class="text-sm text-red-500">
                    {{ form.errors.fecha_hora }}
                </p>
            </div>

            <div>
                <label class="mb-2 block">Notas adicionales</label>

                <textarea
                    v-model="form.notas"
                    class="w-full rounded border bg-black p-2 text-white"
                    rows="4"
                    maxlength="500"
                ></textarea>

                <p class="text-sm text-red-500">
                    {{ form.errors.notas }}
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                >
                    {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                </button>

                <Link
                    href="/citas"
                    class="rounded bg-gray-600 px-4 py-2 text-white"
                >
                    Regresar
                </Link>
            </div>
        </form>
    </div>
</template>
