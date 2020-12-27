@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', $model_config['title'])
@section('adminx_content')

<h2>{{ $model_config['title'] }}</h2>
<hr />

@stop