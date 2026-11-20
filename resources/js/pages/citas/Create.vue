<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

defineProps<{
    servicios: Array<{ id: number; nombre: string; duracion_minutos: number }>;
}>();

const form = useForm({
    servicio_id: null as number | null,
    fecha_hora: '',
    notas: '',
});

function enviar() {
    form.transform((d) => ({
        ...d,
        fecha_hora: d.fecha_hora ? d.fecha_hora.replace('T', ' ') + ':00' : '',
    })).post('/citas');
}
</script>

<template>
    <div class="mx-auto max-w-xl p-6 text-white">
        <h1 class="mb-4 text-2xl font-bold">Agendar cita</h1>

        <form class="space-y-4" @submit.prevent="enviar">
            <div>
                <label class="block">Servicio</label>
                <select v-model="form.servicio_id" class="w-full rounded border bg-neutral-900 p-2 text-white">
                    <select v-model="form.servicio_id" class="w-full rounded border bg-neutral-900 p-2 text-white [color-scheme:dark]">
                    </select>
    <option :value="null" disabled class="bg-neutral-900 text-white">Elige un servicio</option>
    <option v-for="s in servicios" :key="s.id" :value="s.id" class="bg-neutral-900 text-white">
        {{ s.nombre }} ({{ s.duracion_minutos }} min)
    </option>
</select>
                <p v-if="form.errors.servicio_id" class="text-red-500">{{ form.errors.servicio_id }}</p>
            </div>

            <div>
                <label class="block">Fecha y hora</label>
                <input v-model="form.fecha_hora" type="datetime-local" class="w-full rounded border bg-neutral-900 p-2" />
                <p v-if="form.errors.fecha_hora" class="text-red-500">{{ form.errors.fecha_hora }}</p>
            </div>

            <div>
                <label class="block">Motivo (opcional)</label>
                <textarea v-model="form.notas" class="w-full rounded border bg-neutral-900 p-2" />
            </div>

            <button type="submit" :disabled="form.processing" class="rounded bg-blue-600 px-4 py-2 text-white">
                Agendar
            </button>
        </form>
    </div>
</template>