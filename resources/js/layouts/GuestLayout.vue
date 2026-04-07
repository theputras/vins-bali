<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { useCurrency } from '@/composables/useCurrency';

const page = usePage();
const auth = computed(() => page.props.auth as { user: { role: string } | null });

const { currentCurrency } = useCurrency();

const mobileMenuOpen = ref(false);
const scrolled = ref(false);

function handleScroll() {
    scrolled.value = window.scrollY > 20;
}

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();

    if (document.cookie.includes('googtrans=/id/en') || document.cookie.includes('googtrans=/auto/en')) {
        currentLang.value = 'EN';
    }
});

const currentLang = ref('ID');

function changeLanguage() {
    const newLang = currentLang.value === 'ID' ? 'id' : 'en';
    const comboLang = newLang; // for direct translation setup
    
    // GTranslate hook if available
    const gtranslateSelect = document.querySelector('.gtranslate_wrapper select');
    if (gtranslateSelect && typeof Event === 'function') {
        (gtranslateSelect as HTMLSelectElement).value = newLang;
        gtranslateSelect.dispatchEvent(new Event('change', { bubbles: true }));
    } else {
        // Fallback or custom logic
        document.cookie = `googtrans=/id/${newLang}; path=/;`;
        window.location.reload();
    }
}

const navLinks = [
    { name: 'Home', href: '/' },
    { name: 'Katalog', href: '/catalog' },
];
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header
            class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
            :class="[
                scrolled
                    ? 'glass border-b border-border/50 shadow-sm'
                    : 'bg-transparent',
            ]"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2.5">
                    <img src="/images/logo.png" alt="VINS BALI" class="h-9 w-auto rounded-md" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.name"
                        :href="link.href"
                        class="rounded-md px-3.5 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                    >
                        {{ link.name }}
                    </Link>
                </nav>

                <!-- Desktop Actions -->
                <div class="hidden items-center gap-3 md:flex">
                    <!-- Currency Dropdown -->
                    <select
                        v-model="currentCurrency"
                        class="h-9 cursor-pointer rounded-md border border-input bg-background px-3 py-1 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring dark:bg-input/30 dark:hover:bg-input/50"
                    >
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>

                    <!-- Language Dropdown -->
                    <select
                        v-model="currentLang"
                        @change="changeLanguage"
                        class="h-9 cursor-pointer rounded-md border border-input bg-background px-3 py-1 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none focus:ring-1 focus:ring-ring dark:bg-input/30 dark:hover:bg-input/50"
                    >
                        <option value="ID">ID</option>
                        <option value="EN">EN</option>
                    </select>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <!-- Mobile Dropdowns -->
                    <select
                        v-model="currentCurrency"
                        class="h-9 rounded-md border border-input bg-background px-2.5 py-1 text-xs font-semibold focus:outline-none"
                    >
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>

                    <select
                        v-model="currentLang"
                        @change="changeLanguage"
                        class="h-9 rounded-md border border-input bg-background px-2.5 py-1 text-xs font-semibold focus:outline-none"
                    >
                        <option value="ID">ID</option>
                        <option value="EN">EN</option>
                    </select>

                    <Button variant="ghost" size="icon" @click="mobileMenuOpen = !mobileMenuOpen">
                        <X v-if="mobileMenuOpen" class="size-5" />
                        <Menu v-else class="size-5" />
                    </Button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="glass border-b border-border/50 md:hidden">
                    <div class="space-y-1 px-4 py-3">
                        <Link
                            v-for="link in navLinks"
                            :key="link.name"
                            :href="link.href"
                            class="block rounded-md px-3 py-2.5 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                            @click="mobileMenuOpen = false"
                        >
                            {{ link.name }}
                        </Link>
                            <!-- Login section explicitly removed from mobile menu per request -->
                    </div>
                </div>
            </Transition>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-border bg-muted/30">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Brand -->
                    <div>
                        <div class="flex items-center gap-2.5">
                            <img src="/images/logo.png" alt="VINS BALI" class="h-8 w-auto rounded-md" />
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-muted-foreground">
                            Premium car rental service in Bali. Experience the island with luxury and style.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-foreground">Quick Links</h3>
                        <ul class="space-y-2">
                            <li v-for="link in navLinks" :key="link.name">
                                <Link :href="link.href" class="text-sm text-muted-foreground transition-colors hover:text-foreground">
                                    {{ link.name }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-foreground">Contact</h3>
                        <ul class="space-y-2 text-sm text-muted-foreground">
                            <li>{{ $page.props.global_settings?.company_address || 'Bali, Indonesia' }}</li>
                            <li>
                                <a :href="`mailto:${$page.props.global_settings?.company_email || 'info@vinsbali.com'}`" class="transition-colors hover:text-foreground">
                                    {{ $page.props.global_settings?.company_email || 'info@vinsbali.com' }}
                                </a>
                            </li>
                            <li>
                                <a :href="`https://wa.me/${$page.props.global_settings?.whatsapp_number || '6281234567890'}`" target="_blank" class="transition-colors hover:text-foreground">
                                    WhatsApp: +{{ $page.props.global_settings?.whatsapp_number || '6281234567890' }}
                                </a>
                            </li>
                            <li v-if="$page.props.global_settings?.instagram_url" class="mt-2 block">
                                <a :href="$page.props.global_settings.instagram_url" target="_blank" class="transition-colors hover:text-foreground underline underline-offset-4">
                                    Instagram
                                </a>
                            </li>
                            <li v-if="$page.props.global_settings?.facebook_url" class="block">
                                <a :href="$page.props.global_settings.facebook_url" target="_blank" class="transition-colors hover:text-foreground underline underline-offset-4">
                                    Facebook
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 border-t border-border pt-6 text-center text-xs text-muted-foreground">
                    &copy; {{ new Date().getFullYear() }} VINS BALI. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
