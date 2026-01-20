<script setup lang="ts">
	import { Head, Link } from '@inertiajs/vue3';

	import AppLayout from '@/layouts/AppLayout.vue';
	import { dashboard } from '@/routes';
	import { type BreadcrumbItem } from '@/types';

	interface Product {
		id: number;
		name: string;
		price: number;
		pivot: {
			quantity: number;
			price: number;
			subtotal: number;
		};
	}

	interface User {
		id: number;
		name: string;
		email: string;
	}

	interface Purchase {
		id: number;
		user_id: number;
		total_amount: number;
		status: string;
		payment_method: string;
		transaction_id: string;
		notes: string | null;
		created_at: string;
		updated_at: string;
		user: User;
		products: Product[];
	}

	const props = defineProps<{
		purchase: Purchase;
		pagination: {
			current: number;
			total: number;
			previous_id: number | null;
			next_id: number | null;
			page: number;
		};
	}>();

	const breadcrumbs: BreadcrumbItem[] = [
		{
			title: 'Dashboard',
			href: dashboard().url,
		},
		{
			title: 'Purchases',
			href: `/dashboard/purchases?page=${props.pagination.page}`,
		},
		{
			title: `Purchase #${props.purchase.id} (${props.pagination.current}/${props.pagination.total})`,
			href: `/dashboard/purchases/${props.purchase.id}`,
		},
	];

	const getStatusColor = (status: string): string => {
		switch (status) {
			case 'completed':
				return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
			case 'pending':
				return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
			case 'cancelled':
				return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
			case 'refunded':
				return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
			default:
				return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
		}
	};

	const getPaymentMethodColor = (method: string): string => {
		switch (method) {
			case 'credit_card':
				return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
			case 'paypal':
				return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
			case 'bank_transfer':
				return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
			case 'cash':
				return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
			default:
				return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
		}
	};
</script>

<template>
    <Head :title="`Purchase #${purchase.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-6">
                <!-- Purchase Header -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase #{{ purchase.id }}</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Created on {{ new Date(purchase.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <!-- Previous Purchase -->
                            <Link
                                v-if="props.pagination.previous_id"
                                :href="`/dashboard/purchases/${props.pagination.previous_id}`"
                                class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800"
                            >
                                ← Previous
                            </Link>
                            
                            <!-- Edit Purchase -->
                            <Link
                                :href="`/dashboard/purchases/${purchase.id}/edit`"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            >
                                Edit Purchase
                            </Link>
                            
                            <!-- Next Purchase -->
                            <Link
                                v-if="props.pagination.next_id"
                                :href="`/dashboard/purchases/${props.pagination.next_id}`"
                                class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-offset-gray-800"
                            >
                                Next →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Purchase Details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer Information</h2>
                            <div class="space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name:</span>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ purchase.user.name }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</span>
                                    <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ purchase.user.email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Products</h2>
                            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Product
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Quantity
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Price
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Subtotal
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="product in purchase.products" :key="product.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ product.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ product.pivot.quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                ${{ product.pivot.price }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                ${{ product.pivot.subtotal }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Purchase Status -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Status</h2>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                    <div class="mt-1">
                                        <span :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            getStatusColor(purchase.status)
                                        ]">
                                            {{ purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method:</span>
                                    <div class="mt-1">
                                        <span :class="[
                                            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                            getPaymentMethodColor(purchase.payment_method)
                                        ]">
                                            {{ purchase.payment_method.replace('_', ' ').charAt(0).toUpperCase() + purchase.payment_method.replace('_', ' ').slice(1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Transaction ID:</span>
                                    <span class="text-sm text-gray-900 dark:text-white font-mono">{{ purchase.transaction_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Amount:</span>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">${{ purchase.total_amount.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="purchase.notes" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ purchase.notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
