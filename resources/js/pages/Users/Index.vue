<!-- eslint-disable vue/no-v-html -->
<script setup lang="ts">
	import { Head, useForm, Link } from '@inertiajs/vue3';
	import { ref, watch } from 'vue';

	import AppLayout from '@/layouts/AppLayout.vue';
	import { dashboard } from '@/routes';
	import { type BreadcrumbItem } from '@/types';

	// Helper function to decode HTML entities
	const decodeHtmlEntities = (text: string): string => {
		const textarea = document.createElement('textarea');
		textarea.innerHTML = text;
		return textarea.value;
	};

	interface User {
		id: number;
		name: string;
		email: string;
		roles: Array<{
			id: number;
			name: string;
			slug: string;
			description: string;
			is_active: boolean;
		}>;
		created_at: string;
		updated_at: string;
	}

	interface PaginatedUsers {
		data: User[];
		current_page: number;
		last_page: number;
		per_page: number;
		total: number;
		links: Array<{
			url: string | null;
			label: string;
			active: boolean;
		}>;
	}

	const props = defineProps<{
		users: PaginatedUsers;
		filters: {
			search?: string;
		};
	}>();

	const breadcrumbs: BreadcrumbItem[] = [
		{
			title: 'Dashboard',
			href: dashboard().url,
		},
		{
			title: 'Users',
			href: '/dashboard/users',
		},
	];

	const search = ref(props.filters.search || '');

	const form = useForm({
		search: props.filters.search || '',
	});

	watch(search, (value) => {
		form.search = value;
		form.get('/dashboard/users', {
			preserveState: true,
			preserveScroll: true,
		});
	});
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Manage all users in the system
                            </p>
                        </div>
                        <div class="w-64">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search users..."
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Roles
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Created
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="role in user.roles" 
                                            :key="role.id"
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': role.slug === 'administrator',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': role.slug === 'manager',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': role.slug === 'editor',
                                                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': role.slug === 'viewer'
                                            }"
                                        >
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ new Date(user.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing 
                        <span class="font-medium">{{ (users.current_page - 1) * users.per_page + 1 }}</span>
                        to 
                        <span class="font-medium">{{ Math.min(users.current_page * users.per_page, users.total) }}</span>
                        of 
                        <span class="font-medium">{{ users.total }}</span>
                        results
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <template v-for="link in users.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md border',
                                    link.active
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700'
                                ]"
								preserve-state
								preserve-scroll
                            >
                                <span v-html="decodeHtmlEntities(link.label)"></span>
                            </Link>
                            <span
                                v-else
                                class="px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400"
                            >
                                <span v-html="decodeHtmlEntities(link.label)"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
