<!-- eslint-disable vue/no-v-html -->
<script setup lang="ts">
	import { Head, useForm, Link } from '@inertiajs/vue3';
	import { ref, watch, onMounted } from 'vue';

	import AppLayout from '@/layouts/AppLayout.vue';
	import { dashboard } from '@/routes';
	import { type BreadcrumbItem } from '@/types';

	// Helper function to decode HTML entities
	const decodeHtmlEntities = (text: string): string => {
		const textarea = document.createElement('textarea');
		textarea.innerHTML = text;
		return textarea.value;
	};

	interface Product {
		id: number;
		name: string;
		description: string;
		price: number;
		category: string;
		stock: number;
		sku: string;
		is_active: boolean;
		created_at: string;
		updated_at: string;
	}

	interface PaginatedProducts {
		data: Product[];
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
		products: PaginatedProducts;
		filters: {
			search?: string;
		};
	}>();

	// Debug: Log the props to console
	onMounted(() => {
		console.log('Products props:', props.products);
		console.log('Products data length:', props.products.data.length);
		console.log('Products total:', props.products.total);
	});

	const breadcrumbs: BreadcrumbItem[] = [
		{
			title: 'Dashboard',
			href: dashboard().url,
		},
		{
			title: 'Products',
			href: '/dashboard/products',
		},
	];

	const search = ref(props.filters.search || '');

	const form = useForm({
		search: props.filters.search || '',
	});

	watch(search, (value) => {
		form.search = value;
		form.get('/dashboard/products', {
			preserveState: true,
			preserveScroll: true,
		});
	});
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Manage all products in the system ({{ products.total }} total)
                            </p>
                        </div>
                        <div class="w-64">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search products..."
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
                                    SKU
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Price
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Stock
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="products.data.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No products found.
                                </td>
                            </tr>
                            <tr v-else v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ product.name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ product.description.substring(0, 50) }}{{ product.description.length > 50 ? '...' : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ product.sku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ product.category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ product.price }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        product.stock > 10 
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : product.stock > 0
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    ]">
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                        product.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    ]">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
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
                        <span class="font-medium">{{ (products.current_page - 1) * products.per_page + 1 }}</span>
                        to 
                        <span class="font-medium">{{ Math.min(products.current_page * products.per_page, products.total) }}</span>
                        of 
                        <span class="font-medium">{{ products.total }}</span>
                        results
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <template v-for="link in products.links" :key="link.label">
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
