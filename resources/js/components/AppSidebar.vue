<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Car, LayoutGrid, Settings, Users, Settings2 } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/vbpanel',
        icon: LayoutGrid,
    },
    {
        title: 'Manajemen Mobil',
        href: '/vbpanel/cars',
        icon: Car,
    },
    {
        title: 'Manajemen User',
        href: '/vbpanel/users',
        icon: Users,
    },
    {
        title: 'Manajemen Layanan',
        href: '/vbpanel/services',
        icon: Settings2,
    },
    {
        title: 'Pengaturan',
        href: '/vbpanel/settings',
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Kembali ke Website',
        href: '/',
        icon: Settings,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/vbpanel">
                            <div class="flex items-center justify-center p-1">
                                <img src="/images/logo.png" alt="VINS BALI" class="h-8 w-auto rounded" />
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight ml-2">
                                <span class="truncate font-bold">Admin Panel</span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

