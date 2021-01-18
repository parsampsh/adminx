@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')))
@section('adminx_content')
    <a class="btn btn-primary" href="{{ request()->get('back', $core->url('/model/' . $model_config['slug'])) }}">{{ $core->get_word('btn.back', 'Back') }}</a>
    <h2>{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }}</h2>
    <hr />

    <form action="POSt" class="form-group">
        @csrf
        <input type="hidden" name="_method" value="PUT" />

        @foreach($columns as $column)
            <?php
            $type = $column['type'];
            $is_textarea = false;
            switch ($type) {
                case 'string':
                    $type = 'text';
                    break;
                case 'datetime':
                    $type = 'date';
                    break;
                case 'integer':
                    $type = 'number';
                    break;
                case 'text':
                    $is_textarea = true;
                    break;
            }
            ?>
            @if(isset($model_config['fields_titles'][$column['name']]))
                {{ $model_config['fields_titles'][$column['name']] }}:
            @else
                {{ $column['name'] }}:
            @endif
            @if(isset($model_config['foreign_keys'][$column['name']]))
                <?php $list = $model_config['foreign_keys'][$column['name']]['list'](); ?>
                <select name="{{ $column['name'] }}">
                    @foreach($list as $item)
                        <option value="{{ $item->id }}">{{ $model_config['foreign_keys'][$column['name']]['title']($item) }}</option>
                    @endforeach
                </select>
                <br />
            @else
                @if(!$is_textarea)
                    <input placeholder="{{ $column['comment'] ? $column['comment'] : $column['name'] }}" value="{{ $column['default'] }}" maxlength="{{ $type === 'text' ? $column['max'] : '' }}" {{ $column['is_null'] === false ? 'required' : '' }} type="{{ $type }}" name="{{ $column['name'] }}" class="form-control" />
                @else
                    <textarea placeholder="{{ $column['comment'] ? $column['comment'] : $column['name'] }}" name="{{ $column['name'] }}" class="form-control" {{ $column['is_null'] === false ? 'required' : '' }}>{{ $column['default'] }}</textarea>
                @endif
            @endif
            <br />
        @endforeach

        <input class="btn btn-success" style="width: 100%; padding: 20px;" type="submit" value="{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }}" />
    </form>

@stop