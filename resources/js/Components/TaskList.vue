<template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Search Bar -->
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search tasks by title or description"
              @input="searchTasks"
              class="border rounded-md p-2 mb-4"
            />

            <!-- Add Task Button -->
            <button @click="showAddTaskForm = true" class="bg-blue-500 text-white p-2 rounded-md mb-4">
              Add Task
            </button>

            <!-- Task List Table -->
            <table class="min-w-full table-auto">
              <thead>
                <tr>
                  <th class="px-4 py-2">Title</th>
                  <th class="px-4 py-2">Description</th>
                  <th class="px-4 py-2">Status</th>
                  <th class="px-4 py-2">Category</th>
                  <th class="px-4 py-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="task in tasks.data" :key="task.id">
                  <td class="border px-4 py-2">{{ task.title }}</td>
                  <td class="border px-4 py-2">{{ task.description }}</td>
                  <td class="border px-4 py-2">{{ task.status }}</td>
                  <td class="border px-4 py-2">{{ task.category.name }}</td>
                  <td class="border px-4 py-2">
                    <button @click="editTask(task)" class="bg-yellow-500 text-white p-1 rounded-md">
                      Edit
                    </button>
                    <button @click="deleteTask(task.id)" class="bg-red-500 text-white p-1 rounded-md">
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4">
              <button
                v-if="tasks.prev_page_url"
                @click="getTasks(tasks.prev_page_url)"
                class="px-4 py-2 bg-blue-500 text-white rounded-md"
              >
                Previous
              </button>
              <button
                v-if="tasks.next_page_url"
                @click="getTasks(tasks.next_page_url)"
                class="px-4 py-2 bg-blue-500 text-white rounded-md"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Update Task Form Modal -->
    <div v-if="showAddTaskForm" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg">
        <h3 class="text-xl mb-4">{{ isEditing ? 'Update Task' : 'Add Task' }}</h3>
        <form @submit.prevent="isEditing ? updateTask() : createTask()">
          <input
            type="text"
            v-model="newTask.title"
            placeholder="Title"
            class="border p-2 mb-4 w-full"
            required
          />
          <textarea
            v-model="newTask.description"
            placeholder="Description"
            class="border p-2 mb-4 w-full"
            required
          ></textarea>
          <select v-model="newTask.status" class="border p-2 mb-4 w-full">
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
          <select v-model="newTask.category_id" class="border p-2 mb-4 w-full" required>
            <option value="" disabled>Select Category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <button type="submit" class="bg-blue-500 text-white p-2 rounded-md">
            {{ isEditing ? 'Update Task' : 'Create Task' }}
          </button>
          <button
            type="button"
            @click="showAddTaskForm = false"
            class="ml-2 bg-gray-500 text-white p-2 rounded-md"
          >
            Cancel
          </button>
        </form>
      </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Assuming the Laravel user ID and CSRF token are passed to the frontend via Blade
const userId = window.Laravel.userId;  // Laravel user ID
const csrfToken = window.Laravel.csrfToken;  // Laravel CSRF token

// Axios setup to send authentication data
axios.defaults.withCredentials = true;  // Send cookies (session)
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;  // CSRF token for session-based auth

const tasks = ref({ data: [], prev_page_url: null, next_page_url: null });
const searchQuery = ref("");
const showAddTaskForm = ref(false);
const isEditing = ref(false);
const taskToEdit = ref(null);
const newTask = ref({
  title: "",
  description: "",
  status: "pending",
  user_id: userId,  // Add user_id here
  category_id: null,  // Initialize category_id
});

const categories = ref([]);

// Fetch tasks
const getTasks = async (url = "/api/tasks") => {
  try {
    const response = await axios.get(url, {
      params: { search: searchQuery.value },
    });
    tasks.value = response.data;
  } catch (error) {
    console.error("Error fetching tasks", error);
  }
};

// Fetch categories
const getCategories = async () => {
  try {
    const response = await axios.get("/api/tasks/create"); // Replace with correct route to get categories
    categories.value = response.data.categories;
  } catch (error) {
    console.error("Error fetching categories", error);
  }
};

// Create a new task
const createTask = async () => {
  try {
    const response = await axios.post("/api/tasks", newTask.value);
    tasks.value.data.unshift(response.data.task);
    newTask.value = { title: "", description: "", status: "pending", user_id: userId, category_id: null }; // reset form
    showAddTaskForm.value = false; // hide form
  } catch (error) {
    console.error("Error creating task", error);
  }
};

// Edit a task (open the modal with task data)
const editTask = (task) => {
  isEditing.value = true;
  taskToEdit.value = task;
  newTask.value = { ...task }; // Pre-fill the form with the task data
  showAddTaskForm.value = true;
};

// Update task
const updateTask = async () => {
  try {
    const response = await axios.patch(`/api/tasks/${taskToEdit.value.id}`, newTask.value);
    const updatedTask = response.data.task;
    const index = tasks.value.data.findIndex(task => task.id === updatedTask.id);
    tasks.value.data[index] = updatedTask;  // Update task in the list
    showAddTaskForm.value = false;
    isEditing.value = false;
    taskToEdit.value = null;
  } catch (error) {
    console.error("Error updating task", error);
  }
};

// Delete a task
const deleteTask = async (taskId) => {
  try {
    await axios.delete(`/api/tasks/${taskId}`);
    tasks.value.data = tasks.value.data.filter((task) => task.id !== taskId);
  } catch (error) {
    console.error("Error deleting task", error);
  }
};

// Search tasks based on query
const searchTasks = () => {
  getTasks();
};

// Initial load of tasks and categories
onMounted(() => {
  getTasks();
  getCategories();
});
</script>

<style scoped>
/* Add any necessary styling here */
</style>
