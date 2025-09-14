<script setup lang="ts">
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationFirst,
  PaginationItem,
  PaginationLast,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

const props = defineProps({
  users: {
    type: Object as () => {
        data: any[];
        per_page: number;
        total: number;
        current_page: number;
        from: number;
        to: number;
    },
    required: true,
  },
  perPageOptions: {
    type: Array,
    required: true,
  },
});

const isLoading = ref(false);

const removeStartListener = router.on('start', () => isLoading.value = true);
const removeFinishListener = router.on('finish', () => isLoading.value = false);

onUnmounted(() => {
  removeStartListener();
  removeFinishListener();
});

const pageSize = ref(Number(props.users.per_page || 10));

const updatePerPage = (size: number) => {
  const url = new URL(window.location.href);
  url.searchParams.set('per_page', String(size));
  url.searchParams.set('page', '1');
  router.visit(url.toString(), { preserveState: false, preserveScroll: true });
};

watch(pageSize, (newSize, oldSize) => {
  if (newSize !== oldSize) {
    updatePerPage(newSize);
  }
});

const handlePageChange = (page: number) => {
  const url = new URL(window.location.href);
  url.searchParams.set('page', String(page));
  url.searchParams.set('per_page', String(pageSize.value));
  url.searchParams.set('sort', currentSort.value);
  url.searchParams.set('direction', currentDirection.value);
  router.visit(url.toString(), { preserveState: false, preserveScroll: true });
};

const hasUsers = computed(() => props.users.data.length > 0);

const currentSort = computed(() => {
  try {
    const url = new URL(window.location.href);
    return url.searchParams.get('sort') || '';
  } catch {
    return '';
  }
});

const currentDirection = computed(() => {
  try {
    const url = new URL(window.location.href);
    return (url.searchParams.get('direction') || 'asc').toLowerCase();
  } catch {
    return 'asc';
  }
});

const handleSort = (column: string) => {
  let newDir = 'asc';
  if (currentSort.value === column) {
    newDir = currentDirection.value === 'asc' ? 'desc' : 'asc';
  }

  try {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', column);
    url.searchParams.set('direction', newDir);
    url.searchParams.set('page', '1');
    router.visit(url.toString(), { preserveState: false, preserveScroll: true });
  } catch (e) {
    console.error("Failed to update URL for sorting:", e);
  }
};
</script>

<template>
  <div>
    <div class="flex items-center gap-2 mb-4">
      <label class="text-sm text-muted-foreground">Rows per page</label>
      <Select v-model="pageSize" class="border rounded px-2 py-1 bg-white dark:bg-gray-800">
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
    <div class="overflow-x-auto border rounded-lg">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead scope="col" class="px-6 text-left text-xs uppercase tracking-wider cursor-pointer" @click="handleSort('name')">
              Name
              <span v-if="currentSort === 'name'" class="ml-1 text-xs">{{ currentDirection === 'asc' ? '▲' : '▼' }}</span>
            </TableHead>
            <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="handleSort('email')">
              Email
              <span v-if="currentSort === 'email'" class="ml-1 text-xs">{{ currentDirection === 'asc' ? '▲' : '▼' }}</span>
            </TableHead>
            <TableHead scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="hasUsers">
            <TableRow v-for="user in users.data" :key="user.id">
              <TableCell class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ user.name }}
              </TableCell>
              <TableCell class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ user.email }}
              </TableCell>
              <TableCell class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <Button variant="outline">
                  View
                </Button>
              </TableCell>
            </TableRow>
          </template>
          <TableRow v-else>
            <TableCell colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
              No users found.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-muted-foreground">
        Total {{ users.total }} users
      </div>
      <div class="text-end">
        <Pagination
          v-if="hasUsers"
          :total="users.total"
          :items-per-page="pageSize"
          :page="users.current_page"
          :sibling-count="1"
          show-edges
          @update:page="handlePageChange"
        >
          <PaginationContent v-slot="{ items }">
            <PaginationFirst />
            <PaginationPrevious />

            <template v-for="(item, index) in items">
              <PaginationEllipsis v-if="item.type === 'ellipsis'" :key="`ellipsis-${index}`" />
              <PaginationItem v-else :key="`page-${item.value}`" :value="item.value">
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