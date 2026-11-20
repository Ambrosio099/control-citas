<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

defineProps<{
    citas: Array<{ id: number; fecha: string; estado: string; servicio: string; notas: string | null }>;
}>();

function cancelar(id: number) {
    if (confirm('¿Cancelar esta cita?')) {
        router.delete(`/citas/${id}`);
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl p-6 text-white">
        <h1 class="mb-4 text-2xl font-bold">Mis citas</h1>
        <Link href="/citas/create" class="mb-4 inline-block rounded bg-blue-600 px-4 py-2 text-white">
            Agendar cita
        </Link>

        <p v-if="citas.length === 0">No tienes citas.</p>

        <ul v-else class="space-y-3">
            <li v-for="c in citas" :key="c.id" class="rounded border p-4">
                <p class="font-semibold">{{ c.servicio }} - {{ c.fecha }}</p>
                <p>Estado: {{ c.estado }}</p>
                <p v-if="c.notas">{{ c.notas }}</p>
                <button class="mt-2 text-red-500" @click="cancelar(c.id)">Cancelar</button>
            </li>
        </ul>
    </div>
</template>