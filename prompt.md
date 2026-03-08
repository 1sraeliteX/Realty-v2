Prompt

Act as a senior backend engineer and debug the following database error in my PHP MVC real estate management system.

Error:

SQLSTATE[42S02]: Base table or view not found: 1146 Table 'real_estate_db.payments' doesn't exist

Stack trace shows the issue originates from:

app/controllers/SuperAdminController.php

Specifically inside:

getPlatformStats()

which executes a query similar to:

SELECT SUM(amount) FROM payments

Project details:

PHP MVC architecture

MySQL database

PDO for database access

Running on XAMPP

Database name: real_estate_db

Tasks to perform:

Trace the query source

Inspect SuperAdminController.php around line 75 where the payments table is queried.

Identify all queries referencing payments.

Verify database schema

Check whether the payments table actually exists in real_estate_db.

If it does not exist, determine whether:

The table name is incorrect (e.g., tenant_payments, rent_payments, transactions, invoices).

The table migration or SQL file was never executed.

If the table is missing

Create a proper SQL schema for a payments table compatible with a real estate system.

Include fields such as:

id
tenant_id
property_id
unit_id
amount
payment_method
payment_status
reference
paid_at
created_at
updated_at

Provide the full SQL:

CREATE TABLE payments (...)

If another table already stores payments

Update the query in getPlatformStats() to use the correct table.

Ensure the query safely handles empty results:

Example:

$stmt = $this->db->prepare("SELECT COALESCE(SUM(amount),0) as total_revenue FROM payments");
$stmt->execute();
$totalRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];

Add defensive checks

Prevent dashboard crashes if the table is missing.

Implement try/catch around the query and log errors instead of breaking the dashboard.

Verify related dependencies

Check if other controllers, models, or services reference the payments table.

Ensure consistency across the system.

Return

The corrected getPlatformStats() function

The SQL schema if the table must be created

Any other files that must be updated

Goal:
Ensure the Super Admin dashboard loads correctly and retrieves platform revenue statistics without database errors.