@if($item->canDelete())
    <form onsubmit='return confirm("{{ $core->getWord('adminx.filemanager.delete_btn_confirmation', 'Are you sure you wanna delete this file?') }}")' method='POST' style='float: right; display: inline !important;'>
        @csrf
        <input type='hidden' name='delete_file' value='{{ $item->path }}' />
        <button title='{{ $core->getWord('adminx.filemanager.delete', 'Delete') }}' type='submit' class='btn btn-danger text-light'><i class='fa fa-trash'></i></button>
    </form>
@endif

@if($item->canRead())
    <form method='POST' style='float: right; display: inline !important;'>
        @csrf
        <input type='hidden' name='copy_file' value='{{ $item->path }}' />
        <button title='{{ $core->getWord('adminx.filemanager.copy', 'Copy') }}' type='submit' class='btn btn-success text-light'><i class='fa fa-copy'></i></button>
    </form>

    @if((!$item->isDir()) || $item->canDownloadDirectory())
        <form method='get' style='float: right; display: inline !important;'>
            <input type='hidden' name='download' value='{{ htmlspecialchars($item->path) }}' />
            <button title='{{ $core->getWord('adminx.filemanager.download', 'Download') }}' style='display: inline-block !important;' class='btn btn-dark' type='submit'>
                <i class='fa fa-download'></i>
            </button>
        </form>
    @endif

    @if($item->canDelete())
        <form method='POST' style='float: right; display: inline !important;'>
            @csrf
            <input type='hidden' name='cut_file' value='{{ $item->path }}' />
            <button title='{{ $core->getWord('adminx.filemanager.cut', 'Cut') }}' type='submit' class='btn btn-primary text-light'><i class='fa fa-cut'></i></button>
        </form>

        <form onsubmit='return handleRename("{{ hash('sha256', $item->path) }}")' method='POST'  style='float: right; display: inline !important;'>
            @csrf
            <input type='hidden' name='rename_file' value='{{ $item->path }}' />
            <input id='{{ hash('sha256', $item->path) }}' type='hidden' name='rename_to' value='{{ $item->name() }}' />
            <button title='{{ $core->getWord('adminx.filemanager.rename', 'Rename') }}' type='submit' class='btn btn-secondary text-light'>{{ $core->getWord('adminx.filemanager.rename', 'Rename') }}</button>
        </form>
    @endif
@endif

@if($item->canWrite() && !$item->isDir())
    <form method='get' style='float: right; display: inline !important;'>
        <input type='hidden' name='edit' value='{{ htmlspecialchars($item->path) }}' />
        <button title='{{ $core->getWord('adminx.filemanager.edit', 'Edit') }}' style='display: inline-block !important;' class='btn btn-primary' type='submit'>
            <i class='fa fa-edit'></i>
        </button>
    </form>
@endif

<div style='clear: both;'></div>
