<!DOCTYPE html>
<html lang="en">
<?php require_once 'partials/header.php'; ?>

<body>
    <header>
        <h1>Free Electives</h1>
    </header>
    <main>
        <!-- Dynamic table of terms -->
        <?php foreach ($terms as $term) { ?>
            <table class="tblCourses">
                <thead>
                    <tr>
                        <th scope="col">Term</th>
                        <th scope="col">Details</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td><?= $term->term_id ?></td>
                            <td><span aria-describedby="<?= $term->term_id ?>_desc"><?= $term->term_desc ?></span></td>
                            <?php if ($user->role == 'admin') { ?>
                                <td><a href="terms/<?= $term->term_id ?>">Edit</a></td>
                                <td><a href="terms/<?= $term->term_id ?>">Update</a></td>
                                <td><a href="terms/<?= $term->term_id ?>">Delete</a></td>
                            <?php } ?>
                        </tr>
                </tbody>
            </table>
        <?php } ?>


    </main>
    <?php require_once 'partials/footer.php'; ?>
</body>

</html>