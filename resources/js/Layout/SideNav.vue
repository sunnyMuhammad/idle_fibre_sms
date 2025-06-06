<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();

const isMobileSidebarOpen = ref(false);

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const currentUrl = computed(() => page.url);
const isActiveRoute = (route) => {
    return currentUrl.value.startsWith(route);
};
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <button
            @click="toggleMobileSidebar"
            class="md:hidden fixed top-3 left-3 z-30 p-2 bg-gray-800 rounded shadow"
            aria-label="Toggle sidebar"
        >
            <span class="material-icons text-white">menu</span>
        </button>

        <aside
            :class="[
                'bg-gray-900 text-white flex flex-col fixed top-0 bottom-0 transition-transform duration-300 ease-in-out z-40 w-64',
                {
                    'translate-x-0': isMobileSidebarOpen,
                    '-translate-x-full md:translate-x-0': !isMobileSidebarOpen,
                },
            ]"
        >
            <div class="p-4 border-b border-gray-700">
                <h4 class="text-white">{{ page.props.user.user_name }}</h4>
            </div>

            <nav class="flex-grow overflow-auto">
                <ul class="px-4 mt-3 space-y-2">
                    <li v-if="page.props.user.can['list-user']">
                        <Link
                            href="/list-user"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-user')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">groups</span>
                            <span>Users</span>
                        </Link>
                    </li>

                        <li v-if="page.props.user.can['list-role']">
                        <Link
                            href="/list-role"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-role')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">groups</span>
                            <span>Roles</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-product']">
                        <Link
                            href="/product-stock-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/product-stock-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">inventory</span>
                            <span>Product Stock List</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-category']">
                        <Link
                            href="/list-category"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-category')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">category</span>
                            <span>Category</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-product']">
                        <Link
                            href="/list-product"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-product')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">inventory</span>
                            <span>Products</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-requisition']">
                        <Link
                            href="/list-requisition"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-requisition')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">shopping_cart</span>
                            <span>Requisitions</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['receive-requisition']">
                        <Link
                            href="/requisition-product-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/requisition-product-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">receipt</span>
                            <span>Receive Requisition Products</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['approve-requisition-receive']">
                        <Link
                            href="/requisition-received-request-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute(
                                    '/requisition-received-request-list'
                                )
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons text-green-500"
                                >check_circle</span
                            >
                            <span>Approve Requisition</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['approve-floor-receive']">
                        <Link
                            href="/floor-receive-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/floor-receive-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons text-green-500"
                                >check_circle</span
                            >
                            <span>Approve Floor Recieve</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-issue-product']">
                        <Link
                            href="/issue-product-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/issue-product-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons"
                                >remove_shopping_cart</span
                            >
                            <span>Product Issue List</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-purchase']">
                        <Link
                            href="/list-purchase"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-purchase')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">shopping_bag</span>
                            <span>Purchases</span>
                        </Link>
                    </li>

                         <li v-if="page.props.user.can['list-vendor']">
                        <Link
                            href="/list-vendor"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/list-vendor')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">shopping_bag</span>
                            <span>Vendors</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-minimum-product']">
                        <Link
                            href="/minimum-product-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/minimum-product-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">warning</span>
                            <span>Minimum Stock</span>
                        </Link>
                    </li>

                    <li v-if="page.props.user.can['list-damage-product']">
                        <Link
                            href="/damage-product-list"
                            :class="[
                                'flex items-center gap-2 px-4 py-2 rounded',
                                isActiveRoute('/damage-product-list')
                                    ? 'bg-gray-700'
                                    : 'text-white',
                            ]"
                        >
                            <span class="material-icons">dangerous</span>
                            <span>Damaged Stock List</span>
                        </Link>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <Link
                    href="/logout"
                    class="btn bg-red-600 hover:bg-red-700 text-white w-full py-2 px-4 rounded"
                >
                    Logout
                </Link>
            </div>
        </aside>

        <main class="flex-grow p-4 md:ml-64 min-h-screen overflow-y-auto">
            <div class="bg-white rounded shadow p-4">
                <slot></slot>
            </div>
        </main>
    </div>
</template>
