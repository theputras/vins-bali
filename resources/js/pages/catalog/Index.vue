<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Search, SlidersHorizontal, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import CarCard from '@/components/CarCard.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import type { Car } from '@/types';

interface PaginatedCars {
    data: Car[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    cars: PaginatedCars;
    brands: string[];
    filters: {
        search?: string;
        brand?: string;
        transmission?: string;
        seats?: string;
        sort?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const brand = ref(props.filters.brand ?? '');
const transmission = ref(props.filters.transmission ?? '');
const seats = ref(props.filters.seats ?? '');
const sort = ref(props.filters.sort ?? 'default');

const showFilters = ref(false);

const hasActiveFilters = computed(() => {
    return brand.value || transmission.value || seats.value || sort.value !== 'default';
});

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (brand.value) params.brand = brand.value;
    if (transmission.value) params.transmission = transmission.value;
    if (seats.value) params.seats = seats.value;
    if (sort.value && sort.value !== 'default') params.sort = sort.value;

    router.get('/catalog', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function clearFilters() {
    search.value = '';
    brand.value = '';
    transmission.value = '';
    seats.value = '';
    sort.value = 'default';
    router.get('/catalog', {}, { preserveState: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([brand, transmission, seats, sort], () => {
    applyFilters();
});
</script>

<template>
    <Head title="Katalog Mobil" />
    <FlashMessage />

    <div class="pt-24 pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Katalog Mobil</h1>
                <p class="mt-2 text-muted-foreground">
                    Temukan mobil impian Anda dari koleksi premium kami. {{ cars.total }} unit tersedia.
                </p>
            </div>

            <!-- Search & Filters -->
            <div class="mb-8 space-y-4">
                <!-- Search bar -->
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari mobil berdasarkan nama atau merk..."
                            class="h-10 w-full rounded-lg border border-input bg-background pl-10 pr-4 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        />
                        <button
                            v-if="search"
                            class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="search = ''"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                    <Button
                        variant="outline"
                        size="default"
                        class="gap-2 shrink-0"
                        @click="showFilters = !showFilters"
                    >
                        <SlidersHorizontal class="size-4" />
                        <span class="hidden sm:inline">Filter</span>
                        <span
                            v-if="hasActiveFilters"
                            class="flex size-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-primary-foreground"
                        >
                            !
                        </span>
                    </Button>
                </div>

                <!-- Filter panel -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-if="showFilters" class="rounded-xl border border-border bg-card p-4">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Brand -->
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted-foreground">Merk</label>
                                <select
                                    v-model="brand"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="">Semua Merk</option>
                                    <option v-for="b in brands" :key="b" :value="b">{{ b }}</option>
                                </select>
                            </div>

                            <!-- Transmission -->
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted-foreground">Transmisi</label>
                                <select
                                    v-model="transmission"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="">Semua</option>
                                    <option value="Automatic">Automatic</option>
                                    <option value="Manual">Manual</option>
                                </select>
                            </div>

                            <!-- Seats -->
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted-foreground">Min. Kursi</label>
                                <select
                                    v-model="seats"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="">Semua</option>
                                    <option value="2">2+</option>
                                    <option value="4">4+</option>
                                    <option value="5">5+</option>
                                    <option value="7">7+</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-muted-foreground">Urutan</label>
                                <select
                                    v-model="sort"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                >
                                    <option value="default">Default</option>
                                    <option value="price_asc">Harga: Rendah ke Tinggi</option>
                                    <option value="price_desc">Harga: Tinggi ke Rendah</option>
                                    <option value="newest">Terbaru</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="hasActiveFilters" class="mt-3 flex justify-end">
                            <Button variant="ghost" size="sm" class="gap-1.5 text-xs" @click="clearFilters">
                                <X class="size-3.5" />
                                Reset Filter
                            </Button>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Car Grid -->
            <div v-if="cars.data.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <CarCard v-for="car in cars.data" :key="car.id" :car="car" />
            </div>

            <!-- Empty state -->
            <div v-else class="rounded-xl border border-dashed border-border bg-card p-16 text-center">
                <Search class="mx-auto size-10 text-muted-foreground/30" />
                <h3 class="mt-4 text-base font-semibold text-foreground">Tidak ada mobil ditemukan</h3>
                <p class="mt-1 text-sm text-muted-foreground">Coba ubah kata kunci atau filter pencarian Anda.</p>
                <Button variant="outline" size="sm" class="mt-4" @click="clearFilters">
                    Reset Filter
                </Button>
            </div>

            <!-- Pagination -->
            <div v-if="cars.last_page > 1" class="mt-10 flex items-center justify-center gap-1">
                <template v-for="link in cars.links" :key="link.label">
                    <button
                        v-if="link.url"
                        class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors"
                        :class="[
                            link.active
                                ? 'border-primary bg-primary text-primary-foreground'
                                : 'border-border bg-background text-foreground hover:bg-accent',
                        ]"
                        @click="router.get(link.url!, {}, { preserveState: true, preserveScroll: true })"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="inline-flex h-9 min-w-9 items-center justify-center px-3 text-sm text-muted-foreground"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
