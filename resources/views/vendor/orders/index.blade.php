@extends('vendor.layout.main')

@section('content')
@if(session()->get('success'))
<div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<div class="alert-icon">
			<i class="fa fa-check"></i>
		</div>
		<div class="alert-message">
			<span><strong>Success!</strong> {{ session()->get('success') }}</span>
		</div>
</div>
@endif
@if(session()->get('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert">×</button>
		<div class="alert-icon">
			<i class="fa fa-check"></i>
		</div>
		<div class="alert-message">
			<span><strong>Failed!</strong> {{ session()->get('success') }}</span>
		</div>
</div>
@endif

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header"><div class="left"><span>Orders</span></div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="example" class="table table-bordered">
					<thead style="background: black">
						<tr>
                            <th>#</th>
                            <th style="width: 15%">Order No</th>
                            <th style="width: 25%">Customer Name</th>
                            <th>Total Price</th>
                            <th>Is Paid</th>
                            <th>Status</th>
                            {{-- <th>Products</th> --}}
                            <th>Actions</th>
                            <th>Details</th>
						</tr>
					</thead>
					<tbody>
                        @foreach ($op as $item)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$item->order_id}}</td>
                            <td>{{$item->user_name}}</td>
                            <td>{{$item->vendor_total_price}}</td>
                            <td>{{$item->is_paid}}</td>
                            <td>{{$item->status}}</td>
                            {{-- <td>{{$item->title}}</td> --}}
                            {{-- <td><a href="{{route('supplier.supplier_shippo',['user_id'=>$item->user_id,'product_sku'=>$item->sku,'supplier_id'=>$item->supplier_id])}}" class="btn btn-info">Ship</a></td> --}}
                            <td><a href="#" class="btn btn-info">Ship</a></td>
                            <td><a href="{{route('vendor.orders.view_details', $item->order_id)}}" class="btn btn-info">Details</a></td>
                        </tr>
                        @endforeach

					</tbody>

				</table>
			</div>
		</div>
	</div>
</div>
{{--
<script>
$(document).ready(function() {
	var table = $('#example').DataTable( {
		lengthChange: false,
			buttons: [
			{
				extend: 'copy',
				title: 'Order List',
				exportOptions: {
				columns: [ 0, 1,2,3,4,5,6,7]
				}
			},
			{
				extend: 'excelHtml5',
				title: 'Order List',
				exportOptions: {
				columns: [ 0, 1, 2,3,4,5,6,7]
				}
			},
			{
				extend: 'pdfHtml5',
				title: 'Order List',
				exportOptions: {
				columns: [ 0, 1, 2,3,4,5,6,7]
				}
			},
			{
				extend: 'print',
				title: 'Order List',
				autoPrint: true,
				exportOptions: {
				columns: [ 0, 1, 2,3,4,5,6,7]
				}
			},
			'colvis'
		],
		columnDefs: [
			{ "orderable": false, "targets": 8 }
		]
	});
	table.buttons().container()
	.appendTo( '#example_wrapper .col-md-6:eq(0)' );
} );
function deleteRow(id)
{
	$('#deletefrm_'+id).submit();
}
</script> --}}
@endsection

