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

        @if($parentDir !== null)
            <div>
                <a class='filemanager-item' href='?currentLoc={{ $parentDir }}'>..</a>
            </div>
        @endif

        @foreach($items as $item)
            <div>
                <a class='filemanager-item' href='?currentLoc={{ $item }}'><?= is_dir($item) ? '<i class="fa fa-folder"></i>' : '' ?> {{ pathinfo($item)['basename'] }}</a>
            </div>
        @endforeach
    @endif
@endif
