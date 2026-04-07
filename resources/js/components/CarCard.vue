<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Calendar, Fuel, Settings2, Users, Zap, Timer, Gauge } from 'lucide-vue-next';
import { computed } from 'vue';
import type { Car } from '@/types';

const props = defineProps<{
    car: Car;
}>();

const primaryImage = computed(() => {
    const primary = props.car.images?.find((img) => img.is_primary);
    return primary ?? props.car.images?.[0] ?? null;
});

const imageUrl = computed(() => {
    if (!primaryImage.value) return null;
    return `/storage/${primaryImage.value.image_path}`;
});

import { usePage } from '@inertiajs/vue3';
import { useCurrency } from '@/composables/useCurrency';

// ... Inside setup
const page = usePage();
const { currentCurrency } = useCurrency();

const formattedPrice = computed(() => {
    let price = parseFloat(props.car.price_per_day);
    
    // Default IDR Format
    let styleConfig = { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 };
    let locale = 'id-ID';

    // USD Format logic
    if (currentCurrency.value === 'USD') {
        const rate = (page.props.exchange_rate as number) || 0.000063;
        price = price * rate;
        styleConfig = { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 };
        locale = 'en-US';
    }

    return new Intl.NumberFormat(locale, styleConfig).format(price);
});
</script>

<template>
    <Link
        :href="`/catalog/${car.slug}`"
        class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/5"
    >
        <!-- Image -->
        <div class="relative aspect-[16/10] overflow-hidden bg-muted">
            <img
                v-if="imageUrl"
                :src="imageUrl"
                :alt="car.name"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div
                v-else
                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-muted to-muted-foreground/10"
            >
                <Settings2 class="size-12 text-muted-foreground/30" />
            </div>

            <!-- Brand badge -->
            <span class="absolute top-3 left-3 rounded-md bg-black/60 px-2.5 py-1 text-xs font-medium text-white backdrop-blur-sm">
                {{ car.brand }}
            </span>

            <!-- Featured badge -->
            <span
                v-if="car.is_featured"
                class="absolute top-3 right-3 rounded-md bg-amber-500/90 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm"
            >
                Unggulan
            </span>
        </div>

        <!-- Info -->
        <div class="flex flex-1 flex-col p-4">
            <h3 class="text-base font-semibold text-foreground group-hover:text-primary transition-colors">
                {{ car.name }}
            </h3>
            <p v-if="car.short_description" class="mt-1 text-xs leading-relaxed text-muted-foreground line-clamp-2">
                {{ car.short_description }}
            </p>

            <!-- Specs row (Primary) -->
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span class="flex items-center gap-1">
                    <Users class="size-3.5" />
                    {{ car.seats }}
                </span>
                <span class="flex items-center gap-1">
                    <Settings2 class="size-3.5" />
                    {{ car.transmission }}
                </span>
                <span class="flex items-center gap-1">
                    <Fuel class="size-3.5" />
                    {{ car.fuel_type }}
                </span>
            </div>

            <!-- Specs row (Secondary: Trinity Metrics) -->
            <div class="mt-2 flex flex-wrap items-center gap-3 text-[11px] font-medium text-muted-foreground/80">
                <span v-if="car.horsepower" class="flex items-center gap-1 bg-muted/40 px-1.5 py-0.5 rounded">
                    <Zap class="size-3 text-amber-500" />
                    {{ car.horsepower }} HP
                </span>
                <span v-if="car.engine_capacity" class="flex items-center gap-1 bg-muted/40 px-1.5 py-0.5 rounded">
                    <Gauge class="size-3 text-red-500" />
                    {{ car.engine_capacity }}
                </span>
                <span v-if="car.acceleration_0_100" class="flex items-center gap-1 bg-muted/40 px-1.5 py-0.5 rounded">
                    <Timer class="size-3 text-blue-500" />
                    {{ car.acceleration_0_100 }}s (0-100)
                </span>
            </div>

            <!-- Price -->
            <div class="mt-auto pt-3 border-t border-border/60 mt-4">
                <div class="flex items-baseline justify-between">
                    <span class="text-lg font-bold text-foreground">{{ formattedPrice }}</span>
                    <span class="text-xs text-muted-foreground">/hari</span>
                </div>
            </div>
        </div>
    </Link>
</template>
