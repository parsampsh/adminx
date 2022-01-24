<h3>File Manager</h3>

<style>
.filemanager-item {
    padding: 5px;
    margin: 2px;
    border-radius: 5px;
    border: 1px solid #ababab;
    display: block;
    width: 100%;
}

.filemanager-item:hover {
    background-color: #9a9a9a;
}
</style>

@if(isset($currentLocNotFound) && $currentLocNotFound === true)
    <div class='alert alert-danger'>
        File or directory not found. <a href='?currentLoc=/'>Root</a>
    </div>
@else
    @if(isset($fileContent))
        <div>
            <a href='?currentLoc={{ $parentDir }}'>Back</a>
        </div>
        <div class="badge badge-primary">File: {{ $currentLoc }}</div>
        <hr />
        <pre>{{ $fileContent }}</pre>
    @else
        <div class="badge badge-primary">Current Location: {{ $currentLoc }}</div>

        @if(session()->has('adminx_filemanager_clipboard') && $currentLocObj->canWrite() && $currentLoc !== '/')
            <form method='POST'>
                @csrf
                <button value='1' title='Paste' name='paste_file' class='btn btn-primary' type='submit'>
                    <i class='fa fa-paste'></i> Paste
                </button>
            </form>
        @endif

        @if($parentDir !== null)
            <div>
                <a class='filemanager-item' href='?currentLoc={{ $parentDir }}'>..</a>
            </div>
        @endif

        @foreach($items as $item)
            <div>
                <a class='filemanager-item' <?= $item->canRead() ? "href='?currentLoc=" . htmlspecialchars($item->path) . "'" : '' ?>>
                    <?= $item->isDir() ? '<i class="fa fa-folder"></i>' : '' ?> {{ $item->name() }} <?= $item->canRead() ? '' : '<i class="fa fa-lock" title="You cannot read the content of the file"></i>' ?>
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

                        @if($item->canDelete())
                            <form method='POST' style='float: right; display: inline !important;'>
                                @csrf
                                <input type='hidden' name='cut_file' value='{{ $item->path }}' />
                                <button title='Cut' type='submit' class='btn btn-primary text-light'><i class='fa fa-cut'></i></button>
                            </form>
                        @endif
                    @endif

                    <div style='clear: both;'></div>

                </a>
            </div>
        @endforeach
    @endif
@endif
