<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { AlertCircle } from 'lucide-vue-next';
import { useGithubValidation } from '@/composables/useGithubValidation';
import TagSelector from '@/components/TagSelector.vue';

const props = defineProps<{
  starterkit: {
    id: string;
    url: string;
    tags: Array<{ id: number; name: string }>;
  };
  availableTags: Array<{ id: number; name: string }>;
}>();

const form = useForm({
  url: props.starterkit.url,
  tags: props.starterkit.tags.map(tag => tag.id),
});

const { isValidating, validationError, validateGithubUrl } = useGithubValidation();

const submit = async () => {
  if (await validateGithubUrl(form.url)) {
    form.put(route('starterkit.update', props.starterkit.id));
  }
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Edit Starterkit',
    href: route('starterkit.edit', props.starterkit.id),
  },
];
</script>

<template>

  <Head title="Edit Starterkit" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <Card>
        <CardHeader>
          <CardTitle>Edit Starterkit</CardTitle>
        </CardHeader>
        <form @submit.prevent="submit">
          <CardContent>
            <div class="space-y-4">
              <div class="space-y-2">
                <Label for="url">GitHub Repository URL</Label>
                <Input id="url" v-model="form.url" type="url" placeholder="https://github.com/username/repo" required />
                <Alert v-if="form.errors.url" variant="destructive" class="mt-2">
                  <AlertCircle class="h-4 w-4" />
                  <AlertDescription>{{ form.errors.url }}</AlertDescription>
                </Alert>
                <Alert v-if="validationError" variant="destructive" class="mt-2">
                  <AlertCircle class="h-4 w-4" />
                  <AlertDescription>{{ validationError }}</AlertDescription>
                </Alert>
              </div>

              <div class="space-y-2">
                <Label>Tags</Label>
                <TagSelector v-model="form.tags" :available-tags="availableTags" />
                <Alert v-if="form.errors.tags" variant="destructive" class="mt-2">
                  <AlertCircle class="h-4 w-4" />
                  <AlertDescription>{{ form.errors.tags }}</AlertDescription>
                </Alert>
              </div>
            </div>
          </CardContent>
          <CardFooter class="flex justify-between">
            <Button type="button" variant="outline" :href="route('dashboard')" as="a">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing || isValidating">
              <span v-if="isValidating">Validating...</span>
              <span v-else>Update Starterkit</span>
            </Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>
