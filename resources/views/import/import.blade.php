<html>
    <head>
        <title></title>
    </head>
    <body>
        <form action="/testing" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file">
            <input type="submit" name="submit">
        </form>
    </body>
</html>