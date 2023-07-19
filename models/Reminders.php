<?php 

    class Reminders {

        public $id;
        public $title;
        public $description;
        public $reminder_date;
        public $visualized;
        public $users_id;
        public $created;
        public $modified;

    }

    interface RemindersDAOInterface {

        public function buildReminder($data); // build object reminder
        public function getAllReminders($data); // get all reminder objects from database
        public function getLatestReminders($data); // get latest 4 reminders objects from database
        public function getTodayReminders($data); // get today reminder objects from database
        public function createReminder(Reminders $reminder); // create object reminder
        public function updateReminder(Reminders $reminder); // update object reminder
        public function destroyReminder($id); // delete object reminder

    }


?>