<script setup lang="ts">
import { ref, computed } from 'vue';
import * as LucideIcons from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [string];
}>();

const isOpen = ref(false);
const searchQuery = ref('');

// Filter out non-component exports from lucide-vue-next
const allIcons = Object.keys(LucideIcons).filter(
    (key) => key !== 'createLucideIcon' && key !== 'default'
);

const filteredIcons = computed(() => {
    if (!searchQuery.value) return allIcons.slice(0, 50); // limit to avoid lag
    return allIcons
        .filter((name) => name.toLowerCase().includes(searchQuery.value.toLowerCase()))
        .slice(0, 50);
});

function selectIcon(name: string) {
    emit('update:modelValue', name);
    isOpen.value = false;
}

// Ensure the icon component actually exists before rendering
function getIconComponent(name: string) {
    // @ts-ignore
    return LucideIcons[name] || LucideIcons['HelpCircle'];
}
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogTrigger as-child>
            <Button variant="outline" type="button" class="gap-2 flex w-full justify-start text-left px-3 font-normal h-9">
                <component :is="getIconComponent(modelValue)" class="size-4 shrink-0 text-muted-foreground" />
                <span class="truncate">{{ modelValue || 'Pilih Ikon...' }}</span>
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Pilih Ikon (Lucide)</DialogTitle>
            </DialogHeader>

            <div class="mt-4">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari ikon... (contoh: MapPin, Star, User)"
                    class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20 mb-4"
                />

                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2 max-h-[300px] overflow-y-auto pr-1">
                    <button
                        v-for="iconName in filteredIcons"
                        :key="iconName"
                        type="button"
                        class="flex flex-col items-center justify-center p-2 hover:bg-muted/50 rounded-md border border-transparent hover:border-border transition-colors gap-1.5"
                        @click="selectIcon(iconName)"
                    >
                        <component :is="getIconComponent(iconName)" class="size-6 text-foreground" />
                        <span class="text-[9px] text-muted-foreground truncate w-full text-center">{{ iconName }}</span>
                    </button>
                    <div v-if="filteredIcons.length === 0" class="col-span-full py-8 text-center text-sm text-muted-foreground">
                        Ikon tidak ditemukan.
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
