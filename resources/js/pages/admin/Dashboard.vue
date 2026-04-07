<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Car as CarIcon, CheckCircle, Eye, EyeOff, Star } from 'lucide-vue-next';
import FlashMessage from '@/components/FlashMessage.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Car } from '@/types';

defineProps<{
    stats: {
        totalCars: number;
        availableCars: number;
        featuredCars: number;
        unavailableCars: number;
    };
    recentCars: Car[];
}>();

const statCards = [
    { key: 'totalCars', label: 'Total Mobil', icon: CarIcon, color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-500/10' },
    { key: 'availableCars', label: 'Tersedia', icon: CheckCircle, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-500/10' },
    { key: 'featuredCars', label: 'Unggulan', icon: Star, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-500/10' },
    { key: 'unavailableCars', label: 'Tidak Tersedia', icon: EyeOff, color: 'text-red-600 dark:text-red-400', bg: 'bg-red-500/10' },
] as const;

function formatPrice(price: string | number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(parseFloat(String(price)));
}
</script>

<template>
    <Head title="Admin Dashboard" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Dashboard</h1>
            <p class="text-sm text-muted-foreground">Overview armada VINS BALI.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card v-for="stat in statCards" :key="stat.key">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">{{ stat.label }}</CardTitle>
                    <div :class="[stat.bg, stat.color, 'flex size-9 items-center justify-center rounded-lg']">
                        <component :is="stat.icon" class="size-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold text-foreground">{{ stats[stat.key] }}</p>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Cars -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">Mobil Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-left">
                                <th class="pb-3 font-medium text-muted-foreground">Nama</th>
                                <th class="pb-3 font-medium text-muted-foreground">Merk</th>
                                <th class="pb-3 font-medium text-muted-foreground">Harga/Hari</th>
                                <th class="pb-3 font-medium text-muted-foreground">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="car in recentCars"
                                :key="car.id"
                                class="border-b border-border/50 last:border-0"
                            >
                                <td class="py-3 font-medium text-foreground">{{ car.name }}</td>
                                <td class="py-3 text-muted-foreground">{{ car.brand }}</td>
                                <td class="py-3 text-muted-foreground">{{ formatPrice(car.price_per_day) }}</td>
                                <td class="py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        :class="[
                                            car.is_available
                                                ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                                : 'bg-red-500/10 text-red-700 dark:text-red-400',
                                        ]"
                                    >
                                        <Eye v-if="car.is_available" class="size-3" />
                                        <EyeOff v-else class="size-3" />
                                        {{ car.is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="!recentCars.length" class="py-8 text-center text-sm text-muted-foreground">
                    Belum ada data mobil.
                </div>
            </CardContent>
        </Card>
    </div>
</template>
