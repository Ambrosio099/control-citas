<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    total: number;
    proximas: Array<{ id: number; fecha: string; servicio: string; estado: string }>;
}>();
</script>

<template>
    <div class="space-y-6 p-6 text-white">
        <div class="rounded border p-4">
            <p class="text-sm opacity-70">Citas próximas</p>
            <p class="text-3xl font-bold">{{ total }}</p>
        </div>

        <div>
            <h2 class="mb-3 text-xl font-bold">Siguientes citas</h2>
            <p v-if="proximas.length === 0">No tienes citas próximas.</p>
            <ul v-else class="space-y-2">
                <li v-for="c in proximas" :key="c.id" class="rounded border p-3">
                    {{ c.servicio }} - {{ c.fecha }} ({{ c.estado }})
                </li>
            </ul>
        </div>

        <Link href="/citas/create" class="inline-block rounded bg-blue-600 px-4 py-2 text-white">
            Agendar cita
        </Link>
    </div>
</template>