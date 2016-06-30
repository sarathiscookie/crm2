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
        <h1 class="page-header">Kunden-Übersicht</h1>
        <template id="grid-template">
            <table class="table table-hover table-bordered">
                <thead>
                <tr>
                    <th v-for="key in columns" @click="sortBy(key)" :class="{active: sortKey == key}">@{{ heading[key] }} <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"></span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(index, customer) in customers | filterBy filterKey | orderBy sortKey sortOrders[sortKey]">
                    <td>@{{ customer.erp_id }}</td>
                    <td>@{{customer.firstname}}</td>
                    <td><a href="{{ url('/customer/details/') }}/@{{ customer.id }}">@{{customer.lastname}}</a></td>
                    <td>@{{customer.email}}</td>
                    <td>@{{customer.phone_1}}</td>
                    <td>@{{customer.status}}</td>
                    <td>@{{customer.created_on}}</td>
                </tr>
                </tbody>
            </table>
        </template>
        <div id="app">
            <div class="form-group col-md-4">
                <form id="search" class="form-inline">
                    <label for="query">Suche </label>
                    <input name="query" class="form-control" v-model="searchQuery">
                </form>
            </div>
            <br>
            <customer-grid  :customers="{{$listCustomers}}"  :columns="gridColumns" :heading="colTitles"  :filter-key="searchQuery"></customer-grid>
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
            filterKey: String,
            heading:Object
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
            gridColumns: ['erp_id', 'firstname', 'lastname', 'email', 'phone_1', 'status', 'created_on'],
            gridData: null,
            colTitles: {'erp_id':'KundenNr.', 'firstname':'Vorname', 'lastname':'Nachname', 'email':'E-Mail', 'phone_1':'Telefon', 'status':'Status', 'created_on':'Hinzugefügt am'}
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


