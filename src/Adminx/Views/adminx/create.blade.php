@extends($core->get_layout(), ['core' => $core])
@if($is_update)
    @section('adminx_title', $core->get_word('btn.update', 'Update') . ' ' . $row->id)
@else
    @section('adminx_title', str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')))
@endif
@section('adminx_content')
    <a class="btn btn-primary" href="{{ request()->get('back', $core->url('/model/' . $model_config['slug'])) }}">{{ $core->get_word('btn.back', 'Back') }}</a>
    <br />
    <br />
    @if($is_update)
        <h2>{{ $core->get_word('btn.update', 'Update') }} "{{ $row->id }}"</h2>
        <?php $is_superuser = $core->check_super_user(auth()->user()); ?>
        @if($is_superuser || \Adminx\Access::user_has_permission(auth()->user(), $model_config['slug'] . '.delete'))
            @if($is_superuser || call_user_func_array($model_config['delete_middleware'], [auth()->user(), $row]))
                <br />
                <form action="{{ request()->get('back', $core->url('/model/' . $model_config['slug'])) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $core->get_word('delete.msg', 'Are you sure to delete this item?') }}')">
                    <input type="hidden" name="_method" value="DELETE" />
                    @csrf
                    <input type="hidden" name="delete" value="{{ $row->id }}" />
                    <button class="btn btn-danger" type="submit"><i class="fa fa-trash"></i></button>
                </form>
            @endif
        @endif
        <a class="btn btn-primary" href="{{ $core->url('/model/AdminxLog?filter_model=' . $model_config['slug'] . '&filter_item='. $row->id .'&back=' . request()->fullUrl()) }}">{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.log', 'History')) }} <i class="fa fa-history"></i></a>
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
                <select class="select2-box form-control" name="{{ $column['name'] }}">
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


        <?php $i = 0; ?>
        @foreach($model_config['n2n'] as $item)
        <?php
            $list = $item['list']();
            $current_selected = [];

            if ($is_update) {
                $current_selected = $item['pivot']::where($item['pivot_keys'][0], $row->id)->get();
                $current_selected_ids = [];
                foreach ($current_selected as $cs) {
                    array_push($current_selected_ids, intval($cs->{$item['pivot_keys'][1]}));
                }

                $current_selected = $current_selected_ids;
            }

            $i++;
        ?>
            {{ $item['name'] }}:
            <select multiple="multiple" class="select2-box form-control" name="n2n{{ $i }}[]">
                @foreach($list as $r)
                    <option{!! in_array($r->id, $current_selected) ? ' selected="selected"' : '' !!} value="{{ $r->id }}">{{ $item['title']($r) }}</option>
                @endforeach
            </select>
            <br />
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