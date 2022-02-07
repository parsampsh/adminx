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

<script>
function handleRename(inputId) {
    var input = document.getElementById(inputId)
    var valueBackup = input.value
    var value = prompt('are you sure?', input.value)
    if (value === null) {
            return false;
    }

    input.value = value

    if (input.value === valueBackup) {
        // name is not changed
        alert('You must change the current name')
        return false
    }

    return true
}
</script>

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

        @include('adminx.builtin_plugins.filemanager.buttons', ['item' => $fileItem])

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
                    @include('adminx.builtin_plugins.filemanager.buttons', ['item' => $item])
                </a>
            </div>
        @endforeach
    @endif
@endif
