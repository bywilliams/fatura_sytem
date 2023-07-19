<?php $todayReminders = $reminderDao->getTodayReminders($userData->id); ?>

<?php foreach($todayReminders as $reminder): ?>
    <?php if ($reminder->reminder_date == date("d-m-Y") && $reminder->visualized != "S") : ?>
        <script>
            var msg = "<strong><?= $reminder->title ?></strong><br><br><?= $reminder->description ?> <br>";
            var form = "<form action='reminders_process.php' method='post'>" +
                "<input type='hidden' name='type' value='edit'>" +
                "<input type='hidden' name='id' value='<?= $reminder->id ?>'>" +
                "<input type='hidden' name='title' value='<?= $reminder->title ?>'>" +
                "<input type='hidden' name='description' value='<?= $reminder->description ?>'>" +
                "<input type='hidden' name='reminder_date' value='<?= date("Y-m-d", strtotime($reminder->reminder_date)); ?>'>" +
                "<input type='hidden' name='visualized' value='S'>" +
                "<input class='btn btn-lg btn-success' type='submit' value='Ok'>" +
                "</form>";
            Swal.fire({
                html: msg,
                title: 'Você tem um lembrete hoje!',
                footer: form,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>
<?php endforeach ?>