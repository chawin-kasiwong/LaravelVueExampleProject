<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import UserTable from '@/components/Users/UserTable.vue';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import type { PropType } from 'vue';

export interface User {
  id: number;
  name: string;
  email: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  per_page: number;
  total: number;
  current_page: number;
  from: number;
  to: number;
  links: { url: string | null; label: string; active: boolean }[];
}

export interface Filters {
  search: string | null;
  sort: string;
  direction: 'asc' | 'desc';
  per_page: string;
}

const props = defineProps({
  users: {
    type: Object as PropType<PaginatedResponse<User>>,
    required: true,
  },
  perPageOptions: {
    type: Array as PropType<number[]>,
    required: true,
  },
  filters: {
    type: Object as PropType<Filters>,
    required: true,
  },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Management',
        href: '/users',
    },
];
</script>

<template>

    <Head title="Users Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>List of Users</CardTitle>
                </CardHeader>
                <CardContent>
                     <UserTable
                        :users="users"
                        :filters="filters"
                        :per-page-options="perPageOptions"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
