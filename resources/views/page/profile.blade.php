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
					<div class="text-center p-lg"> <h2>Мой аккаунт</h2> </div>
                    <div class="row">
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-11">
                            <form class="m-t" role="form" method="POST" action="{{ route('profile') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="font-normal">Текущий пароль</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="password" id="password" class="form-control" value="">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="font-normal">Новый пароль</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="npassword" id="npassword" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-normal">Новый пароль еще раз</label>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-superpowers"></i></span><input type="text" name="npassword_confirmation" id="npassword_confirmation" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Изменить пароль
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	
@endsection