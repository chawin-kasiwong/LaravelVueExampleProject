<script setup lang="ts">
import { computed } from 'vue';
import type { PropType } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import type { User } from '@/types';

const props = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
  user: {
    type: Object as PropType<User | null>,
    required: true,
  },
});

const emit = defineEmits(['update:open']);

const isOpen = computed({
  get: () => props.open,
  set: (value) => emit('update:open', value),
});

const joinedDate = computed(() => {
  if (!props.user)
    return '';
  return new Date(props.user.created_at).toLocaleDateString('en-TH');
});

const isVerified = computed(() => {
  if (!props.user)
    return '';
  return props.user.email_verified_at ? 'Yes' : 'No';
});
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent v-if="user" class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ user.name }}</DialogTitle>
        <DialogDescription>
          Details for user ID: {{ user.id }}
        </DialogDescription>
      </DialogHeader>

      <!-- Modal Body -->
      <div class="grid gap-4 py-4">
        <div class="grid grid-cols-4 items-center gap-4">
          <Label class="text-right text-sm text-muted-foreground">Email</Label>
          <span class="col-span-3">{{ user.email }}</span>
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label class="text-right text-sm text-muted-foreground">Joined</Label>
          <span class="col-span-3">{{ joinedDate }}</span>
        </div>
        <div class="grid grid-cols-4 items-center gap-4">
          <Label class="text-right text-sm text-muted-foreground">Verified</Label>
          <span class="col-span-3">
            {{ isVerified }}
          </span>
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" @click="isOpen = false">
          Close
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
