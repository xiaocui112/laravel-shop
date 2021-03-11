@extends('layouts.app')
@section('title','收货地址列表')
@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card panel-default">
            <div class="card-header">收货地址列表</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td>收货人</td>
                            <td>地址</td>
                            <td>邮编</td>
                            <td>电话</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($addresses as $address)
                        <tr>
                            <td>{{$address->contact_name}}</td>
                            <td>{{$address->full_address}}</td>
                            <td>{{$address->zip}}</td>
                            <td>{{$address->contact_phone}}</td>
                            <td>
                                <button class="btn btn-primary">修改</button>
                                <button class="btn btn-danger">删除</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection