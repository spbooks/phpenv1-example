<?php

use Particle\Validator\Validator;

require_once '../vendor/autoload.php';

$database = new medoo([
    'database_type' => 'sqlite',
    'database_file' => is_writable('../database.local.db') ? '../database.local.db' : 'database.db'
]);

$comment = new SitePoint\Comment($database);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = new Validator();
    $v->required('name')->lengthBetween(1, 100)->alnum(true);
    $v->required('email')->email()->lengthBetween(5, 255);
    $v->required('comment')->lengthBetween(10, null);

    $result = $v->validate($_POST);

    if ($result->isValid()) {

        try {
            $comment
                ->setName($_POST['name'])
                ->setEmail($_POST['email'])
                ->setComment($_POST['comment'])
                ->save();

            header('Location: /');

            return;

        } catch (\Exception $e) {
            die($e->getMessage());
        }

    } else {
        dump($result->getMessages());
    }
}

?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <!-- Place favicon.ico in the root directory -->

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an
    <strong>outdated</strong> browser. Please <a
        href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
</p>
<![endif]-->


<?php foreach ($comment->findAll() as $comment) : ?>

    <div class="comment">
        <h3>On <?= $comment->getSubmissionDate() ?>, <?= $comment->getName() ?> wrote:</h3>

        <p><?= $comment->getComment(); ?></p>
    </div>

<?php endforeach; ?>

<form method="post">
    <label>Name: <input type="text" name="name" placeholder="Your name"></label>
    <label>Email: <input type="text" name="email" placeholder="your@email.com"></label>
    <label>Comment: <textarea name="comment" cols="30"
                              rows="10"></textarea></label>
    <input type="submit" value="Save">
</form>
<script
    src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
<script src="js/plugins.js"></script>
<script src="js/main.js"></script>

<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<script>
    (function (b, o, i, l, e, r) {
        b.GoogleAnalyticsObject = l;
        b[l] || (b[l] =
            function () {
                (b[l].q = b[l].q || []).push(arguments)
            });
        b[l].l = +new Date;
        e = o.createElement(i);
        r = o.getElementsByTagName(i)[0];
        e.src = 'https://www.google-analytics.com/analytics.js';
        r.parentNode.insertBefore(e, r)
    }(window, document, 'script', 'ga'));
    ga('create', 'UA-XXXXX-X', 'auto');
    ga('send', 'pageview');
</script>
</body>
</html>
