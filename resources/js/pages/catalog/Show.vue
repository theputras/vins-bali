<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, CheckCircle, FileText, Fuel, MessageCircle, Palette, Settings2, Users, Zap, Gauge, Timer } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import CarCard from '@/components/CarCard.vue';
import CarGallery from '@/components/CarGallery.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useScrollReveal } from '@/composables/useScrollReveal';
import type { Car } from '@/types';

useScrollReveal();

const props = defineProps<{
    car: Car;
    relatedCars: Car[];
}>();

import { useCurrency } from '@/composables/useCurrency';

// ...
const page = usePage();
const { currentCurrency } = useCurrency();

function formatAmount(multiplier: number, discountPercent: number = 0) {
    let basePrice = parseFloat(props.car.price_per_day);
    let price = (basePrice * multiplier) * (1 - (discountPercent / 100));
    
    let styleConfig: Intl.NumberFormatOptions = { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 };
    let locale = 'id-ID';

    if (currentCurrency.value === 'USD') {
        const rate = (page.props.exchange_rate as number) || 0.000063;
        price = price * rate;
        styleConfig = { style: 'currency', currency: 'USD', minimumFractionDigits: 0, maximumFractionDigits: 2 };
        locale = 'en-US';
    }

    return new Intl.NumberFormat(locale, styleConfig).format(price);
}

const pricePackages = computed(() => {
    const base = [
        { label: 'Per Hari', amount: formatAmount(1), period: '/hari' }
    ];

    const carServices = (props.car as any).services;
    if (!carServices || carServices.length === 0) {
        return base;
    }

    const dynamicPacks = carServices.map((svc: any) => {
        let periodText = `/${svc.duration_days} hari`;
        if (svc.duration_days === 7) periodText = '/minggu';
        else if (svc.duration_days === 30) periodText = '/bulan';
        
        return {
            label: svc.name,
            amount: formatAmount(svc.duration_days, svc.pivot.discount_percentage),
            period: periodText,
            highlight: svc.pivot.discount_percentage > 0 ? `Diskon ${svc.pivot.discount_percentage}%` : undefined
        };
    });

    return [...base, ...dynamicPacks];
});

const proceedToWhatsApp = () => {
    const waNumber = page.props.global_settings?.whatsapp_number || '';
    const text = encodeURIComponent(`Hello VINS Bali, I wanna rent ${props.car.name}`);
    window.open(`https://wa.me/${waNumber}?text=${text}`, '_blank');
    showTermsModal.value = false;
};

const specs = computed(() => {
    const arr: any[] = [
        { icon: Settings2, label: 'Transmisi', value: props.car.transmission },
        { icon: Calendar, label: 'Tahun', value: props.car.year },
        { icon: Users, label: 'Kursi', value: `${props.car.seats} penumpang` },
        { icon: Fuel, label: 'Bahan Bakar', value: props.car.fuel_type },
        { icon: Palette, label: 'Warna', value: props.car.color },
    ];
    if (props.car.horsepower) arr.push({ icon: Zap, label: 'Tenaga', value: `${props.car.horsepower} HP` });
    if (props.car.engine_capacity) arr.push({ icon: Gauge, label: 'Mesin', value: props.car.engine_capacity });
    if (props.car.acceleration_0_100) arr.push({ icon: Timer, label: '0-100 km/h', value: `${props.car.acceleration_0_100}s` });
    return arr;
});

// S&K Modal state
const showTermsModal = ref(false);
const agreedToTerms = ref(false);

const termsAndConditions = computed<string[]>(() => {
    const data = page.props.global_settings?.terms_and_conditions;
    return Array.isArray(data) ? data : [];
});

const usps = computed(() => {
    const data = page.props.global_settings?.home_usps;
    return Array.isArray(data) ? data : [];
});

const activeTab = ref<'specifications' | 'terms'>('specifications');

function openTermsModal() {
    agreedToTerms.value = false;
    showTermsModal.value = true;
}
</script>

<template>
    <Head :title="car.name" />
    <FlashMessage />

    <div class="pt-24 pb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Back link -->
            <Link href="/catalog" class="mb-6 inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground">
                <ArrowLeft class="size-4" />
                Kembali ke Katalog
            </Link>

            <div class="grid gap-8 lg:grid-cols-5 pb-24 lg:pb-0">
                <!-- Left Column (Gallery + Tabs) -->
                <div class="lg:col-span-3 space-y-8 min-w-0" data-reveal>
                    <!-- Mobile Title (Visible only on mobile) -->
                    <div class="lg:hidden">
                        <span class="inline-block rounded-md bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary uppercase tracking-wide">
                            {{ car.brand }}
                        </span>
                        <h1 class="mt-3 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                            {{ car.name }}
                        </h1>
                    </div>

                    <!-- Gallery -->
                    <CarGallery
                        :images="car.images ?? []"
                        :car-name="car.name"
                    />

                    <!-- Mobile Info Section (Price & USP, visible only on mobile) -->
                    <div class="space-y-6 lg:hidden">
                        <!-- Price Tiers -->
                        <div class="space-y-3">
                            <div
                                v-for="pkg in pricePackages"
                                :key="pkg.label"
                                class="flex items-center justify-between rounded-lg border border-border bg-muted/10 p-3.5 transition-colors hover:bg-muted/30"
                            >
                                <div>
                                    <p class="text-sm font-semibold text-foreground">{{ pkg.label }}</p>
                                    <p v-if="pkg.highlight" class="text-xs text-amber-500 font-medium mt-0.5">{{ pkg.highlight }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-foreground">{{ pkg.amount }}</span>
                                    <span class="text-xs text-muted-foreground ml-1">{{ pkg.period }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- USP Card -->
                        <div class="rounded-xl border border-[#9f0306]/30 bg-card p-5">
                            <ul class="space-y-4">
                                <li v-for="(usp, index) in usps" :key="index" class="flex items-center gap-3">
                                    <CheckCircle class="size-5 shrink-0 text-[#9f0306]" />
                                    <span class="text-sm font-medium text-foreground">{{ usp }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="flex items-center overflow-x-auto rounded border border-border bg-card text-center hide-scrollbar" data-reveal>
                        <button 
                            @click="activeTab = 'specifications'"
                            class="flex-1 whitespace-nowrap px-4 py-3 text-sm font-semibold transition-colors uppercase tracking-wider"
                            :class="activeTab === 'specifications' ? 'bg-[#9f0306] text-white' : 'text-muted-foreground hover:bg-muted/40'"
                        >
                            Specifications
                        </button>
                        <button 
                            @click="activeTab = 'terms'"
                            class="flex-1 whitespace-nowrap border-l border-border px-4 py-3 text-sm font-semibold transition-colors uppercase tracking-wider"
                            :class="activeTab === 'terms' ? 'bg-[#9f0306] text-white' : 'text-muted-foreground hover:bg-muted/40'"
                        >
                            Terms of Rental
                        </button>
                    </div>

                    <!-- Tab Content -->
                    <div class="rounded-xl border border-border bg-card p-6 sm:p-8 min-h-[300px]" data-reveal data-reveal-delay="200">
                        <!-- Specifications Tab -->
                        <div v-show="activeTab === 'specifications'" class="animate-fade-in">
                            <h3 class="mb-6 text-xl font-bold tracking-tight text-foreground">Spesifikasi Kendaraan</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div
                                    v-for="spec in specs"
                                    :key="spec.label"
                                    class="flex flex-col gap-1.5 rounded-lg border border-border bg-muted/20 p-4"
                                >
                                    <component :is="spec.icon" class="size-5 text-primary" />
                                    <p class="mt-1 text-[10px] uppercase tracking-wider text-muted-foreground font-semibold">{{ spec.label }}</p>
                                    <p class="text-sm font-bold text-foreground">{{ spec.value }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rental Conditions Tab -->


                        <!-- Terms of Rental Tab -->
                        <div v-show="activeTab === 'terms'" class="animate-fade-in">
                            <div class="flex items-center gap-3 mb-6">
                                <FileText class="size-6 text-[#9f0306]" />
                                <h3 class="text-xl font-bold tracking-tight text-foreground">Syarat & Ketentuan Detail</h3>
                            </div>
                            
                            <div class="grid gap-4 sm:grid-cols-2" v-if="termsAndConditions.length > 0">
                                <ul class="space-y-3 sm:col-span-2">
                                    <li
                                        v-for="(term, index) in termsAndConditions"
                                        :key="index"
                                        class="flex items-start gap-3 text-sm text-foreground/90 bg-muted/20 p-3 rounded-lg border border-border/50"
                                    >
                                        <CheckCircle class="mt-0.5 size-4 shrink-0 text-[#9f0306]" />
                                        <span>{{ term }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="text-sm text-muted-foreground">Syarat dan ketentuan belum diatur.</div>
                        </div>
                    </div>
                    
                    <!-- Description Moved Below Tab Content -->
                    <div v-if="car.description" class="rounded-xl border border-border bg-card p-6 sm:p-8" data-reveal>
                        <h3 class="mb-4 text-xl font-bold tracking-tight text-foreground">Unit Overview</h3>
                        <p class="text-sm leading-relaxed text-muted-foreground whitespace-pre-wrap">{{ car.description }}</p>
                    </div>
                </div>

                <!-- Right Column (Info & Booking) — strictly for desktop -->
                <div class="hidden lg:col-span-2 lg:block min-w-0" data-reveal="right" data-reveal-delay="200">
                    <div class="lg:sticky lg:top-24 space-y-6">
                        <div>
                            <!-- Brand badge -->
                            <span class="inline-block rounded-md bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary uppercase tracking-wide">
                                {{ car.brand }}
                            </span>

                            <h1 class="mt-3 text-3xl font-bold tracking-tight text-foreground sm:text-4xl">
                                {{ car.name }}
                            </h1>
                        </div>

                        <!-- Price Tiers -->
                        <div class="space-y-3">
                            <div
                                v-for="pkg in pricePackages"
                                :key="pkg.label"
                                class="flex items-center justify-between rounded-lg border border-border bg-muted/10 p-3.5 transition-colors hover:bg-muted/30"
                            >
                                <div>
                                    <p class="text-sm font-semibold text-foreground">{{ pkg.label }}</p>
                                    <p v-if="pkg.highlight" class="text-xs text-amber-500 font-medium mt-0.5">{{ pkg.highlight }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-foreground">{{ pkg.amount }}</span>
                                    <span class="text-xs text-muted-foreground ml-1">{{ pkg.period }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button (desktop: part of sticky panel, mobile: fixed bottom) -->
                        <div class="hidden lg:block">
                            <Button
                                size="lg"
                                class="w-full gap-2 bg-red-700 text-white hover:bg-red-800 h-14 text-lg"
                                @click="openTermsModal"
                            >
                                <MessageCircle class="size-5" />
                                Sewa Sekarang
                            </Button>
                            <p class="mt-3 text-center text-[11px] text-muted-foreground">
                                Tim VIP kami akan merespon via WhatsApp secara instan
                            </p>
                        </div>

                        <!-- USP Card -->
                        <div class="rounded-xl border border-[#9f0306]/30 bg-card p-5">
                            <ul class="space-y-4">
                                <li v-for="(usp, index) in usps" :key="index" class="flex items-center gap-3">
                                    <CheckCircle class="size-5 shrink-0 text-[#9f0306]" />
                                    <span class="text-sm font-medium text-foreground">{{ usp }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Mobile-only fixed bottom CTA -->
                <div class="fixed bottom-0 left-0 right-0 z-50 bg-background/95 backdrop-blur-md p-4 border-t border-border shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.5)] lg:hidden">
                    <Button
                        size="lg"
                        class="w-full gap-2 bg-red-700 text-white hover:bg-red-800"
                        @click="openTermsModal"
                    >
                        <MessageCircle class="size-5" />
                        Sewa Sekarang
                    </Button>
                </div>
            </div>

            <!-- Related Cars -->
            <div v-if="relatedCars.length" class="mt-16" data-reveal>
                <h2 class="text-xl font-bold tracking-tight text-foreground">Mobil Lainnya</h2>
                <p class="mt-1 text-sm text-muted-foreground">Mungkin Anda juga tertarik dengan pilihan ini.</p>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <CarCard v-for="relCar in relatedCars" :key="relCar.id" :car="relCar" />
                </div>
            </div>
        </div>
    </div>

    <!-- S&K Modal (popup saat klik "Sewa Sekarang") -->
    <Dialog v-model:open="showTermsModal">
        <DialogContent class="max-w-lg max-h-[85vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <FileText class="size-5 text-primary" />
                    Syarat & Ketentuan Sewa
                </DialogTitle>
                <DialogDescription>
                    Silakan baca dan setujui syarat & ketentuan berikut sebelum melanjutkan ke WhatsApp.
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4 py-2">
                <ul v-if="termsAndConditions.length > 0" class="space-y-2 rounded-lg border border-border/50 bg-muted/20 p-4">
                    <li
                        v-for="(term, index) in termsAndConditions"
                        :key="index"
                        class="flex items-start gap-2 text-xs text-muted-foreground leading-relaxed"
                    >
                        <CheckCircle class="mt-0.5 size-3 shrink-0 text-amber-500" />
                        {{ term }}
                    </li>
                </ul>
                <div v-if="termsAndConditions.length === 0" class="text-sm text-muted-foreground">Syarat dan ketentuan belum diatur.</div>
            </div>

            <!-- Agreement checkbox -->
            <label class="flex items-start gap-3 rounded-lg border border-border p-3 cursor-pointer transition hover:bg-muted/30">
                <input
                    v-model="agreedToTerms"
                    type="checkbox"
                    class="mt-0.5 size-4 rounded border-input accent-primary"
                />
                <span class="text-sm text-foreground">
                    Saya telah membaca dan <strong>menyetujui</strong> seluruh Syarat & Ketentuan sewa kendaraan VINS BALI.
                </span>
            </label>

            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="outline">Batal Menyewa</Button>
                </DialogClose>
                <Button
                    :disabled="!agreedToTerms"
                    class="gap-2 bg-red-700 text-white hover:bg-red-800 disabled:opacity-50"
                    @click="proceedToWhatsApp"
                >
                    <MessageCircle class="size-4" />
                    Sewa via WhatsApp
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
