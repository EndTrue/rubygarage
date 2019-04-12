<template>
    <div class="row"> 
        <div class="col-md-3"></div>
        <div class="col-xs-12 col-md-6"> 
            <div class="spinner-border text-info" role="status" v-if="isLoading">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="row" v-else>
                <div class="col-xs-12 col-md-12" v-for="project in tasks" :key="project.pid">
                    <ul class="list-group">  
                        <li class="list-group-item">{{ project.project.pname }}</li>
                        <li class="list-group-item">
                            <div class="input-group">
                            "+"<input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" id="button-addon2">Button</button>
                                </div>
                            </div>
                        </li>
                        <draggable :list="project.tasks" :group="project.project.pid" @start="drag=true" @end="drag=false" @update="update(project.tasks)">
                            <li v-for="(task, index) in project.tasks" :key="task.id" :data-id="task.id" class="list-group-item">{{ task.name }} [{{task.deadline}}]</li>
                        </draggable>
                    </ul>
                </div>
            </div>       
        </div>        
        <div class="col-md-3"></div>   
    </div>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable
    },
    // props: {
    //     templist: Array
    // },
    data() {
        return{
            templist: []
        }
        
    },
    mounted() {
            if (this.tasks.length) {
                return;
            }
            
            this.$store.dispatch('getTasks');
        },
    methods: {
        update(tasks){
            tasks.map((task, index) => {
                task.order = index + 1;
            });
            console.log(tasks);
            axios.post('/api/tasks/upd', tasks
            ).then((response) => {
                console.log(response.data);
            }).catch((error) => {
                console.log(error);
            })
        }
    },
    computed: {
            currentUser() {
                return this.$store.getters.currentUser;
            },
            // projects() {
            //     return this.$store.getters.projects;
            // },
            tasks() {
                console.log(this.$store.getters.tasks);
                return this.$store.getters.tasks;
            },
            isLoading() {
                return this.$store.getters.isLoading;
            }                     
        }
}
</script>