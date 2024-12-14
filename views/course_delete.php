<!DOCTYPE html>
<html lang="en">
<?php require_once 'partials/header.php'; ?>

<body>
            <form class="form" action="#" method="post">
                <h2>Are you sure you want to delete this course?</h2>
                <input disabled type="hidden" name="id" value="<?= $course->course_id ?>">
                <label for="name">Course name</label>
                <input disabled type="text" name="name" value="<?= $course->course_name ?>">
                <label for="code">Course code</label>
                <input disabled type="text" name="code" value="<?= $course->course_id ?>">
                <label for="credits">Credits</label>
                <input disabled type="number" name="credits" value="<?= $course->course_credits ?>">
                <label for="desc">Description</label>
                <input disabled type="text" name="desc" value="<?= $course->course_desc ?>">
                <input type="submit" value="Delete">
            </form>

        <?php

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $course_id = filter_input(INPUT_POST, 'id', FILTER_DEFAULT);

            $success = $course->delete();

            $success ? $_SESSION['success'] = 'Course deleted successfully' : $_SESSION['error'] = 'Failed to delete course';
            require_once 'views/courses.php';
            }
    ?>

    <!-- this has a temporary css (move to a css file) -->
    <style>
        :root {
            --background: #f7f7f7;
            --foreground: #333;
            --shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        form {
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            width: 300px;
            margin: 0 auto;
            padding: 10px;
            background-color: var(--background);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        form input {
            padding: 5px;
            border-radius: 5px;
            background-color: var(--background);
            color: var(--foreground);
            outline: none;
            border: none;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.2);
        }

        input[type="submit"] {
            cursor: pointer;
            background-color: #ff4f4f;
            color: #fff;
            backdrop-filter: blur(25px);
            border-radius: 30px;
            padding: 10px 20px;
            border: none;
            box-shadow:
                0 10px 20px rgba(0, 0, 0, 0.3),
                0 4px 8px rgba(0, 0, 0, 0.2) inset,
                0 -4px 8px rgba(255, 255, 255, 0.2) inset;
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ec0000;
            transform: translateY(-5px);
        }

        input[type="submit"]:active {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            box-shadow:
                0 10px 20px rgba(0, 0, 0, 0.3),
                0 4px 8px rgba(0, 0, 0, 0.2) inset,
                0 -4px 8px rgba(255, 255, 255, 0.2) inset;
        }

        form>* {
            margin: 5px;
        }

        .msg-box {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            font-family: sans-serif;
            font-size: 1.3em;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            padding: 5px;
            opacity: 1;
            animation: slide-up 0.5s forwards, slide-down 0.5s 3.5s forwards;
            overflow: hidden;
            max-height: 30px;
        }

        .msg-box.error {
            background-color: red;
        }

        .msg-box.normal {
            background-color: black;
        }

        .msg-box>* {
            margin: 0;
        }

        .msg-box>h1 {
            font-size: 1.2em;
            margin-bottom: 2px;
        }

        .msg-box>p {
            font-size: 0.8em;
        }

        @keyframes slide-up {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @keyframes slide-down {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-100%);
            }
        }
    </style>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="msg-box error">
            <p><?= $_SESSION['error'] ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="msg-box normal">
            <p><?= $_SESSION['success'] ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>




</body>

</html>