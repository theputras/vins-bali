<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle, XCircle, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash as { success?: string; error?: string });
const visible = ref(false);
const currentMessage = ref('');
const currentType = ref<'success' | 'error'>('success');

watch(
    () => [flash.value?.success, flash.value?.error],
    ([success, error]) => {
        if (success) {
            currentMessage.value = success;
            currentType.value = 'success';
            visible.value = true;
            autoHide();
        } else if (error) {
            currentMessage.value = error;
            currentType.value = 'error';
            visible.value = true;
            autoHide();
        }
    },
    { immediate: true },
);

let timeout: ReturnType<typeof setTimeout>;
function autoHide() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        visible.value = false;
    }, 5000);
}
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="visible"
                class="fixed right-4 bottom-6 z-[100] max-w-sm rounded-lg border px-4 py-3 shadow-lg"
                :class="[
                    currentType === 'success'
                        ? 'border-emerald-500/30 bg-emerald-50 text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-950/80 dark:text-emerald-200'
                        : 'border-red-500/30 bg-red-50 text-red-800 dark:border-red-500/20 dark:bg-red-950/80 dark:text-red-200',
                ]"
            >
                <div class="flex items-start gap-3">
                    <CheckCircle v-if="currentType === 'success'" class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                    <XCircle v-else class="mt-0.5 size-5 shrink-0 text-red-600 dark:text-red-400" />
                    <p class="text-sm font-medium">{{ currentMessage }}</p>
                    <button
                        class="ml-auto -mr-1 shrink-0 rounded p-0.5 opacity-60 transition-opacity hover:opacity-100"
                        @click="visible = false"
                    >
                        <X class="size-4" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
