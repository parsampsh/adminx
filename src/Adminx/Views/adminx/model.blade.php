<?php
$columns = $core->get_model_columns($model_config);
$is_superuser = $core->check_super_user(auth()->user());
?>
@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', $model_config['title'])
@section('adminx_content')

<h2>{{ $model_config['title'] }}</h2>
<hr />

@if($core->check_super_user(auth()->user()) || (\Adminx\Access::user_has_permission(auth()->user(), $model_config['slug'] . '.create') && call_user_func_array($model_config['create_middleware'], [auth()->user()]) === true))
<a class="btn btn-success" href="{{ $core->url('/model/' . $model_config['slug'] . '/create?back=' . request()->fullUrl()) }}">{{ str_replace('{name}', $model_config['slug'], $core->get_word('btn.create', 'Create new {name}')) }} <i class="fa fa-plus"></i></a>
<br />
<br />
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ $model_config['title'] }}</h6>
    </div>
    <div class="card-body">
      @if(is_callable($model_config['search']))
        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
          <div class="input-group">
            <input value="{{ request()->get('search') }}" name="search" type="text" class="form-control bg-light border-0 small" placeholder="{{ $model_config['search_hint'] }}" aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-search fa-sm"></i>
              </button>
            </div>
          </div>
        </form>
        <br />
      @endif
      <?php echo call_user_func_array($model_config['custom_html'], []); ?>
      @if($model_config['only_bottom_pagination'] === false)
      <?php echo $rows->render(); ?>
      @endif
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ isset($model_config['fields_titles'][$column]) ? $model_config['fields_titles'][$column] : $column }}</th>
                @endforeach
                @foreach ($model_config['virtual_fields'] as $k => $v)
                    <th>{{ $k }}</th>
                @endforeach
                <th>{{ $core->get_word('tbl.action', 'Actions') }}</th>
            </tr>
          </thead>
          @if(!$model_config['no_table_footer'])
          <tfoot><tr>
                @foreach ($columns as $column)
                    <th>{{ isset($model_config['fields_titles'][$column]) ? $model_config['fields_titles'][$column] : $column }}</th>
                @endforeach
                @foreach ($model_config['virtual_fields'] as $k => $v)
                    <th>{{ $k }}</th>
                @endforeach
                <th>{{ $core->get_word('tbl.action', 'Actions') }}</th>
          </tr></tfoot>
          @endif
          <tbody>
            @foreach($rows as $row)
              <tr>
                @foreach ($columns as $column)
                  @if(isset($model_config['foreign_keys'][$column]))
                      <?php $foreign_row = $model_config['foreign_keys'][$column]['model']::find($row->{$column}); ?>
                      <td>{{ $model_config['foreign_keys'][$column]['title']($foreign_row) }}</td>
                  @else
                    @if(isset($model_config['fields_values'][$column]) && is_callable($model_config['fields_values'][$column]))
                      <td><?php echo $model_config['fields_values'][$column]($row) ?></td>
                    @else
                      <td>{{ $row->{$column} }}</td>
                    @endif
                  @endif
                @endforeach
                @foreach ($model_config['virtual_fields'] as $vf => $value)
                    <td><?php echo $value($row) ?></td>
                @endforeach
                @if($is_superuser || \Adminx\Access::user_has_permission(auth()->user(), $model_config['slug'] . '.delete'))
                @if($is_superuser || call_user_func_array($model_config['delete_middleware'], [auth()->user(), $row]))
                <td>
                  <form method="POST" onsubmit="return confirm('{{ $core->get_word('delete.msg', 'Are you sure to delete this item?') }}')">
                    <input type="hidden" name="_method" value="DELETE" />
                    @csrf
                    <input type="hidden" name="delete" value="{{ $row->id }}" />
                    <button class="btn btn-danger" type="submit">{{ $core->get_word('btn.delete', 'Delete') }}</button>
                  </form>
                </td>
                @endif
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($model_config['only_top_pagination'] === false)
      <br />
      <?php echo $rows->render(); ?>
      @endif
      <?php echo call_user_func_array($model_config['custom_html_bottom'], []); ?>
    </div>
</div>

@stop