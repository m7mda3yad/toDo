<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Import the TaskList component
import TaskList from '@/Components/TaskList.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

                        <task-list></task-list> <!-- Render the TaskList component here -->
    </AuthenticatedLayout>
</template>

