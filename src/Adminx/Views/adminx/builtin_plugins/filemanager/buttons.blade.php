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

    @if((!$item->isDir()) || $item->canDownloadDirectory())
        <form method='get' style='float: right; display: inline !important;'>
            <input type='hidden' name='download' value='{{ htmlspecialchars($item->path) }}' />
            <button title='download' style='display: inline-block !important;' class='btn btn-dark' type='submit'>
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

        <form onsubmit='return handleRename("{{ hash('sha256', $item->path) }}")' method='POST'  style='float: right; display: inline !important;'>
            @csrf
            <input type='hidden' name='rename_file' value='{{ $item->path }}' />
            <input id='{{ hash('sha256', $item->path) }}' type='hidden' name='rename_to' value='{{ $item->name() }}' />
            <button title='Rename' type='submit' class='btn btn-secondary text-light'>Rename</button>
        </form>
    @endif
@endif

<div style='clear: both;'></div>
