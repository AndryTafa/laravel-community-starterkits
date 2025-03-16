<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import BookmarkButton from '@/components/BookmarkButton.vue';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import axios from 'axios';
import StarterkitCard from '@/components/StarterkitCard.vue';

const props = defineProps<{
  starterkits: {
    data: Array<{
      id: string;
      url: string;
      user: {
        id: number;
        name: string;
      };
      tags: Array<{
        id: number;
        name: string;
      }>;
      created_at: string;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  auth: {
    user: any | null;
  };
  tags: Array<{
    id: number;
    name: string;
  }>;
}>();

const isLoggedIn = !!props.auth.user;
const isLoading = ref(false);
const loadedStarterkits = ref(props.starterkits.data);
const currentPage = ref(props.starterkits.current_page);
const hasMorePages = ref(currentPage.value < props.starterkits.last_page);

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Starterkits',
    href: '/app/starterkits',
  },
];

// Format date helper
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(date);
};

// Load more starterkits for infinite scrolling
const loadMoreStarterkits = async () => {
  if (isLoading.value || !hasMorePages.value) return;

  isLoading.value = true;
  try {
    const nextPage = currentPage.value + 1;
    const response = await axios.get('/api/starterkits/load-more', {
      params: {
        page: nextPage,
        tags: selectedTags.value
      }
    });

    const newStarterkits = response.data.data;
    loadedStarterkits.value = [...loadedStarterkits.value, ...newStarterkits];

    currentPage.value = response.data.current_page;
    hasMorePages.value = currentPage.value < response.data.last_page;
  } catch (error) {
    console.error('Error loading more starterkits:', error);
  } finally {
    isLoading.value = false;
  }
};

// Infinite scroll handler
const handleScroll = () => {
  if (isLoading.value) return;

  const scrollPosition = window.innerHeight + window.scrollY;
  const bodyHeight = document.body.offsetHeight;

  // Load more when user scrolls to 80% of the page
  if (scrollPosition >= bodyHeight * 0.8 && hasMorePages.value) {
    loadMoreStarterkits();
  }
};

// Setup scroll event listener
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

// Cleanup
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const selectedTags = ref<number[]>([]);

const filterStarterkits = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/api/starterkits/load-more', {
      params: {
        page: 1,
        tags: selectedTags.value
      }
    });

    loadedStarterkits.value = response.data.data;
    currentPage.value = response.data.current_page;
    hasMorePages.value = currentPage.value < response.data.last_page;
  } catch (error) {
    console.error('Error filtering starterkits:', error);
  } finally {
    isLoading.value = false;
  }
};

</script>

<template>

  <Head title="Starterkits Gallery" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Sign Up Banner for guests -->
      <div v-if="!isLoggedIn"
        class="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
        <div class="flex items-start">
          <div class="flex-1">
            <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300">Welcome to Laravel Starterkits!</h3>
            <p class="text-blue-700 dark:text-blue-400 mt-1">
              Sign up to access all features including submitting your own starterkits.
            </p>
          </div>
          <div class="ml-4">
            <Link :href="route('register')">
            <Button size="sm" variant="outline"
              class="border-blue-500 text-blue-600 hover:bg-blue-100 dark:border-blue-400 dark:text-blue-300">
              Sign Up
            </Button>
            </Link>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Starterkits Gallery</h1>
        <Link
          :href="isLoggedIn ? route('starterkit.create') : route('register', { redirect: route('starterkit.create') })">
        <Button>Add Starter Kit</Button>
        </Link>
      </div>

      <div class="mb-6">
        <h2 class="text-lg font-medium mb-2">Filter by tags</h2>
        <div class="flex flex-wrap gap-2 mb-4">
          <template v-if="tags && tags.length">
            <button v-for="tag in tags" :key="tag.id" @click="
              selectedTags.includes(tag.id)
                ? selectedTags = selectedTags.filter(t => t !== tag.id)
                : selectedTags.push(tag.id);
            filterStarterkits();
            " :class="[
                'rounded-full px-3 py-1 text-sm transition-colors',
                selectedTags.includes(tag.id)
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-secondary text-secondary-foreground hover:bg-secondary/80'
              ]">
              {{ tag.name }}
            </button>
          </template>
        </div>
      </div>

      <div v-if="isLoading" class="mt-4 text-center">
        <div
          class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent"
          role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="loadedStarterkits.length === 0" class="p-8 text-center">
        <p class="text-gray-500 mb-4">No starterkits have been added yet.</p>
        <Link :href="route('starterkit.create')">
        <Button>Be the first to add one!</Button>
        </Link>
      </div>

      <!-- List view instead of grid -->
      <div v-else class="space-y-4">
        <StarterkitCard v-for="kit in loadedStarterkits" :key="kit.id" :starterkit="kit" :show-bookmark="isLoggedIn" />
      </div>
    </div>
  </AppLayout>
</template>
