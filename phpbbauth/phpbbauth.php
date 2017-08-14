<?php
/*
Plugin Name: phpbbauth
Plugin URI: http://wordpress.org/#
Description: Authenticate in Wordpress using passwords from a phpBB installation
Author: Olivier Croquette ocroquette@free.fr
Version: 1.0

You still need to create users in the Wordpress administration panel, but
the users may use their phpBB password to login.

Wordpress passwords are still valid to log in.

It's important that the Wordpress logins match the <code>username_clean</code>
of the Phpbb users. username_clean doesn't contain any uppercase 
characters, nor special ones. Check directly in the DB if in doubt.

LICENSE :

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if (!class_exists('PasswordHashPhpBb'))
{
    require_once "PasswordHashPhpBb.php";
}

function phpbbauth_check_password($check, $password, $hash, $user_id) {

    $config_phpbb_db_host   = "localhost";
    $config_phpbb_db_user   = "user";
    $config_phpbb_db_passwd = "pass";
    $config_phpbb_db_db     = "DB";
    $config_phpbb_db_prefix = "phpbb3_";

    if ( $check ) {
        // Password already validated by the legacy Wordpress authentication
        // Just pass
        return true;
    }

    // We have to get back the login to fetch the data from PHPBB's table
    $userdata = get_userdata($user_id);
    $username = $userdata->user_login;

    $sqlConnection =  mysql_connect($config_phpbb_db_host, $config_phpbb_db_user, $config_phpbb_db_passwd, true);

    if (!$sqlConnection)
    {
        echo('phpbbauth: could not connect to the PHPBB database.<br>');
        return false;
    }

    // Select Database
    $selected = mysql_select_db($config_phpbb_db_db, $sqlConnection);

    // Check if we were able to select the database.
    if (!$selected)
    {
        echo("phpbbauth: could not use db $config_phpbb_db_db.<br>");
        return false;
    }

    mysql_query("SET NAMES 'utf8'", $sqlConnection); // This is so utf8 usernames work. Needed for MySQL 4.1

    $username = utf8_encode($username);

    // Check Database for username and password.
    $query = sprintf("SELECT `user_id`, `username_clean`, `user_password`
                        FROM `%s`
                        WHERE `username_clean` = '%s'
                        LIMIT 1",
                        $config_phpbb_db_prefix."_users",
                        mysql_real_escape_string($username, $sqlConnection));

    // Query Database.
    $results = mysql_query($query, $sqlConnection);
 
    if ( ! $results ) {
        echo("phpbbauth: query failed<br>");
    }

    while( $result = mysql_fetch_assoc($results) )
    {
        $hasher = new PasswordHashPhpBb(8, TRUE);
        if ( $hasher->CheckPassword($password, $result['user_password']) )
        {
            // Password accepted
            return true;
        }
    }

    return false;
}

add_filter("check_password" , phpbbauth_check_password, 1, 4);

?>
