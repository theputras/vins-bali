<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ArrowLeft, Car } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import FlashMessage from '@/components/FlashMessage.vue';
</script>

<template>
    <Head title="Terima Kasih" />
    <FlashMessage />

    <div class="min-h-screen flex items-center justify-center pt-24 pb-16 px-4">
        <div class="w-full max-w-lg text-center">
            <!-- Success Icon with Animation -->
            <div class="mx-auto mb-8 flex size-24 items-center justify-center rounded-full bg-emerald-500/10 ring-4 ring-emerald-500/20 animate-bounce-slow">
                <CheckCircle class="size-12 text-emerald-500" />
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                Terima Kasih! 🎉
            </h1>

            <!-- Description -->
            <p class="mt-4 text-base leading-relaxed text-muted-foreground max-w-md mx-auto">
                Data Anda telah berhasil kami terima. Tim kami akan segera menghubungi Anda untuk proses selanjutnya.
            </p>

            <!-- Info Card -->
            <div class="mt-8 rounded-xl border border-border bg-card p-6 text-left space-y-3">
                <h3 class="font-semibold text-foreground text-sm">Langkah Selanjutnya:</h3>
                <ul class="space-y-2.5 text-sm text-muted-foreground">
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">1</span>
                        <span>Tim kami akan menghubungi Anda via <strong class="text-foreground">WhatsApp / Telepon</strong> untuk konfirmasi.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">2</span>
                        <span>Siapkan <strong class="text-foreground">dokumen persyaratan</strong> yang diperlukan.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">3</span>
                        <span>Proses <strong class="text-foreground">pembayaran DP</strong> akan diinformasikan oleh tim kami.</span>
                    </li>
                </ul>
            </div>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <Link href="/catalog">
                    <Button variant="outline" class="w-full sm:w-auto gap-2">
                        <Car class="size-4" />
                        Lihat Mobil Lainnya
                    </Button>
                </Link>
                <Link href="/">
                    <Button class="w-full sm:w-auto gap-2 bg-red-700 text-white hover:bg-red-800">
                        <ArrowLeft class="size-4" />
                        Kembali ke Beranda
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes bounce-slow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}
.animate-bounce-slow {
    animation: bounce-slow 2s ease-in-out infinite;
}
</style>
