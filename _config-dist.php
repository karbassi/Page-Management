<?php
/** MySQL settings - You can get this info from your web host **/
/** The name of the database */
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Page Password **/
/** There is no point for a secure password at this point of the project
    because this is only a test project. Ultimately, the password would
    be in the database, encrypted.
**/
define('PASSWORD_SEED', '');

/** Contains the encrypted password
    SHA1(MD5('PASSWORD') + PASSWORD_SEED);
**/
define('PROJECT_PASSWORD', '');

/** You're done :) **/

require_once "lib/ezsql/ez_sql_mysql.php";
$db = new ezSQL_mysql(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);