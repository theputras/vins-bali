<script setup lang="ts">
import { ChevronLeft, ChevronRight, Expand } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { CarImage } from '@/types';

const props = defineProps<{
    images: CarImage[];
    carName: string;
}>();

const selectedIndex = ref(0);
const slideDirection = ref<'left' | 'right'>('left');

const sortedImages = computed(() =>
    [...props.images].sort((a, b) => {
        // Primary first, then by sort_order
        if (a.is_primary && !b.is_primary) return -1;
        if (!a.is_primary && b.is_primary) return 1;
        return a.sort_order - b.sort_order;
    }),
);

const currentImage = computed(() => sortedImages.value[selectedIndex.value] ?? null);

const imageUrl = computed(() => {
    if (!currentImage.value) return null;
    return `/storage/${currentImage.value.image_path}`;
});

function prev() {
    slideDirection.value = 'right';
    selectedIndex.value = selectedIndex.value > 0 ? selectedIndex.value - 1 : sortedImages.value.length - 1;
}

function next() {
    slideDirection.value = 'left';
    selectedIndex.value = selectedIndex.value < sortedImages.value.length - 1 ? selectedIndex.value + 1 : 0;
}

// Lightbox
const lightboxOpen = ref(false);

// Drag to scroll for thumbnails (mouse)
const thumbnailsContainer = ref<HTMLElement | null>(null);
let isDown = false;
let startX = 0;
let scrollLeft = 0;

function onMouseDown(e: MouseEvent) {
    isDown = true;
    if (!thumbnailsContainer.value) return;
    startX = e.pageX - thumbnailsContainer.value.offsetLeft;
    scrollLeft = thumbnailsContainer.value.scrollLeft;
}

function onMouseLeave() {
    isDown = false;
}

function onMouseUp() {
    isDown = false;
}

function onMouseMove(e: MouseEvent) {
    if (!isDown || !thumbnailsContainer.value) return;
    e.preventDefault();
    const x = e.pageX - thumbnailsContainer.value.offsetLeft;
    const walk = (x - startX) * 2;
    thumbnailsContainer.value.scrollLeft = scrollLeft - walk;
}

// Touch drag-to-scroll for thumbnails (mobile)
let touchStartX = 0;
let touchScrollLeft = 0;

function onTouchStart(e: TouchEvent) {
    if (!thumbnailsContainer.value) return;
    touchStartX = e.touches[0].pageX - thumbnailsContainer.value.offsetLeft;
    touchScrollLeft = thumbnailsContainer.value.scrollLeft;
}

function onTouchMove(e: TouchEvent) {
    if (!thumbnailsContainer.value) return;
    const x = e.touches[0].pageX - thumbnailsContainer.value.offsetLeft;
    const walk = (x - touchStartX) * 1.5;
    thumbnailsContainer.value.scrollLeft = touchScrollLeft - walk;
}

// Touch swipe on main image for prev/next (mobile)
let swipeStartX = 0;
let swipeStartY = 0;
const SWIPE_THRESHOLD = 40;

function onImageTouchStart(e: TouchEvent) {
    swipeStartX = e.touches[0].clientX;
    swipeStartY = e.touches[0].clientY;
}

function onImageTouchEnd(e: TouchEvent) {
    const dx = e.changedTouches[0].clientX - swipeStartX;
    const dy = e.changedTouches[0].clientY - swipeStartY;
    // Only trigger if horizontal swipe dominates
    if (Math.abs(dx) > SWIPE_THRESHOLD && Math.abs(dx) > Math.abs(dy)) {
        if (dx < 0) next();
        else prev();
    }
}
</script>

<template>
    <div class="space-y-3 min-w-0">
        <!-- Main Image -->
        <div
            class="group relative aspect-[16/10] overflow-hidden rounded-xl border border-border bg-muted"
            @touchstart.passive="onImageTouchStart"
            @touchend.passive="onImageTouchEnd"
        >
            <Transition :name="`gallery-slide-${slideDirection}`" mode="out-in">
                <img
                    v-if="imageUrl"
                    :key="selectedIndex"
                    :src="imageUrl"
                    :alt="`${carName} - Image ${selectedIndex + 1}`"
                    class="h-full w-full object-cover select-none"
                    draggable="false"
                />
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-muted to-muted-foreground/10"
                >
                    <span class="text-muted-foreground/40 text-sm">No image available</span>
                </div>
            </Transition>

            <!-- Nav Arrows: always visible on mobile, hover-only on desktop -->
            <template v-if="sortedImages.length > 1">
                <button
                    class="absolute top-1/2 left-3 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white backdrop-blur-sm transition-all hover:bg-black/70 sm:opacity-0 sm:group-hover:opacity-100"
                    @click="prev"
                >
                    <ChevronLeft class="size-5" />
                </button>
                <button
                    class="absolute top-1/2 right-3 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white backdrop-blur-sm transition-all hover:bg-black/70 sm:opacity-0 sm:group-hover:opacity-100"
                    @click="next"
                >
                    <ChevronRight class="size-5" />
                </button>
            </template>

            <!-- Expand: always visible on mobile, hover-only on desktop -->
            <button
                v-if="imageUrl"
                class="absolute right-3 bottom-3 rounded-full bg-black/50 p-2 text-white backdrop-blur-sm transition-all hover:bg-black/70 sm:opacity-0 sm:group-hover:opacity-100"
                @click="lightboxOpen = true"
            >
                <Expand class="size-4" />
            </button>

            <!-- Counter -->
            <span v-if="sortedImages.length > 1" class="absolute bottom-3 left-3 rounded-md bg-black/50 px-2.5 py-1 text-xs text-white backdrop-blur-sm">
                {{ selectedIndex + 1 }} / {{ sortedImages.length }}
            </span>
        </div>

        <!-- Thumbnails -->
        <div 
            v-if="sortedImages.length > 1" 
            ref="thumbnailsContainer"
            class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar touch-pan-x cursor-grab active:cursor-grabbing w-full min-w-0"
            @mousedown="onMouseDown"
            @mouseleave="onMouseLeave"
            @mouseup="onMouseUp"
            @mousemove="onMouseMove"
            @touchstart.passive="onTouchStart"
            @touchmove.passive="onTouchMove"
        >
            <button
                v-for="(img, index) in sortedImages"
                :key="img.id"
                class="h-14 w-16 sm:h-16 sm:w-20 shrink-0 overflow-hidden rounded-lg border-2 transition-all"
                :class="[
                    index === selectedIndex
                        ? 'border-primary ring-2 ring-primary/20'
                        : 'border-border opacity-60 hover:opacity-100',
                ]"
                @click="selectedIndex = index"
            >
                <img
                    :src="`/storage/${img.image_path}`"
                    :alt="`${carName} thumbnail ${index + 1}`"
                    class="h-full w-full object-cover"
                />
            </button>
        </div>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="lightboxOpen"
                class="fixed inset-0 z-[200] flex items-center justify-center bg-black/90 p-4"
                @click.self="lightboxOpen = false"
            >
                <button
                    class="absolute top-4 right-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                    @click="lightboxOpen = false"
                >
                    <span class="text-2xl leading-none">&times;</span>
                </button>

                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    :alt="carName"
                    class="max-h-[90vh] max-w-[90vw] rounded-lg object-contain"
                />

                <template v-if="sortedImages.length > 1">
                    <button
                        class="absolute top-1/2 left-4 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20"
                        @click="prev"
                    >
                        <ChevronLeft class="size-6" />
                    </button>
                    <button
                        class="absolute top-1/2 right-4 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition hover:bg-white/20"
                        @click="next"
                    >
                        <ChevronRight class="size-6" />
                    </button>
                </template>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Slide Left (next image) */
.gallery-slide-left-enter-active,
.gallery-slide-left-leave-active {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.gallery-slide-left-enter-from {
    opacity: 0;
    transform: translateX(60px);
}
.gallery-slide-left-leave-to {
    opacity: 0;
    transform: translateX(-60px);
}

/* Slide Right (prev image) */
.gallery-slide-right-enter-active,
.gallery-slide-right-leave-active {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.gallery-slide-right-enter-from {
    opacity: 0;
    transform: translateX(-60px);
}
.gallery-slide-right-leave-to {
    opacity: 0;
    transform: translateX(60px);
}
</style>
