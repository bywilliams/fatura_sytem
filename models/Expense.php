<?php 

    class Expense {

        public $id;
        public $description;
        public $value;
        public $user_id;
        public $dt_registered;
        public $dt_expense;
        public $month_reference;
        public $dt_updated;

    }

    interface ExpenseDAOInterface {
        public function buildExpense ($data);
        public function getAllUserExpense($user_id);
        public function createUserExpense(Expense $expense);
        public function updateUserExpense(Expense $expense);
        public function getReports($sql, $id);
        public function destroyUserExpense($id);
    }


?>