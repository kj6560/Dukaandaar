<!DOCTYPE html>
<html>
<head>
    <title>Payment gateway using Paytm</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" width="500px">
    <div class="panel panel-primary" style="margin-top:110px;">
        <div class="panel-heading"><h3 class="text-center">Proceed To Buy</h3></div>
        <div class="panel-body">
            
            <form action="{{ route('make.payment') }}" method="POST" enctype="multipart/form-data">
                {!! csrf_field() !!}

                @if($message = Session::get('message'))
                        <p>{!! $message !!}</p>
                    <?php Session::forget('success'); ?>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <strong>Feature Id:</strong>
                        <input type="text" name="product_id" value="{{ $product_id }}" class="form-control" placeholder="Product_id" readonly>
                    </div>
                    <div class="col-md-12">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" placeholder="Name" readonly>
                    </div>
                    <div class="col-md-12">
                        <strong>Mobile No:</strong>
                        <input type="text" name="mobile" value="{{ $user->number }}" class="form-control" maxlength="10" placeholder="Mobile No." readonly>
                    </div>
                    <div class="col-md-12">
                        <strong>Email:</strong>
                        <input type="email" class="form-control" value="{{ $user->email }}" placeholder="Email" name="email" readonly>
                    </div>
                    <div class="col-md-12" >
                        <br/>
                        <div class="btn btn-info">
                            charges : {{$feature->price??0}} Rs/-
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br/>
                        <button type="submit" class="btn btn-success">Proceed</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>