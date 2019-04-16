<template>
    <div class="text-center">
    <div class="row"> 
        <div class="col-xs-12 col-md-6 mx-auto"> 
            <div class="spinner-border text-info" role="status" v-if="isLoading">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="row" v-else>
                <div class="col-xs-12 col-md-12" v-for="(project, index) in tasks" :key="project.pid">
                    <ul class="list-group">  
                        <li class="list-group-item listhead">
                            <div class="float-left">
                                <i class="material-icons md-18 align-middle calendar">calendar_today</i>
                                <i style="color: #fff;" class="align-middle">{{ project.project.pname }}</i>
                            </div>
                            <div class="float-right" style="color: #fff;">
                                <a href="#" @click="editProject(project.project.pid, project.project.pname)" class="remote-link"><i class="material-icons md-18 align-middle" style="font-size:16px;">create</i></a>
                                <span class="align-middle">|</span> 
                                <a href="#" @click="deleteProject(project.project.pid, project.project.pname)" class="remote-link"><i class="material-icons md-18 align-middle" style="font-size:16px;">delete</i></a>
                            </div>                        
                        </li>
                        <!-- Add task -->
                        <li class="list-group-item listedit">
                            <form v-on:submit.prevent>
                            <div class="input-group input-group-sm">
                            <i class="material-icons md-18 plusic">add</i>
                            <input type="text" class="form-control" placeholder="Add new task name" required v-model="tempmodel[index]">
                                <div class="input-group-append">
                                    <button class="btn btn-success btn-sm" type="submit" @click="addTask(index, project.project.pid, project.project.pname)">Add Task</button>
                                </div>
                            </div>
                            </form>
                        </li>
                        <!-- Draggable cycle  -->
                        <draggable :list="project.tasks" :group="project.project.pid" @start="drag=true" @end="drag=false" @update="update(project.tasks)" handle=".my-handle">
                            <li v-for="(task, index) in project.tasks" :key="task.id" :data-id="task.id" class="list-group-item list-group-item-action" id="list">
                                <div class="d-flex justify-content-between">
                                    <div class="chk-div">
                                        <input type="checkbox" aria-label="Checkbox" v-model="task.status" @change="editCheck(task.id, task.status)">
                                    </div>
                                    <div class="mr-auto left-pad" v-bind:class="{completed: task.status}">
                                        {{ task.name }}<span class="badge badge-warning ml-2" v-if="task.deadline">{{ task.deadline }}</span>
                                    </div>
                                    
                                    <div class="hide">
                                        <a href="#" class="my-handle"><i class="material-icons md-18 align-middle">unfold_more</i></a>|
                                        <a href="#" @click="editTask(project.project.pid, task.id, task.name, task.deadline)"><i class="material-icons md-18 align-middle">edit</i></a>|
                                        <a href="#" @click="deleteTask(project.project.pid, task.id, task.name)"><i class="material-icons md-18 align-middle">delete</i></a>
                                    </div>
                                </div>   
                            </li>       
                        </draggable>
                        <!-- /Draggable cycle -->
                    </ul>
                </div>
            </div>       
        </div>        
    </div>
    <div class="text-center">
        <button class="btn btn-primary" type="button" disabled v-if="buttonLoad">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <button class="btn btn-primary" type="button" @click="addProject" v-else>Add TODO List</button>
    </div>
    <!-- MODAL -->
        <div v-if="showModal">
            <transition name="modal">
                <div class="modal-mask">
                    <div class="modal-wrapper">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" v-html="modal.title"></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="modalReset">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p v-if="modal.input && modal.button=='edit'">Project name: <input v-model="modal.newPname" class="form-control" placeholder="New project name" required></p>
                                <p v-if="modal.input && modal.button=='editTask'">Task name: <input v-model="modal.newPname" class="form-control" placeholder="New task name" required></p>
                                <p v-if="modal.showDate">
                                    Task deadline: <datepicker v-model="modal.date" input-class="form-control"  placeholder="Select deadline date" format="dd-MM-yyyy"></datepicker>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <div class="w-25">
                                    <button type="button" class="btn btn-sm btn-secondary btn-block" @click="modalReset">Cancel</button>
                                </div>
                                <div class="w-25">
                                    <button class="btn btn-sm btn-primary btn-block" type="button" disabled v-if="modal.btnLoad">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary btn-block" v-if="modal.button=='delete' && !modal.btnLoad" @click="confirmDelete">Delete</button>
                                    <button type="button" class="btn btn-sm btn-primary btn-block" v-if="modal.button=='delTask' && !modal.btnLoad" @click="confirmDeleteTask">Delete Task</button>
                                    <button type="button" class="btn btn-sm btn-primary btn-block" v-if="modal.button=='edit' && !modal.btnLoad" @click="confirmEdit">Edit</button>
                                    <button type="button" class="btn btn-sm btn-primary btn-block" v-if="modal.button=='editTask' && !modal.btnLoad" @click="confirmEditTask">Edit Task</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </transition>
        </div>
        <!-- /MODAL -->
    </div>
</template>

<script>

import draggable from 'vuedraggable';
import Datepicker from 'vuejs-datepicker';

export default {
    components: {
        draggable,
        Datepicker
    },
    // props: {
    //     templist: Array
    // },
    data() {
        return{
            active: [],
            tempmodel: [],
            tempload:[],
            buttonLoad: false,
            showModal: false,
            modal:{
                title:  null,
                text:   null,
                pid:    null,
                id:    null,
                newPname: null,
                button: null,
                btnLoad: false,
                model: null,
                input: false,
                date: null,
                showDate: false
            }
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
        },
        addProject(){
            this.buttonLoad = true;
            axios.post('/api/projects/add'
            ).then((response) => {
                this.buttonLoad = false;
                this.$store.commit('addProject', response.data);

                console.log(response.data);
            }).catch((error) => {
                this.buttonLoad = false;
                console.log(error);
            })
        },
        deleteProject(pid, pname) {
            this.modal.title = 'Are you sure want to delete project <b>' +pname +'</b>?';
            this.modal.pid = pid;
            this.modal.button = 'delete';
            this.modal.input = false;
            this.showModal = true;
        },
        confirmDelete() {
            this.modal.btnLoad = true;
            axios.post('/api/projects/del', {'pid': this.modal.pid}
            ).then((response) => {
                this.modal.btnLoad = false;
                this.$store.commit('delProject', response.data.pid);
                this.showModal = false;
            }).catch((error) => {
                    this.modal.btnLoad = false;
                    console.log(error);
            })
        },
        editProject(pid, pname) {
            this.modal.title = 'Enter new name for project <b>' +pname +'</b>.';
            this.modal.pid = pid;
            this.modal.button = 'edit';
            this.modal.input = true;
            this.showModal = true;
        },
        confirmEdit() {
            this.modal.btnLoad = true;
            console.log(this.modal.pid);
            console.log(this.modal.newPname);
            axios.post('/api/projects/edit', {'pid': this.modal.pid, 'pname': this.modal.newPname}
            ).then((response) => {
                this.modal.btnLoad = false;
                console.log(response.data);
                this.$store.commit('editProject', response.data);
                this.showModal = false;
            }).catch((error) => {
                    this.modal.btnLoad = false;
                    console.log(error);
            })
        },
        addTask(index, pid, pname){
            
            axios.post('/api/tasks/add', {'pid': pid, 'pname': pname, 'name': this.tempmodel[index]}
            ).then((response) => {
                console.log(response.data);
                this.$store.commit('addTask', response.data);
            }).catch((error) => {
                    console.log(error);
            })

        },
        editCheck(pid, status) {
            axios.post('/api/tasks/chk', {'pid': pid, 'status': status}
            ).then((response) => {
                console.log(response.data);
            }).catch((error) => {
                    console.log(error);
            })
        },
        deleteTask(pid, id, name) {
            this.modal.title = 'Are you sure want to delete task <b>' +name +'</b>?';
            this.modal.pid = pid;
            this.modal.id = id;
            this.modal.button = 'delTask';
            this.modal.input = false;
            this.showModal = true;
        },
        confirmDeleteTask() {
            this.modal.btnLoad = true;
            axios.post('/api/tasks/del', {'id': this.modal.id, 'pid': this.modal.pid}
            ).then((response) => {
                this.modal.btnLoad = false;
                this.$store.commit('delTask', {'id': this.modal.id, 'pid': this.modal.pid});
                this.showModal = false;
            }).catch((error) => {
                    this.modal.btnLoad = false;
                    console.log(error);
            })
        },
        editTask(pid, id, name, date) {
            this.modal.title = 'Enter new name for task <b>' +name +'</b>.';
            this.modal.pid = pid;
            this.modal.id = id;
            this.modal.newPname = name;
            this.modal.button = 'editTask';
            this.modal.input = true;
            this.showModal = true;
            this.modal.showDate = true;
            this.modal.date = date;
        },
        confirmEditTask() {
            this.modal.btnLoad = true;
            console.log(this.modal.date);
            // axios.post('/api/tasks/edit', {'id': this.modal.pid, 'name': this.modal.newPname}
            // ).then((response) => {
            //     this.modal.btnLoad = false;
            //     this.showDate = false;
            //     console.log(response.data);
            //     this.$store.commit('editProject', response.data);
            //     this.showModal = false;
            // }).catch((error) => {
            //         this.modal.btnLoad = false;
            //         console.log(error);
            // })
        },
        modalReset() {
            this.showModal = false;
            this.modal.title = null;
            this.modal.text = null;
            this.modal.pid = null;
            this.modal.id = null;
            this.modal.newPname = null;
            this.modal.button= null;
            this.modal.btnLoad = false;
            this.modal.model = null;
            this.modal.input = false;
            this.modal.date = null;
            this.modal.showDate = false;
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

<style>
    .completed{
        text-decoration: line-through;
    }
    .left-pad{
        padding-left: 1rem;
    }
    .chk-div{
        padding-right: 0.5rem;
        border-right: 4px double #ccc;
    }
    .list-group-item{
        padding: 0 0.75rem;
    } 
    .my-handle {
        cursor: move;
        cursor: -webkit-grabbing;
    }  
    .listhead {
        background-image: linear-gradient(#5082BC, #385F9A);
        margin-top: 1rem;
        padding: 0.2rem;
    }
    .listedit {
        background-image: linear-gradient(#E5E4E4, #C6C6C6);
        padding: 0.2rem;
    }
    .btn-success{
        background-image: linear-gradient(#90BEA5, #658973);
        border: #5a7a66;
    }
    .calendar{
        text-shadow: 0px 0.4px 2px rgb(65, 147, 255);
        color: #424242;
        transition: all 1s;
    }
    .plusic{
        font-weight: bold;
        color: #658973;
        padding-top: 2px;
    }
    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        display: table;
        transition: opacity .3s ease;
    }

    .modal-wrapper {
        display: table-cell;
        vertical-align: middle;
    }
    .remote-link{
        color: #ccc;
    }
    .remote-link:hover{
        color: #fff;
    }
    #list .hide{
    visibility: hidden;
    }
    #list:hover .hide{
        visibility: visible;
    }
</style>