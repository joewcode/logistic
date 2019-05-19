@extends('layouts.app')

@section('stylesheet')
@endsection

@section('javascript')
@endsection

@section('content')

	<div class="row">
		<div class="col-lg-12">
			<div class="wrapper wrapper-content animated fadeInRight">
				<div class="ibox-content m-b-sm border-bottom">
					<div class="text-center p-lg"> <h2>Инструкция по использованию ПО</h2> </div>
				</div>
				
				@foreach ( $database as $base )
				<div class="faq-item">
					<div class="row"> <div class="col-md-7"> <a data-toggle="collapse" href="#faq{{$base[0]}}" class="faq-question">{{$base[1]}}</a> </div> </div>
					<div class="row">
						<div class="col-lg-12"> <div id="faq{{$base[0]}}" class="panel-collapse collapse">
							<div class="faq-answer">{!!$base[2]!!}</div>
						</div> </div>
					</div>
				</div>
				@endforeach
				
			</div>
		</div>
	</div>
	
@endsection