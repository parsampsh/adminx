@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')))
@section('adminx_content')
    <a class="btn btn-primary" href="{{ request()->get('back', $core->url('/model/' . $model_config['slug'])) }}">{{ $core->get_word('btn.back', 'Back') }}</a>
    <h2>{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }}</h2>
    <hr />
@stop