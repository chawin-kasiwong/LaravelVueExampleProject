<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { PropType } from 'vue';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue
} from '@/components/ui/select';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious
} from '@/components/ui/pagination';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table';

import type { PaginatedResponse, User, Filters } from '@/types';

const props = defineProps({
  users: {
    type: Object as PropType<PaginatedResponse<User>>,
    required: true,
  },
  filters: {
    type: Object as PropType<Filters>,
    required: true,
  },
  perPageOptions: {
    type: Array as PropType<number[]>,
    required: true,
  },
});

const isLoading = ref(false);
const perPage = ref(Number(props.users.per_page));

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'actions', label: 'Actions', sortable: false },
];

const removeStartListener = router.on('start', () => isLoading.value = true);
const removeFinishListener = router.on('finish', () => isLoading.value = false);

onUnmounted(() => {
  removeStartListener();
  removeFinishListener();
});


const updateQuery = (params: Record<string, string | number | null>) => {
  router.get(window.location.pathname, {
    ...props.filters,
    per_page: perPage.value,
    ...params,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const handleSort = (column: string) => {
  const newDirection = props.filters.sort === column && props.filters.direction === 'asc' ? 'desc' : 'asc';
  updateQuery({
    sort: column,
    direction: newDirection,
    page: 1,
  });
};

const handlePageChange = (page: number) => {
  updateQuery({ page });
};

watch(perPage, (newPageSize) => {
  updateQuery({
    per_page: newPageSize,
    page: 1,
  });
});
</script>

<template>
  <div>
    <!-- Per Page Selector -->
    <div class="flex items-center gap-2 mb-4">
      <label class="text-sm text-muted-foreground">Rows per page</label>
      <Select v-model:model-value="perPage">
        <SelectTrigger class="w-24">
          <SelectValue placeholder="Select" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto border rounded-lg">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead
              v-for="column in columns"
              :key="column.key"
              :class="{ 'cursor-pointer hover:bg-muted/50': column.sortable }"
              @click="column.sortable ? handleSort(column.key) : null"
            >
              {{ column.label }}
              <span v-if="column.sortable && filters.sort === column.key" class="ml-1 text-xs">
                {{ filters.direction === 'asc' ? '▲' : '▼' }}
              </span>
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="users.data.length > 0">
            <TableRow v-for="user in users.data" :key="user.id">
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>
                <Button variant="outline">View</Button>
              </TableCell>
            </TableRow>
          </template>
          <TableRow v-else>
            <TableCell :colspan="columns.length" class="text-center">
              No users found.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-muted-foreground">
        Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
      </div>
      <div class="items-end">
        <Pagination
          v-slot="{ page }"
          :items-per-page="perPage"
          :total="users.total"
          :default-page="users.current_page"
          @update:page="handlePageChange"
        >
          <PaginationFirst />
          <PaginationPrevious />
          <PaginationContent v-slot="{ items }">
            <template v-for="(item, index) in items" :key="index">
              <PaginationItem
                v-if="item.type === 'page'"
                :value="item.value"
                :is-active="item.value === page"
              >
                {{ item.value }}
              </PaginationItem>
            </template>
            <PaginationNext />
            <PaginationLast />
          </PaginationContent>
        </Pagination>
      </div>
    </div>
  </div>
</template>