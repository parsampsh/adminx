<h3>{{ $core->getWord('adminx.filemanager.main_title', 'File Manager') }}</h3>

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
    var value = prompt('{{ $core->getWord('adminx.filemanager.are_you_sure', 'Are you sure?') }}', input.value)
    if (value === null) {
            return false;
    }

    input.value = value

    if (input.value === valueBackup) {
        // name is not changed
        alert('{{ $core->getWord('adminx.filemanager.change_current_name', 'You must change current name.') }}')
        return false
    }

    return true
}
</script>

@if(isset($currentLocNotFound) && $currentLocNotFound === true)
    <div class='alert alert-danger'>
        {{ $core->getWord('adminx.filemanager.not_found', 'Fire or directory not found?') }} <a href='?currentLoc=/'>Root</a>
    </div>
@else
    @if(isset($fileContent))
        <div>
            <a href='?currentLoc={{ $parentDir }}'>{{ $core->getWord('adminx.filemanager.back', 'Back') }}</a>
        </div>
        <div class="badge badge-primary">{{ $core->getWord('adminx.filemanager.file', 'File') }}: {{ $currentLoc }}</div>

        @include('adminx.builtin_plugins.filemanager.buttons', ['item' => $fileItem])

        <hr />
        @if(isset($editor) && $editor)
            @include('adminx.builtin_plugins.filemanager.editor', ['fileContent' => $fileContent, 'btnTitle' => $core->getWord('adminx.filemanager.edit', 'Edit')])
        @else
            <pre>{{ $fileContent }}</pre>
        @endif
    @else
        <div class="badge badge-primary">{{ $core->getWord('adminx.filemanager.current_location', 'Current Location') }}: {{ $currentLoc }}</div>

        <div>
            @if(session()->has('adminx_filemanager_clipboard') && $currentLocObj->canWrite() && $currentLoc !== '/')
                <form style='display: inline' method='POST'>
                    @csrf
                    <button value='1' title='{{ $core->getWord('adminx.filemanager.paste', 'Paste') }}' name='paste_file' class='btn btn-primary' type='submit'>
                        <i class='fa fa-paste'></i> {{ $core->getWord('adminx.filemanager.paste', 'Paste') }}
                    </button>
                </form>
            @endif

            @if($currentLocObj->canWrite() && $currentLoc !== '/')
                <form style='display: inline' method='POST' enctype="multipart/form-data">
                    @csrf
                    <input type='file' name='upload_file' required class='btn btn-secondary' />
                    <button title='{{ $core->getWord('adminx.filemanager.upload', 'Upload') }}' class='btn btn-secondary' type='submit'>
                        <i class='fa fa-upload'></i> {{ $core->getWord('adminx.filemanager.upload', 'Upload') }}
                    </button>
                </form>

                <form style='display: inline' method='POST'>
                    @csrf
                    <input type='text' name='create_file' placeholder="{{ $core->getWord('adminx.filemanager.create_new_file', 'Create a new file') }}" />
                    <button title='{{ $core->getWord('adminx.filemanager.new_file', 'New file') }}' class='btn btn-secondary' type='submit'>
                        <i class='fa fa-plus'></i> {{ $core->getWord('adminx.filemanager.new_file', 'New file') }}
                    </button>
                </form>
            @endif
        </div>

        @if($parentDir !== null)
            <div>
                <a class='filemanager-item' href='?currentLoc={{ $parentDir }}'>..</a>
            </div>
        @endif

        @foreach($items as $item)
            <div>
                <a class='filemanager-item' <?= $item->canRead() ? "href='?currentLoc=" . htmlspecialchars($item->path) . "'" : '' ?>>
                    <?= $item->isDir() ? '<i class="fa fa-folder"></i>' : '' ?> {{ $item->name() }} <?= $item->canRead() ? '' : '<i class="fa fa-lock" title="' . $core->getWord('adminx.filemanager.cant_read_file', 'You cannot read content of this file') . '"></i>' ?>
                    @include('adminx.builtin_plugins.filemanager.buttons', ['item' => $item])
                </a>
            </div>
        @endforeach
    @endif
@endif
