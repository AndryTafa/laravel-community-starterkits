<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import StarterkitCard from '@/components/StarterkitCard.vue';

const props = defineProps<{
  starterkits: Array<{
    id: string;
    url: string;
    created_at: string;
    user: {
      id: number;
      name: string;
    };
    tags: Array<{
      id: number;
      name: string;
    }>;
  }>;
  bookmarks: Array<{
    id: string;
    url: string;
    created_at: string;
    user: {
      id: number;
      name: string;
    };
    tags: Array<{
      id: number;
      name: string;
    }>;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
];
</script>

<template>

  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <Link :href="route('starterkit.create')">
        <Button>Add Starter Kit</Button>
        </Link>
      </div>

      <Tabs default-value="bookmarks" class="w-full">
        <TabsList class="grid w-full grid-cols-2">
          <TabsTrigger value="bookmarks">Bookmarks</TabsTrigger>
          <TabsTrigger value="my-kits">My Starter Kits</TabsTrigger>
        </TabsList>

        <TabsContent value="bookmarks">
          <div v-if="!bookmarks?.length" class="p-8 text-center">
            <p class="text-gray-500 mb-4">You haven't bookmarked any starterkits yet.</p>
            <Link :href="route('starterkit.index')">
            <Button>Browse Starter Kits</Button>
            </Link>
          </div>
          <div v-else class="space-y-4">
            <StarterkitCard v-for="kit in bookmarks" :key="kit.id" :starterkit="kit" :show-bookmark="true" />
          </div>
        </TabsContent>

        <TabsContent value="my-kits">
          <div v-if="!starterkits?.length" class="p-8 text-center">
            <p class="text-gray-500 mb-4">You haven't created any starter kits yet.</p>
            <Link :href="route('starterkit.create')">
            <Button>Create Your First Kit</Button>
            </Link>
          </div>
          <div v-else class="space-y-4">
            <StarterkitCard v-for="kit in starterkits" :key="kit.id" :starterkit="kit" :show-actions="true" />
          </div>
        </TabsContent>
      </Tabs>
    </div>
  </AppLayout>
</template>
