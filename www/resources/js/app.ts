/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./bootstrap"
import Vue from "vue"

import DashboardComponent from "./components/DashboardComponent.vue";

Vue.component('dashboard-component', DashboardComponent)

new Vue({
    el: '#app'
})
