<?php
set_exception_handler(function($exception)
{
  header("HTTP/1.1 500 Internal Server Error");
  
  echo "
  <html>
    <head>
      <title>There's been an error</title>
    </head>
    <body>
      <div>
        <h1>There's been an error</h1>
        <pre>".htmlspecialchars($exception)."</pre>
      </div>
    </body>
  </html>
  ";
  
});

?>