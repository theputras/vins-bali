<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Search, Shield, ShieldOff, Trash2, User, Users, X } from 'lucide-vue-next';
import { ref } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface UserData {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'user';
    email_verified_at: string | null;
    two_factor_confirmed_at: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedUsers {
    data: UserData[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    users: PaginatedUsers;
    filters: {
        search?: string;
        role?: string;
    };
    stats: {
        totalUsers: number;
        adminUsers: number;
        regularUsers: number;
    };
}>();

const search = ref(props.filters.search ?? '');
const roleFilter = ref(props.filters.role ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (roleFilter.value) params.role = roleFilter.value;

    router.get('/admin/users', params, {
        preserveState: true,
        preserveScroll: true,
    });
}

function clearSearch() {
    search.value = '';
    applyFilters();
}

function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
}

function toggleRole(user: UserData) {
    router.patch(`/admin/users/${user.id}/toggle-role`, {}, {
        preserveScroll: true,
    });
}

function deleteUser(user: UserData) {
    router.delete(`/admin/users/${user.id}`, {
        preserveScroll: true,
    });
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateTime(date: string) {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const statCards = [
    { key: 'totalUsers' as const, label: 'Total User', icon: Users, color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-500/10' },
    { key: 'adminUsers' as const, label: 'Admin', icon: Shield, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-500/10' },
    { key: 'regularUsers' as const, label: 'User Biasa', icon: User, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-500/10' },
];
</script>

<template>
    <Head title="Manajemen User" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Manajemen User</h1>
                <p class="text-sm text-muted-foreground">Kelola akun pengguna dan admin</p>
            </div>
            <Link href="/admin/users/create">
                <Button class="gap-2">
                    <Plus class="size-4" />
                    Tambah User
                </Button>
            </Link>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 sm:grid-cols-3">
            <Card v-for="stat in statCards" :key="stat.key">
                <CardContent class="flex items-center justify-between pt-6">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">{{ stat.label }}</p>
                        <p class="text-2xl font-bold text-foreground">{{ stats[stat.key] }}</p>
                    </div>
                    <div :class="[stat.bg, stat.color, 'flex size-10 items-center justify-center rounded-lg']">
                        <component :is="stat.icon" class="size-5" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Search & Filter -->
        <Card>
            <CardContent class="pt-6">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau email..."
                            class="h-9 w-full rounded-md border border-input bg-background pl-10 pr-8 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            @input="onSearchInput"
                        />
                        <button
                            v-if="search"
                            class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            @click="clearSearch"
                        >
                            <X class="size-4" />
                        </button>
                    </div>
                    <select
                        v-model="roleFilter"
                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 sm:w-40"
                        @change="applyFilters"
                    >
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </CardContent>
        </Card>

        <!-- Table -->
        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">User</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Role</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Verifikasi</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">2FA</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Bergabung</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="border-b border-border/50 transition-colors last:border-0 hover:bg-muted/20"
                            >
                                <!-- User info -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-9 items-center justify-center rounded-full bg-primary/10 font-semibold text-primary text-sm">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-foreground">{{ user.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="user.role === 'admin' ? 'default' : 'secondary'"
                                        class="gap-1 text-[10px]"
                                    >
                                        <Shield v-if="user.role === 'admin'" class="size-3" />
                                        <User v-else class="size-3" />
                                        {{ user.role === 'admin' ? 'Admin' : 'User' }}
                                    </Badge>
                                </td>

                                <!-- Email verified -->
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="user.email_verified_at ? 'default' : 'destructive'"
                                        class="text-[10px]"
                                    >
                                        {{ user.email_verified_at ? 'Terverifikasi' : 'Belum' }}
                                    </Badge>
                                </td>

                                <!-- 2FA -->
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="user.two_factor_confirmed_at ? 'default' : 'secondary'"
                                        class="text-[10px]"
                                    >
                                        {{ user.two_factor_confirmed_at ? 'Aktif' : 'Nonaktif' }}
                                    </Badge>
                                </td>

                                <!-- Joined -->
                                <td class="px-4 py-3 text-xs text-muted-foreground">
                                    {{ formatDate(user.created_at) }}
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/admin/users/${user.id}/edit`">
                                            <Button variant="ghost" size="icon-sm" title="Edit">
                                                <Edit class="size-4" />
                                            </Button>
                                        </Link>

                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            :title="user.role === 'admin' ? 'Jadikan User' : 'Jadikan Admin'"
                                            @click="toggleRole(user)"
                                        >
                                            <ShieldOff v-if="user.role === 'admin'" class="size-4" />
                                            <Shield v-else class="size-4" />
                                        </Button>

                                        <!-- Delete Dialog -->
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon-sm" class="text-destructive hover:text-destructive" title="Hapus">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <DialogHeader>
                                                    <DialogTitle>Hapus User</DialogTitle>
                                                    <DialogDescription>
                                                        Apakah Anda yakin ingin menghapus <strong>{{ user.name }}</strong> ({{ user.email }})?
                                                        Akun ini akan dihapus secara permanen.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <DialogFooter>
                                                    <DialogClose as-child>
                                                        <Button variant="outline">Batal</Button>
                                                    </DialogClose>
                                                    <Button variant="destructive" @click="deleteUser(user)">
                                                        Hapus User
                                                    </Button>
                                                </DialogFooter>
                                            </DialogContent>
                                        </Dialog>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div v-if="!users.data.length" class="py-12 text-center">
                    <Users class="mx-auto size-10 text-muted-foreground/30" />
                    <p class="mt-3 text-sm text-muted-foreground">Tidak ada user ditemukan.</p>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="flex items-center justify-center gap-1">
            <template v-for="link in users.links" :key="link.label">
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
</template>
