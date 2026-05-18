<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Plus, Trash2, Tag, Watch, LayoutList } from 'lucide-vue-next';
import { ref } from 'vue';
import FlashMessage from '@/components/FlashMessage.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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

const props = defineProps<{
    services: {
        id: number;
        name: string;
        duration_days: number;
        is_active: boolean;
    }[];
}>();

const showDialog = ref(false);
const editingService = ref<{ id: number; name: string; duration_days: number; is_active: boolean } | null>(null);

const form = useForm({
    name: '',
    duration_days: 1,
    is_active: true,
});

function openCreate() {
    editingService.value = null;
    form.reset();
    form.clearErrors();
    showDialog.value = true;
}

function openEdit(service: { id: number; name: string; duration_days: number; is_active: boolean }) {
    editingService.value = { ...service };
    form.name = service.name;
    form.duration_days = service.duration_days;
    form.is_active = service.is_active;
    form.clearErrors();
    showDialog.value = true;
}

function submit() {
    if (editingService.value) {
        form.put(`/vbpanel/services/${editingService.value.id}`, {
            onSuccess: () => {
                showDialog.value = false;
            },
        });
    } else {
        form.post('/vbpanel/services', {
            onSuccess: () => {
                showDialog.value = false;
            },
        });
    }
}

function destroy(service: { id: number }) {
    router.delete(`/vbpanel/services/${service.id}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Manajemen Layanan Diskon" />
    <FlashMessage />

    <div class="flex flex-col gap-6 p-4">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">Manajemen Layanan Diskon</h1>
                <p class="text-sm text-muted-foreground">Kelola master template paket durasi sewa.</p>
            </div>
            <Button @click="openCreate" class="gap-2">
                <Plus class="size-4" />
                Tambah Paket
            </Button>
        </div>

        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/30">
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground w-16">No</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama Paket Layanan</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Durasi Hari</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(service, index) in services"
                                :key="service.id"
                                class="border-b border-border/50 transition-colors last:border-0 hover:bg-muted/20"
                            >
                                <td class="px-4 py-3 text-muted-foreground">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">
                                    <div class="flex items-center gap-2">
                                        <LayoutList class="size-4 text-muted-foreground" />
                                        {{ service.name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    <div class="flex items-center gap-1.5">
                                        <Watch class="size-4 text-primary" />
                                        {{ service.duration_days }} Hari
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="service.is_active ? 'default' : 'secondary'">
                                        {{ service.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEdit(service)">
                                            <Edit class="size-4" />
                                        </Button>

                                        <!-- Delete Dialog -->
                                        <Dialog>
                                            <DialogTrigger as-child>
                                                <Button variant="ghost" size="icon-sm" class="text-destructive hover:text-destructive">
                                                    <Trash2 class="size-4" />
                                                </Button>
                                            </DialogTrigger>
                                            <DialogContent>
                                                <DialogHeader>
                                                    <DialogTitle>Hapus Layanan</DialogTitle>
                                                    <DialogDescription>
                                                        Apakah Anda yakin ingin menghapus layanan <strong>{{ service.name }}</strong>?
                                                        Hal ini akan menghapusnya dari daftar penawaran diskon di semua mobil.
                                                    </DialogDescription>
                                                </DialogHeader>
                                                <DialogFooter>
                                                    <DialogClose as-child>
                                                        <Button variant="outline">Batal</Button>
                                                    </DialogClose>
                                                    <Button variant="destructive" @click="destroy(service)">
                                                        Hapus
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

                <div v-if="!services?.length" class="py-12 text-center">
                    <LayoutList class="size-12 mx-auto text-muted-foreground/30 mb-3" />
                    <p class="text-sm text-muted-foreground">Belum ada paket layanan durasi.</p>
                    <Button variant="outline" size="sm" class="gap-2 mt-4" @click="openCreate">
                        <Plus class="size-4" />
                        Buat Paket Pertama
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Form Dialog -->
        <Dialog v-model:open="showDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingService ? 'Edit Layanan' : 'Tambah Layanan' }}</DialogTitle>
                    <DialogDescription>
                        Konfigurasi template durasi sewa yang nantinya akan dihubungkan ke masing-masing daftar mobil.
                    </DialogDescription>
                </DialogHeader>
                
                <form @submit.prevent="submit" class="space-y-4 py-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium">Nama Paket Layanan</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            placeholder="Contoh: Sewa 3 Hari"
                            required
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-destructive">{{ form.errors.name }}</p>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium">Lama Durasi (Hari)</label>
                        <input
                            v-model.number="form.duration_days"
                            type="number"
                            min="1"
                            class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            placeholder="3"
                            required
                        />
                        <p class="text-xs text-muted-foreground">Sistem akan menggunakan angka hari ini untuk melipat gandakan harga dan memvalidasi minimum sewa saat checkout.</p>
                        <p v-if="form.errors.duration_days" class="mt-1 text-xs text-destructive">{{ form.errors.duration_days }}</p>
                    </div>

                    <label class="flex items-center gap-3 rounded-lg border border-border p-3 cursor-pointer transition hover:bg-muted/30">
                        <input
                            v-model="form.is_active"
                            type="checkbox"
                            class="mt-0.5 size-4 rounded border-input accent-primary"
                        />
                        <div class="space-y-0.5">
                            <span class="text-sm font-medium text-foreground">Status Aktif</span>
                            <p class="text-xs text-muted-foreground">Tampilkan layanan ini secara aktif di form mobil.</p>
                        </div>
                    </label>
                </form>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                    <Button type="button" @click="submit" :disabled="form.processing">
                        Simpan
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
