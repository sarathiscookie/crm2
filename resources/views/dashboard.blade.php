@extends('layouts.app')

@section('title', 'Dashboard')

@section('style')
    <style>
        th.active .arrow {
            opacity: 1;
        }

        .arrow {
            display: inline-block;
            vertical-align: middle;
            width: 0;
            height: 0;
            margin-left: 5px;
            opacity: 0.66;
        }

        .arrow.asc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #42b983;
        }

        .arrow.dsc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #42b983;
        }

        #search {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <h1 class="page-header">Customers List</h1>
        <template id="grid-template">
            <table class="table table-hover table-bordered">
                <thead>
                <tr>
                    <th>Id</th>
                    <th v-for="key in columns" @click="sortBy(key)" :class="{active: sortKey == key}">@{{key | capitalize}}<span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(index, customer) in customers | filterBy filterKey | orderBy sortKey sortOrders[sortKey]">
                    <td>@{{ index + 1 }}</td>
                    <td>@{{customer.firstname}}</td>
                    <td>@{{customer.lastname}}</td>
                    <td>@{{customer.email}}</td>
                    <td>@{{customer.phone}}</td>
                    <td>@{{customer.created_on}}</td>
                </tr>
                </tbody>
            </table>
        </template>
        <div id="app">
            <div class="form-group col-md-4">
                <form id="search" class="form-inline">
                    <label for="query">Search </label>
                    <input name="query" class="form-control" v-model="searchQuery">
                </form>
                {{--<input v-model="filterData" placeholder="Filter by..." type="text" class="form-control" autocomplete="off">--}}
            </div>
            <br>
            <customer-grid  :customers="{{$listCustomers}}"  :columns="gridColumns"  :filter-key="searchQuery"></customer-grid>
        </div>
    </div>
</div>
@endsection

@push('script')
<!-- Vue Js -->
<script src="/assets/js/vue.js"></script>
<script src="/assets/js/vue-resource.js"></script>
<script>
    Vue.component('customer-grid', {
        template: '#grid-template',
        props: {
            customers: Array,
            columns: Array,
            filterKey: String
        },
        data: function () {
            var sortOrders = {}
            this.columns.forEach(function (key) {
                sortOrders[key] = 1
            })
            return {
                sortKey: '',
                sortOrders: sortOrders
            }
        },
        methods: {
            sortBy: function (key) {
                this.sortKey = key
                this.sortOrders[key] = this.sortOrders[key] * -1
            }
        }
    })

    // bootstrap the demo
    var demo = new Vue({
        el: '#app',
        data: {
            searchQuery: '',
            gridColumns: ['firstname', 'lastname', 'email', 'phone', 'created_on'],
            gridData: null
        },

        created: function() {
            this.fetchData()
        },

        methods: {
            fetchData: function () {
                var self = this;
                $.get('/', function( data ) {
                    self.gridData = data;
                });
            }
        }
    });
</script>
@endpush


