@if($item->canDelete())
    <form onsubmit='return confirm("Are you sure you wanna delete this file?")' method='POST' style='float: right; display: inline !important;'>
        @csrf
        <input type='hidden' name='delete_file' value='{{ $item->path }}' />
        <button title='Delete' type='submit' class='btn btn-danger text-light'><i class='fa fa-trash'></i></button>
    </form>
@endif

@if($item->canRead())
    <form method='POST' style='float: right; display: inline !important;'>
        @csrf
        <input type='hidden' name='copy_file' value='{{ $item->path }}' />
        <button title='Copy' type='submit' class='btn btn-success text-light'><i class='fa fa-copy'></i></button>
    </form>

    @if(!$item->isDir())
        <form method='get' style='float: right; display: inline !important;'>
            <input type='hidden' name='download' value='{{ htmlspecialchars($item->path) }}' />
            <button style='display: inline-block !important;' class='btn btn-dark' type='submit'>
                <i class='fa fa-download'></i>
            </button>
        </form>
    @endif

    @if($item->canDelete())
        <form method='POST' style='float: right; display: inline !important;'>
            @csrf
            <input type='hidden' name='cut_file' value='{{ $item->path }}' />
            <button title='Cut' type='submit' class='btn btn-primary text-light'><i class='fa fa-cut'></i></button>
        </form>
    @endif
@endif

<div style='clear: both;'></div>
