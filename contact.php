<?php
$fields = array(
    'full_name' => 'Name',
    'email' => 'Email address',
    'favorite_planet' => 'Favorite planet',
    'experience' => 'Next topic',
    'message' => 'Message'
);
$values = array();
$errors = array();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    $errors[] = 'Please submit the feedback form to continue.';
} else {
    foreach ($fields as $key => $label) {
        $value = isset($_POST[$key]) && is_string($_POST[$key]) ? trim($_POST[$key]) : '';
        $values[$key] = $value;
        if ($value === '') {
            $errors[] = $label . ' is required.';
        }
    }

    $planets = array('Mercury', 'Venus', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune');
    $topics = array('Planetary moons', 'Space missions', 'Planet facts', 'More images');

    if (isset($values['email']) && $values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if (isset($values['favorite_planet']) && !in_array($values['favorite_planet'], $planets, true)) {
        $errors[] = 'Choose a planet from the list.';
    }
    if (isset($values['experience']) && !in_array($values['experience'], $topics, true)) {
        $errors[] = 'Choose a topic from the list.';
    }
    if (isset($values['full_name']) && strlen($values['full_name']) > 80) {
        $errors[] = 'Your name must be 80 characters or fewer.';
    }
    if (isset($values['email']) && strlen($values['email']) > 254) {
        $errors[] = 'Your email address must be 254 characters or fewer.';
    }
    if (isset($values['message']) && strlen($values['message']) > 1000) {
        $errors[] = 'Your message must be 1,000 characters or fewer.';
    }
    if (!isset($_POST['consent']) || $_POST['consent'] !== 'yes') {
        $errors[] = 'Please confirm that you agree to send this feedback.';
    }
}

function escape_output($value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($errors) {
    http_response_code($_SERVER['REQUEST_METHOD'] === 'POST' ? 400 : 405);
} else {
    http_response_code(200);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback | SeeSolar</title>
    <link rel="stylesheet" href="css/master.css">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="index.html"><img src="images/sun.png" alt=""><span>SeeSolar</span></a>
            <nav class="site-nav" aria-label="Main navigation"><ul><li><a href="index.html">Home</a></li><li><a class="nav-cta" href="contact.html">Share your favorite planet!</a></li></ul></nav>
        </div>
    </header>
    <main class="page-shell">
        <section class="content-section">
            <?php if ($errors): ?>
                <p class="eyebrow">Please check your form</p>
                <h1>We couldn't send that yet.</h1>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape_output($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a class="button-link" href="contact.html">Return to the form</a>
            <?php else: ?>
                <p class="eyebrow">Message received</p>
                <h1>Thanks, <?= escape_output($values['full_name']) ?>!</h1>
                <p>Your favorite planet is <?= escape_output($values['favorite_planet']) ?>, and you want to explore <?= escape_output($values['experience']) ?> next.</p>
                <h2>Your message</h2>
                <p><?= nl2br(escape_output($values['message'])) ?></p>
                <p class="form-note">This sample site displays feedback but does not store it or send email.</p>
                <a class="button-link" href="index.html">Return to the solar system</a>
            <?php endif; ?>
        </section>
    </main>
    <footer class="site-footer"><p>SeeSolar · Mission control</p></footer>
</body>
</html>
