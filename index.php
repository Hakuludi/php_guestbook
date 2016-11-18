<?php error_reporting(E_ALL);
// 'WELCOME TO HELL!!!'
$servername = "localhost";
$username   = "root";
$password   = "12345";

try {
    $conn = new PDO("mysql:host=$servername;dbname=guestbook", $username, $password);
    $conn->exec("set names utf8");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch(PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
    }
?>

<?php if (!empty($_POST['name']) && !empty($_POST['message'])) {
    $stmt = $conn->prepare("INSERT INTO Guests (name, message) VALUES (:name, :message)");
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':message', $_POST['message']);
    $stmt->execute();
}
?>



<?php if (!empty($_POST["keyword"])) {
    $search = 'SELECT * from Guests WHERE name LIKE :keyword OR message LIKE :keyword';
    $fuck   = $conn->prepare($search);
    $fuck->bindValue(':keyword', "%{$_POST['keyword']}%");
    $fuck->execute();
    $array  = $fuck->fetchAll();
    foreach ($array as $message) {
        // print_r($message["id"]);
        $ctime   = $message["ctime"];
        $id      = $message["id"];
        $name    = $message["name"];
        $message = $message["message"];
        $result .= "<div class='name center pcom search-res'><p class='name'>$name (from search):</p><p class='message'>$message</p><form class='delete' action='index.php' method='post'><input class='idel' type='text' name='id' value='$id'><button class='dbutt' type='submit' title='delete this message'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button></form><p class='ctime'>$ctime #$id</p></div><br>";
    }

    if (isset($_GET["jss"])) {
        if (empty($result)) {
            $result .= "<div class='name center pcom search-res'><p class='nothing'>No results could be found.</p></div><br>";
        }
        echo $result;
        die;
    }
} else {
    if (isset($result)) {
        $result = "<div class='name center pcom search-res'><p class='nothing'> nothing </p></div><br>";
        echo $result;
        die;
    }
}
?>

<?php try {

    // Find out how many items are in the table
    $total = $conn->query('
        SELECT
            COUNT(*)
        FROM
            Guests
    ')->fetchColumn();

    // How many items to list per page
    $limit = 5;

    // How many pages will there be
    $pages = ceil($total / $limit);

    // What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    // Calculate the offset for the query
    $offset   = ($page - 1)  * $limit;
    $prevlink = ($page > 1) ? '<a class="pre" href="?page=1" title="First page">&laquo;</a> <a class="pre" href="?page=' . ($page - 1) . '" title="Previous page" class="pre">&lsaquo;</a>' : '<span class="disabled pre">&laquo;</span> <span class="disabled pre">&lsaquo;</span>';
    $nextlink = ($page < $pages) ? '<a  class="pre" href="?page=' . ($page + 1) . '" title="Next page">&rsaquo;</a> <a class="pre" href="?page=' . $pages . '" title="Last page">&raquo;</a>' : '<span class="disabled pre">&rsaquo;</span> <span class="disabled pre">&raquo;</span>';

    // Some information to display to the user
    $start = $offset + 1;
    $end   = min(($offset + $limit), $total);
    $stmt  = $conn->prepare('
        SELECT*
        FROM
            Guests
        ORDER BY
            id DESC
        LIMIT :limit
        OFFSET
            :offset
    ');

    // Bind the query params
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Do we have any results?
    if ($stmt->rowCount() > 0) {
        // Define how we want to fetch the results
        // $stmt->setFetchMode(PDO::FETCH_ASSOC);
        // rsort(array)$iterator = new IteratorIterator($stmt);
        $iterator =$stmt->fetchAll();

        // Display the results
        foreach ($iterator as $row) {
            $ctime     = $row["ctime"];
            $id        = $row["id"];
            $name      = $row["name"];
            $message   = $row["message"];
            $comments .= "<div class='name center pcom'><p class='name'>$name:</p><p class='message'>$message</p><form class='delete' action='index.php' method='post'><input class='idel' type='text' name='id' value='$id'><button class='dbutt' type='submit' title='delete this message'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button></form><p class='ctime'>$ctime #$id</p></div><br>";
        }
        if (isset($_GET["ajax"])) {
                echo $comments;
                echo '<div class="center paginat" id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' messages ', $nextlink, ' </p></div>';
                die;
            }

    } else {
        echo '<p>No results could be displayed.</p>';
    }

} catch (Exception $e) {
    echo '<p>', $e->getMessage(), '</p>';
}
?>

<?php if (isset($_POST["id"])) {
    $delete = 'DELETE from Guests WHERE id=:id';
    $stmt   = $conn->prepare($delete);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title id="pagename"></title>
    <link rel='shortcut icon' href='/favicon.ico' type='image/x-icon'/>
</head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="/js/jquery-3.1.1.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Tangerine">

<header>
    <h1 link="black" class="font-effect-shadow-multiple hat" title="go back to start"><a class="headu" href="/">The Guest Book</a></h1>
</header>
<div>
<d class="glyphicon-remove"></d>
</div>
<link rel="stylesheet" href="/css/style.css">

<script src="/js/site.js"></script>

<div class="comments">
    <?= $comments;
    echo '<div class="center paginat" id="paging"><p>', $prevlink, ' Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' messages ', $nextlink, ' </p></div>';
    ?>
</div>
<br>
<div class="center">
    <form class="post" action="index.php" method="post">
        <div>
            <input type="text" name="name" placeholder="Guest name" title="we just need your name for nothing" required ><br>
            <input type="text" name="message" placeholder="Message" autocomplete="off" title="What you think right now about this ..." required ><br>
            <div>
                <button type="submit" class="btn btn-danger" title="just post your message">Post message</button>
            </div>
        </div>
        <br>
    </form>
    <div class="bull">
        <hr>
    </div>
    <form class="navbar-form navbar-centre search" action="index.php" method="post">
        <div>
            <input type="text" class="form-control" name="keyword" placeholder="Search" title="enter your keyword"><br>
            <div>
                <button type="submit" class="btn btn-default" title="find what you need">Search</button>
            </div>
        </div>
    </form>
</div>

</html>
