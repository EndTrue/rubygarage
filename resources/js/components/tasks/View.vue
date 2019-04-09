<template>
    <div> 
        <div class="row" v-for="project in projects" :key="project.pi">
            
            <ul class="list-group col-xs-12 col-md-6">  
                <li class="list-group-item">{{ project.pname }}</li>
                <li class="list-group-item">
                    <div class="input-group">
                       "+"<input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" id="button-addon2">Button</button>
                        </div>
                    </div>
                </li>
                <draggable v-model="myArray" group="people" @start="drag=true" @end="drag=false">
                    <li class="list-group-item" v-for="task in filt(project.pid)" :key="task.id">{{ task.name }}</li>
                </draggable>
            </ul>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable
    },
    mounted() {
            if (this.tasks.length) {
                return;
            }
            
            this.$store.dispatch('getTasks');
        },
    methods: {
        filt(category){
            return this.tasks.filter(task => task.pid == category)
        },
    },
    computed: {
            currentUser() {
                return this.$store.getters.currentUser;
            },
            projects() {
                return this.$store.getters.projects;
            },
            tasks() {
                return this.$store.getters.tasks;
            }                     
        }
}
</script>