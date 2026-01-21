<script setup lang="ts">
  import { ref } from 'vue';
  import { onMounted } from 'vue';

  const newTask = ref<string>('');
  const base_url = 'http://localhost:8080';
  
  interface Task {
    id: number,
    title: string,
    done: boolean,
  }; 
  const allTask = ref<Task[]>([]);
  const taskInputOpen = ref<boolean>(false);
  

  function addTask(title: string) {
    fetch('http://localhost:8080/tasks', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({title: newTask.value})
    })
    .then(response => response.json())
    .then(data => {console.log(data); loadTask();})
    .catch(err => console.error('Error creating new task: ', err));
    
    newTask.value = ''
    taskInputOpen.value = false;
  };

  function loadTask() {
    fetch(`${base_url}/tasks`)
    .then(response => response.json())
    .then(data => {
      allTask.value = data.tasks.map((task: any) => ({
        id: task.id,
        title: task.title,
        done: task.active
      }))
    })
    .catch(err => console.error('Error retrieving tasks: ', err));
  }

  function deleteTask(id: number) {
    fetch(`${base_url}/task/${id}`, {
      method: 'DELETE',
      headers: {
      'Content-Type' : 'application/json'
      }
    })
    .then(response => response.text())
    .then(data => {console.log(data); loadTask();})
    .catch(err => console.error('Error deleting task: ', err));
  }

  onMounted(() => {
    loadTask();
  });


  function finishTask(id: number) {
    fetch(`${base_url}/tasks/${id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(err => console.error('Failed to update task: ', err));
  }



  function openAddTask() {
    taskInputOpen.value = true;
  }

  function cancelAddTask() {
    taskInputOpen.value = false;
    newTask.value = '';
  };

</script>

<template>
  <main class="flex w-full h-screen justify-center items-center">
    <section class="flex w-[700px] h-auto flex-col bg-[#EDE8D0] p-4 rounded-[10px]">
      <div class="flex items-center justify-between w-full"> 
        <span class="font-bold text-[#141414] text-[26px]">TO DO LIST</span>
        <button @click="openAddTask" class="flex items-center gap-2 opacity-50 hover:opacity-100 active:opacity-50" title="Add task">
          <span class="text-[18px] text-[#4F4D46] font-bold">Add New Task</span>
          <img src="/src/asset/icons/feather-pen.png" alt="feather-pen-icon" class="size-8 p-0.5 cursor-pointer rotate-0 hover:rotate-45  transition-all duration-200">
        </button>
      </div>

      <div class="flex flex-col w-full gap-2">
        <!-- This contains the list of task -->
        <div class="flex flex-col items-center w-full gap-2 py-4"> 
          <!-- This contains a block of one task: Task string, button:Save/Delete/Radio indicating that the task is finished -->

          <div class="flex items-center justify-between w-full gap-2" v-if="taskInputOpen===true">
            <!-- Displays when setting up new task -->
            <input type="text" placeholder="Enter the task here..." class="p-2 rounded-[5px] text-[16px] w-full"
            v-model="newTask">
  
            <div class="flex items-center gap-1 ">
              <button @click="addTask(newTask)" class="bg-[#787569] p-2 text-[#FFF] flex items-center justify-center rounded-[10px] hover:font-bold transition-all duration-200">Save</button>
              <button @click="cancelAddTask" class="bg-[#C9C5B1] p-2 text-[#141414] flex items-center justify-center rounded-[10px] hover:font-bold transition-all duration-200">Cancel</button>
            </div>
          </div>
          
          <div v-for="(task, index) in allTask" v-if="allTask.length > 0" class="flex items-center justify-between w-full gap-2 py-1 border-b border-b-[#C9C5B1]"> 
            <!-- Displays when task is created -->
            <div class="flex items-center gap-2">
              <input type="checkbox" @change="finishTask(task.id)" v-model="task.done" v-if="!task.done"> 
              <span :key="task.id" :class="['text-[18px] text-[#4F4D46]', task.done && 'line-through']">{{task.title}}</span>
            </div>

            <button @click="deleteTask(task.id)" class="flex items-center justify-center font-bold opacity-50 hover:opacity-100 active:opacity-50">Clear</button>
          </div>

          <span v-else class="flex items-center justify-center w-full text-[18px] py-2">No task for today</span>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped></style>

