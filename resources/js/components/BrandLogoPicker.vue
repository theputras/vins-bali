<script setup lang="ts">
import { ref, computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Plus, X } from 'lucide-vue-next';

import { ALL_BRANDS } from '@/lib/carBrands';

const props = defineProps<{
    modelValue: { slug: string; label: string }[];
}>();

const emit = defineEmits<{
    'update:modelValue': [{ slug: string; label: string }[]];
}>();

const isOpen = ref(false);
const searchQuery = ref('');

const selectedSlugs = computed(() => new Set(props.modelValue.map((b) => b.slug)));

const filteredBrands = computed(() => {
    const q = searchQuery.value.toLowerCase();
    return ALL_BRANDS.filter((b) => {
        if (q && !b.label.toLowerCase().includes(q) && !b.slug.includes(q)) return false;
        return true;
    });
});

function toggleBrand(brand: { slug: string; label: string }) {
    const current = [...props.modelValue];
    const idx = current.findIndex((b) => b.slug === brand.slug);
    if (idx >= 0) {
        current.splice(idx, 1);
    } else {
        current.push({ slug: brand.slug, label: brand.label });
    }
    emit('update:modelValue', current);
}

function removeBrand(slug: string) {
    emit(
        'update:modelValue',
        props.modelValue.filter((b) => b.slug !== slug),
    );
}

function brandLogoUrl(slug: string) {
    return `https://cdn.jsdelivr.net/gh/filippofilip95/car-logos-dataset@master/logos/optimized/${slug}.png`;
}
</script>

<template>
    <div class="space-y-3">
        <!-- Selected brands preview -->
        <div v-if="modelValue.length" class="flex flex-wrap gap-2">
            <div
                v-for="brand in modelValue"
                :key="brand.slug"
                class="flex items-center gap-2 rounded-md border border-border bg-muted/20 px-2.5 py-1.5 text-xs font-medium"
            >
                <img
                    :src="brandLogoUrl(brand.slug)"
                    :alt="brand.label"
                    class="h-4 w-auto"
                    loading="lazy"
                />
                <span>{{ brand.label }}</span>
                <button type="button" class="text-muted-foreground hover:text-destructive transition-colors" @click="removeBrand(brand.slug)">
                    <X class="size-3" />
                </button>
            </div>
        </div>
        <p v-else class="text-sm text-muted-foreground italic">Belum ada brand dipilih.</p>

        <!-- Picker trigger -->
        <Dialog v-model:open="isOpen">
            <DialogTrigger as-child>
                <Button variant="outline" type="button" class="gap-2 h-9 text-sm">
                    <Plus class="size-4" />
                    Pilih Logo Brand
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Pilih Brand Mobil</DialogTitle>
                </DialogHeader>

                <div class="mt-4">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari brand... (Toyota, BMW, Porsche)"
                        class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 mb-4"
                    />

                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[350px] overflow-y-auto pr-1">
                        <button
                            v-for="brand in filteredBrands"
                            :key="brand.slug"
                            type="button"
                            class="flex flex-col items-center justify-center p-3 rounded-lg border transition-all gap-2"
                            :class="
                                selectedSlugs.has(brand.slug)
                                    ? 'border-primary bg-primary/10 ring-2 ring-primary/30'
                                    : 'border-transparent hover:border-border hover:bg-muted/40'
                            "
                            @click="toggleBrand(brand)"
                        >
                            <img
                                :src="brandLogoUrl(brand.slug)"
                                :alt="brand.label"
                                class="h-8 w-auto max-w-[48px]"
                                loading="lazy"
                            />
                            <span class="text-[10px] font-medium truncate w-full text-center" :class="selectedSlugs.has(brand.slug) ? 'text-primary' : 'text-muted-foreground'">
                                {{ brand.label }}
                            </span>
                        </button>
                        <div v-if="filteredBrands.length === 0" class="col-span-full py-8 text-center text-sm text-muted-foreground">
                            Brand tidak ditemukan.
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
