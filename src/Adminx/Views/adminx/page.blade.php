@extends($core->get_layout(), ['core' => $core])
@section('adminx_title', $page_title)
@section('adminx_content')
<?php echo $output; ?>
@stop
