@extends('layouts.app')

@section('title', 'Dashboard')

@section('style')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default" id="app">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    <div class="form-group col-md-4">
                        <input v-model="filterData" placeholder="Filter by..." type="text" class="form-control" autocomplete="off">
                    </div>
                    <table class="table table-bordered table-hover table-responsive">
                      <thead>
                        <th> Id </th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created Date</th>
                      </thead>
                      <tbody v-for="(index, customer) in customers | filterBy filterData">
                        <tr>
                            <td>@{{ index + 1 }}</td>
                            <td>@{{ customer.firstname }}</td>
                            <td>@{{ customer.lastname }}</td>
                            <td>@{{ customer.email }}</td>
                            <td>@{{ customer.phone }}</td>
                            <td>@{{ customer.created_at }}</td>
                        </tr>
                      </tbody>
                    </table>
                </div>
            </div>
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
        http: {
            root: '/root',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },

        el: '#app',

        data: {
            filterInput: 'yes',
            filterData: '',
            customers: [],
        },

        methods: {
            fetchCustomer: function(){
                this.$http.get('/customer', function(data){
                    this.$set('customers', data);
                });
            }
        },

        ready: function(){
            this.fetchCustomer();
        }
    });
</script>
@endpush


