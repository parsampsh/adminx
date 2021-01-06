<?php
$columns = $core->get_model_columns($model_config);
?>
@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', $model_config['title'])
@section('adminx_content')

<h2>{{ $model_config['title'] }}</h2>
<hr />

<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ $model_config['title'] }}</h6>
    </div>
    <div class="card-body">
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
          </tr></tfoot>
          @endif
          <tbody>
            @foreach($rows as $row)
              <tr>
                @foreach ($columns as $column)
                  @if(isset($model_config['fields_values'][$column]) && is_callable($model_config['fields_values'][$column]))
                    <td><?php echo $model_config['fields_values'][$column]($row) ?></td>
                  @else
                    <td>{{ $row->{$column} }}</td>
                  @endif
                @endforeach
                @foreach ($model_config['virtual_fields'] as $vf => $value)
                    <td><?php echo $value($row) ?></td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($model_config['only_top_pagination'] === false)
      <br />
      <?php echo $rows->render(); ?>
      @endif
    </div>
</div>

@stop