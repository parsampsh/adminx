@extends($core->get_layout(), ['core' => $core])
@if($is_update)
    @section('adminx_title', $core->get_word('btn.update', 'Update') . ' ' . $row->id)
@else
    @section('adminx_title', str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')))
@endif
@section('adminx_content')
    <a class="btn btn-primary" href="{{ request()->get('back', $core->url('/model/' . $model_config['slug'])) }}">{{ $core->get_word('btn.back', 'Back') }}</a>
    @if($is_update)
        <h2>{{ $core->get_word('btn.update', 'Update') }} "{{ $row->id }}"</h2>
    @else
        <h2>{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }}</h2>
    @endif
    <hr />

    <form method="POST" class="form-group">
        @csrf

        @if($is_update)
            <input type="hidden" name="_method" value="PUT" />
        @endif

        @foreach($columns as $column)
            <?php
            $type = $column['type'];
            $is_textarea = false;
            switch ($type) {
                case 'string':
                    $type = 'text';
                    break;
                case 'datetime':
                    $type = 'datetime';
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
                <?php
                    $list = $model_config['foreign_keys'][$column['name']]['list']();
                    $current_selected = null;

                    if ($is_update) {
                        $current_selected = intval($row->{$column['name']});
                    }
                ?>
                <select class="select2-box" name="{{ $column['name'] }}">
                    @foreach($list as $item)
                        <option{{ $current_selected === $item->id ? ' selected' : '' }} value="{{ $item->id }}">{{ $model_config['foreign_keys'][$column['name']]['title']($item) }}</option>
                    @endforeach
                </select>
                <br />
            @else
                <?php
                    $default = $column['default'];
                    if ($is_update) {
                        $default = $row->{$column['name']};
                    }
                ?>
                @if(!$is_textarea)
                    <input placeholder="{{ $column['comment'] ? $column['comment'] : $column['name'] }}" value="{{ $default }}" maxlength="{{ $type === 'text' ? $column['max'] : '' }}" {{ $column['is_null'] === false ? 'required' : '' }} type="{{ $type }}" name="{{ $column['name'] }}" class="form-control" />
                @else
                    <textarea placeholder="{{ $column['comment'] ? $column['comment'] : $column['name'] }}" name="{{ $column['name'] }}" class="form-control" {{ $column['is_null'] === false ? 'required' : '' }}>{{ $default }}</textarea>
                @endif
            @endif
            <br />
        @endforeach


        <?php
        if ($is_update) {
            echo call_user_func_array($model_config['update_html'], [$row]);
        } else {
            echo call_user_func($model_config['create_html']);
        }
        ?>

        @if($is_update)
            <input class="btn btn-success" style="width: 100%; padding: 20px;" type="submit" value="{{ $core->get_word('btn.update', 'Update') }}" />
        @else
        <input class="btn btn-success" style="width: 100%; padding: 20px;" type="submit" value="{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }}" />
        @endif
    </form>

@stop