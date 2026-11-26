<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    cita: {
        id: number;
        fecha: string;
        estado: string;
        servicio: string;
        duracion: number;
        precio: number | string;
        notas: string | null;
    };
}>();
</script>

<template>
    <div class="mx-auto max-w-xl p-6 text-white">
        <h1 class="mb-6 text-2xl font-bold">Detalles de la cita</h1>

        <div
            class="space-y-4 rounded-lg border border-neutral-700 bg-neutral-900 p-6"
        >
            <div>
                <p class="text-sm text-neutral-400">Número de cita</p>
                <p>#{{ cita.id }}</p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Servicio médico</p>
                <p>{{ cita.servicio }}</p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Fecha y hora</p>
                <p>{{ cita.fecha }}</p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Duración</p>
                <p>{{ cita.duracion }} minutos</p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Precio</p>
                <p>${{ cita.precio }} MXN</p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Estado</p>
                <p class="font-semibold text-yellow-400">
                    {{ cita.estado }}
                </p>
            </div>

            <div>
                <p class="text-sm text-neutral-400">Motivo de consulta</p>
                <p>{{ cita.notas || 'Sin motivo registrado' }}</p>
            </div>
        </div>

        <Link
            href="/citas"
            class="mt-6 inline-block rounded bg-blue-600 px-4 py-2 text-white"
        >
            Volver a mis citas
        </Link>
    </div>
</template>
