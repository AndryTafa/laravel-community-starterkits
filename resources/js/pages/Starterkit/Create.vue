<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useGithubValidation } from '@/composables/useGithubValidation';
import TagSelector from '@/components/TagSelector.vue';

const form = useForm({
  url: '',
  tags: [] as number[],
});

const { isValidating, validationError, validateGithubUrl } = useGithubValidation();

const handleSubmit = async () => {
  if (await validateGithubUrl(form.url)) {
    form.post(route('starterkit.store'));
  }
};

const props = defineProps<{
  availableTags: Array<{ id: number; name: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Starterkit',
    href: '/app/starterkit',
  },
  {
    title: 'Create',
    href: '/app/starterkit/create',
  },
];
</script>

<template>

  <Head title="Add Starter Kit" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle>Create New Starterkit</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
              <Label for="url">GitHub URL</Label>
              <Input id="url" v-model="form.url" placeholder="https://github.com/username/repository" />
              <div v-if="form.errors.url" class="text-red-500 text-sm mt-1">
                {{ form.errors.url }}
              </div>
              <div v-if="validationError" class="text-red-500 text-sm mt-1">
                {{ validationError }}
              </div>
            </div>

            <div class="space-y-2">
              <Label>Tags</Label>
              <TagSelector v-model="form.tags" :available-tags="availableTags" />
              <div v-if="form.errors.tags" class="text-red-500 text-sm mt-1">
                {{ form.errors.tags }}
              </div>
            </div>

            <div>
              <Button type="submit" :disabled="form.processing || isValidating">
                <span v-if="isValidating">Validating...</span>
                <span v-else>Submit</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
