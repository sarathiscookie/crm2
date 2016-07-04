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
            <h1 class="page-header">{{ trans('messages.customerListPageHeadingLabel') }}</h1>
            <div id="app">
                <div class="form-group col-md-4">
                    <form id="search" class="form-inline">
                        <label for="query">{{ trans('messages.customerListPageSearchBox') }} </label>
                        <input name="query" class="form-control" v-model="searchQuery">
                    </form>
                </div>
                <br>
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th v-for="key in columns" @click="sortBy(key)" :class="{active: sortKey == key}">@{{ colTitles[key] }} <span class="arrow" :class="order > 0 ? 'asc' : 'dsc'"></span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(index, item) in items | filterBy searchQuery | orderBy sortKey order">
                        <td>@{{ item.erp_id }}</td>
                        <td>@{{item.firstname}}</td>
                        <td><a href="{{ url('/customer/details/') }}/@{{ item.id }}">@{{item.lastname}}</a></td>
                        <td>@{{item.email}}</td>
                        <td>@{{item.phone_1}}</td>
                        <td>@{{item.status}}</td>
                        <td>@{{item.created_on}}</td>
                    </tr>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination">
                        <li v-if="pagination.current_page > 1">
                            <a href="#" aria-label="Previous"
                               @click.prevent="changePage(pagination.current_page - 1)">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li v-for="page in pagesNumber"
                            v-bind:class="[ page == isActived ? 'active' : '']">
                            <a href="#"
                               @click.prevent="changePage(page)">@{{ page }}</a>
                        </li>
                        <li v-if="pagination.current_page < pagination.last_page">
                            <a href="#" aria-label="Next"
                               @click.prevent="changePage(pagination.current_page + 1)">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('script')
<!-- Vue Js -->
<script src="/assets/js/vue.js"></script>
<script src="/assets/js/vue-resource.js"></script>
<script>

    new Vue({
        el: '#app',

        data: {
            searchQuery: '',

            sortKey: '',

            order: 1,

            columns: ['erp_id', 'firstname', 'lastname', 'email', 'phone_1', 'status', 'created_on'],

            colTitles: {'erp_id':'@lang('messages.customerListPageTableCustomerNo')', 'firstname':'@lang('messages.customerListPageTableFirstname')', 'lastname':'@lang('messages.customerListPageTableLastname')', 'email':'E-Mail', 'phone_1':'@lang('messages.customerListPageTablePhone')', 'status':'Status', 'created_on':'@lang('messages.customerListPageTableAddedDate')'},

            pagination: {
                total: 0,
                per_page: 7,
                from: 1,
                to: 0,
                current_page: 1
            },

            offset: 4,// left and right padding from the pagination <span>,just change it to see effects

            items: []
        },

        ready: function () {
            this.fetchItems(this.pagination.current_page);
        },

        computed: {
            isActived: function () {
                return this.pagination.current_page;
            },
            pagesNumber: function () {
                if (!this.pagination.to) {
                    return [];
                }
                var from = this.pagination.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }
                var pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }

                return pagesArray;
            }
        },

        methods: {
            fetchItems: function (page) {
                var data = {page: page};
                this.$http.get('/list/customers', data).then(function (response) {
                    //look into the routes file and format your response
                    this.$set('items', response.data.data.data);
                    this.$set('pagination', response.data.pagination);
                }, function (error) {
                    // handle error
                });
            },
            changePage: function (page) {
                this.pagination.current_page = page;
                this.fetchItems(page);
            },
            sortBy: function (key) {
                this.sortKey = key;
                this.order   = this.order * -1;
                //alert(this.order = this.order * -1);
                //alert(this.sortKey = key);
                //this.sortOrders[key] = this.sortOrders[key] * -1
                //order = order * -1
            }
        }

    });
</script>
@endpush


