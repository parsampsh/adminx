<form method='POST' class='form-group'>
    @csrf
    <textarea class='form-control' name='file_content'>{{ $fileContent }}</textarea>
    <br />
    <input type='submit' style='width: 100%' class='btn btn-primary' value='{{ $btnTitle }}' />
</form>
