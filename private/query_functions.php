<?php 

    // SUBJECT
    function shift_subject_positions($start_pos, $end_pos, $current_id=0) {
      global $db;
  
      // $start_pos is the current position.
      // $end_pos is the new position.
      if($start_pos == $end_pos) { return; }
  
      $sql = "UPDATE subjects ";
      if($start_pos == 0) {
        // new item, +1 to items greater than $end_pos
        $sql .= "SET position = position + 1 ";
        $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
      } elseif($end_pos == 0) {
        // delete item, -1 from items greater than $start_pos
        $sql .= "SET position = position - 1 ";
        $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
      } elseif($start_pos < $end_pos) {
        // move later, -1 from items between (including $end_pos)
        $sql .= "SET position = position - 1 ";
        $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
        $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
      } elseif($start_pos > $end_pos) {
        // move earlier, +1 to items between (including $end_pos)
        $sql .= "SET position = position + 1 ";
        $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
        $sql .= "AND position < '" . db_escape($db, $start_pos) . "' ";
      }
      // Exclude the current_id in the SQL WHERE clause
      $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";
  
      $result = mysqli_query($db, $sql);
      // For UPDATE statements, $result is true/false
      if($result) {
        return true;
      } else {
        // UPDATE failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }

    function find_all_subjects($options=[]) {
        global $db;
        $visible = $options['visible'] ?? false;

        $query = "SELECT * FROM subjects ";
        if($visible) {
            $query .= "WHERE visible= true ";
        }
        $query .= "ORDER BY position ASC";
        $result = mysqli_query($db, $query);

        confirm_result_set($result);
        return $result;
    }

    function find_subject_by_id($id, $options=[]) {
        global $db;
        $visible = $options['visible'] ?? false;

        $query = "SELECT * FROM subjects ";
        $query .= "WHERE id='" . db_escape($db, $id) . "' ";
        if($visible) {
            $query .= "AND visible=true";
        }
        $result = mysqli_query($db, $query);
        confirm_result_set($result);
        $subject = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        return $subject;
    }

    function insert_subject($subject) {
        global $db;

        $errors = validate_subject($subject);
        if (!empty($errors)) {
            return $errors;
        }

        shift_subject_positions(0, $subject['position']);

        $query = "INSERT INTO subjects ";
        $query .= "(menu_name, position, visible)";
        $query .= "VALUES (";
        $query .= "'" . db_escape($db, $subject['menu_name']) . "', ";
        $query .= "'" . db_escape($db, $subject['position']) . "', ";
        $query .= "'" . db_escape($db, $subject['visible']) . "')";
        $result = mysqli_query($db, $query);
        // INSERT query returns true or false;
        if ($result) {
            return true;
        } else {
            // INSERT FAILED
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
        
    }

    function validate_subject($subject) {
        $errors = [];
    
        // menu_name
        if(is_blank($subject['menu_name'])) {
          $errors[] = "Name cannot be blank.";
        } elseif(!has_length($subject['menu_name'], ['min' => 2, 'max' => 255])) {
          $errors[] = "Name must be between 2 and 255 characters.";
        }
    
        // position
        // Make sure we are working with an integer
        $postion_int = (int) $subject['position'];
        if($postion_int <= 0) {
          $errors[] = "Position must be greater than zero.";
        }
        if($postion_int > 999) {
          $errors[] = "Position must be less than 999.";
        }
    
        // visible
        // Make sure we are working with a string
        $visible_str = (string) $subject['visible'];
        if(!has_inclusion_of($visible_str, ["0","1"])) {
          $errors[] = "Visible must be true or false.";
        }
    
        return $errors;
    } 

    function validate_page($page) {
        $errors = [];
    
        // subject
        if(is_blank($page['subject_id'])) {
          $errors[] = "Suject cannot be blank.";
        }

        // menu_name
        if(is_blank($page['menu_name'])) {
          $errors[] = "Name cannot be blank.";
        } elseif(!has_length($page['menu_name'], ['min' => 2, 'max' => 255])) {
          $errors[] = "Name must be between 2 and 255 characters.";
        }

        $current_id = $page['id'] ?? '0';
        if (!has_unique_page_menu_name($page['menu_name'], $current_id)) {
            $errors[] = "Menu name must be unique.";
        }
    
        // position
        // Make sure we are working with an integer
        $postion_int = (int) $page['position'];
        if($postion_int <= 0) {
          $errors[] = "Position must be greater than zero.";
        }
        if($postion_int > 999) {
          $errors[] = "Position must be less than 999.";
        }
    
        // visible
        // Make sure we are working with a string
        $visible_str = (string) $page['visible'];
        if(!has_inclusion_of($visible_str, ["0","1"])) {
          $errors[] = "Visible must be true or false.";
        }

        // content
        if(is_blank($page['content'])) {
            $errors[] = "Content cannot be blank.";
          }
    
        return $errors;
    } 


    function update_subject($subject) {
        global $db;

        $errors = validate_subject($subject);
        if (!empty($errors)) {
            return $errors;
        }

        $old_position = find_subject_by_id($subject['id']);
        shift_subject_positions($old_position['position'], $subject['position'], $subject['id']);

        $query = "UPDATE subjects SET ";
        $query .= "menu_name='" . db_escape($db, $subject['menu_name']) . "', ";
        $query .= "position='" . db_escape($db, $subject['position']) . "', ";
        $query .= "visible='" . db_escape($db, $subject['visible']) . "' ";
        $query .= "WHERE id='" . db_escape($db, $subject['id']) . "'";

        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            // UPDATE FAILED
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }

    }

    function delete_subjects($id) {
        global $db;

        $old_position = find_subject_by_id($id);
        shift_subject_positions($old_position['position'], 0, $id);

        $query = "DELETE FROM subjects ";
        $query .= "WHERE id='" . db_escape($db, $id) . "' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($db, $query);
        //DELETE returns true/false
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
        }

    }

    // PAGES
    function shift_page_positions($start_pos, $end_pos, $subject_id, $current_id=0) {
      global $db;
  
      if($start_pos == $end_pos) { return; }
  
      $sql = "UPDATE pages ";
      if($start_pos == 0) {
        // new item, +1 to items greater than $end_pos
        $sql .= "SET position = position + 1 ";
        $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
      } elseif($end_pos == 0) {
        // delete item, -1 from items greater than $start_pos
        $sql .= "SET position = position - 1 ";
        $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
      } elseif($start_pos < $end_pos) {
        // move later, -1 from items between (including $end_pos)
        $sql .= "SET position = position - 1 ";
        $sql .= "WHERE position > '" . db_escape($db, $start_pos) . "' ";
        $sql .= "AND position <= '" . db_escape($db, $end_pos) . "' ";
      } elseif($start_pos > $end_pos) {
        // move earlier, +1 to items between (including $end_pos)
        $sql .= "SET position = position + 1 ";
        $sql .= "WHERE position >= '" . db_escape($db, $end_pos) . "' ";
        $sql .= "AND position < '" . db_escape($db, $start_pos) . "' ";
      }
      // Exclude the current_id in the SQL WHERE clause
      $sql .= "AND id != '" . db_escape($db, $current_id) . "' ";
      $sql .= "AND subject_id = '" . db_escape($db, $subject_id) . "'";
  
      $result = mysqli_query($db, $sql);
      // For UPDATE statements, $result is true/false
      if($result) {
        return true;
      } else {
        // UPDATE failed
        echo mysqli_error($db);
        db_disconnect($db);
        exit;
      }
    }

    function find_all_pages() {
        global $db;
        $query = "SELECT * FROM pages ";
        $query .= "ORDER BY subject_id ASC, position ASC";
        $result = mysqli_query($db, $query);

        confirm_result_set(($result));
        return $result;
    }

    function find_page_by_id($id, $options=[]) {
        global $db;
        $visible = $options['visible'] ?? false;
        $query = "SELECT * FROM pages ";
        $query .= "WHERE id='" . db_escape($db, $id) . "' ";
        if($visible) {
            $query .= "AND visible=true";
        }
        $result = mysqli_query($db, $query);
        confirm_result_set($result);
        $page = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $page;
    }

    function insert_page($page) {
        global $db;

        $errors = validate_page($page);
        if (!empty($errors)) {
            return $errors;
        }

        shift_page_positions(0, $page['position'], $page['subject_id']);

        $query = "INSERT INTO pages ";
        $query .= "(subject_id, menu_name, position, visible, content)";
        $query .= "VALUES (";
        $query .= "'" . db_escape($db, $page['subject_id']) . "', ";
        $query .= "'" . db_escape($db, $page['menu_name']) . "', ";
        $query .= "'" . db_escape($db, $page['position']) . "', ";
        $query .= "'" . db_escape($db, $page['visible']) . "', ";
        $query .= "'" . db_escape($db, $page['content']) . "'";
        $query .= ")";
        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }

    }

    function update_page($page) {
        global $db;

        $errors = validate_page($page);
        if (!empty($errors)) {
            return $errors;
        }

        $current_page = find_page_by_id($page['id']);
        shift_page_positions($current_page['position'], $page['position'], $page['subject_id'], $page['id']);

        $query = "UPDATE pages SET ";
        $query .= "subject_id='" . db_escape($db, $page['subject_id']) . "', ";
        $query .= "menu_name='" . db_escape($db, $page['menu_name']) . "', ";
        $query .= "position='" . db_escape($db, $page['position']) . "', ";
        $query .= "visible='" . db_escape($db, $page['visible']) . "', ";
        $query .= "content='" . db_escape($db, $page['content']) . "' ";
        $query .= "WHERE id='" . db_escape($db, $page['id']) . "' ";
        $query .= "LIMIT 1";

        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }

    }

    function delete_page($id) {
        global $db;


        $page = find_page_by_id($id);
        shift_page_positions($page['position'], 0, $page['subject_id'], $id);

        $query = "DELETE FROM pages ";
        $query .= "WHERE id='" . db_escape($db, $id) . "' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function find_pages_by_subject_id($subject_id, $options=[]) {
        global $db;

        $visible = $options['visible'] ?? false;

        $query = "SELECT * FROM pages ";
        $query .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
        if($visible) {
            $query .= "AND visible=true ";
        }
        $query .= "ORDER BY position ASC";
        $result = mysqli_query($db, $query);
        confirm_result_set($result);
        return $result;
    }

    function count_pages_by_subject_id($subject_id, $options=[]) {
        global $db;

        $visible = $options['visible'] ?? false;

        $query = "SELECT COUNT(id) FROM pages ";
        $query .= "WHERE subject_id='" . db_escape($db, $subject_id) . "' ";
        if($visible) {
            $query .= "AND visible=true ";
        }
        $query .= "ORDER BY position ASC";
        $result = mysqli_query($db, $query);
        confirm_result_set($result);

        $row = mysqli_fetch_row($result);
        $count = $row[0];
        return $count;
    }


    // ADMIN

    function validate_admin($admin, $options=[]) {

      $password_required = $options['password_required'] ?? true;
      

        if(is_blank($admin['first_name'])) {
          $errors[] = "First name cannot be blank.";
        } elseif (!has_length($admin['first_name'], array('min' => 2, 'max' => 255))) {
          $errors[] = "First name must be between 2 and 255 characters.";
        }
    
        if(is_blank($admin['last_name'])) {
          $errors[] = "Last name cannot be blank.";
        } elseif (!has_length($admin['last_name'], array('min' => 2, 'max' => 255))) {
          $errors[] = "Last name must be between 2 and 255 characters.";
        }
    
        if(is_blank($admin['email'])) {
          $errors[] = "Email cannot be blank.";
        } elseif (!has_length($admin['email'], array('max' => 255))) {
          $errors[] = "Email must be less than 255 characters.";
        } elseif (!has_valid_email_format($admin['email'])) {
          $errors[] = "Email must be a valid format.";
        }
    
        if(is_blank($admin['username'])) {
          $errors[] = "Username cannot be blank.";
        } elseif (!has_length($admin['username'], array('min' => 8, 'max' => 255))) {
          $errors[] = "Username must be between 8 and 255 characters.";
        } elseif (!has_unique_username($admin['username'], $admin['id'] ?? 0)) {
          $errors[] = "Username not allowed. Try another.";
        }
    
      if($password_required) {

        if(is_blank($admin['password'])) {
          $errors[] = "Password cannot be blank.";
        } elseif (!has_length($admin['password'], array('min' => 12))) {
          $errors[] = "Password must contain 12 or more characters";
        } elseif (!preg_match('/[A-Z]/', $admin['password'])) {
          $errors[] = "Password must contain at least 1 uppercase letter";
        } elseif (!preg_match('/[a-z]/', $admin['password'])) {
          $errors[] = "Password must contain at least 1 lowercase letter";
        } elseif (!preg_match('/[0-9]/', $admin['password'])) {
          $errors[] = "Password must contain at least 1 number";
        } elseif (!preg_match('/[^A-Za-z0-9\s]/', $admin['password'])) {
          $errors[] = "Password must contain at least 1 symbol";
        }
    
        if(is_blank($admin['confirm_password'])) {
          $errors[] = "Confirm password cannot be blank.";
        } elseif ($admin['password'] !== $admin['confirm_password']) {
          $errors[] = "Password and confirm password must match.";
        }
        
      }
        return $errors;
    }


    function insert_admin($user) {
        global $db;
        
        $errors = validate_admin($user);
        if (!empty($errors)) {
            return $errors;
        }

        $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);

        $query = "INSERT INTO admins ";
        $query .= "(first_name, last_name, email, username, hashed_password) ";
        $query .= "VALUES ( ";
        $query .= "'" . db_escape($db, $user["first_name"]) . "', ";
        $query .= "'" . db_escape($db, $user["last_name"]) . "', ";
        $query .= "'" . db_escape($db, $user["email"]) . "', ";
        $query .= "'" . db_escape($db, $user["username"]) . "', ";
        $query .= "'" . db_escape($db, $hashed_password) . "'";
        $query .= " )";
        
        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function find_admin_by_id($id) {
        global $db;
        
        $query = "SELECT * FROM admins ";
        $query .= "WHERE id='" . db_escape($db, $id) . "'";
        $query .= "ORDER BY id ASC";
        $result = mysqli_query($db, $query);
        confirm_result_set($result);
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        return $user;

    }

    function find_admin_by_username($username) {
        global $db;
        
        $query = "SELECT * FROM admins ";
        $query .= "WHERE username='" . db_escape($db, $username) . "'";
        $query .= "LIMIT 1";
        $result = mysqli_query($db, $query);
        confirm_result_set($result);
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        return $user;

    }

    function find_all_admin_user() {
        global $db;
        $query = "SELECT * FROM admins ";
        $query .= "ORDER BY last_name ASC, first_name ASC";
        $result = mysqli_query($db, $query);
        confirm_result_set(($result));
        return $result;
    }

    function update_admin_user($user) {
        global $db;

        // value = true if password is sent
        $password_sent = !is_blank($user['password']);

        $errors = validate_admin($user, ['password_required' => $password_sent]);
        if (!empty($errors)) {
            return $errors;
        }

        $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);

        $query = "UPDATE admins SET ";
        $query .= "first_name='" . db_escape($db, $user['first_name']) . "', ";
        $query .= "last_name='" . db_escape($db, $user['last_name']) . "', ";
        $query .= "email='" . db_escape($db, $user['email']) . "', ";
        if ($password_sent) {
          $query .= "hashed_password='" . db_escape($db, $hashed_password) . "', ";
        }
        $query .= "username='" . db_escape($db, $user['username']) . "' ";
        $query .= "WHERE id='" . h($user['id']) . "' ";
        $query .= "LIMIT 1";
        
        $result = mysqli_query($db, $query);
        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function delete_admin_user($id) {
        global $db;

        $query = "DELETE FROM admins ";
        $query .= "WHERE id='" . db_escape($db, $id) . "' ";
        $query .= "LIMIT 1";
        $result = mysqli_query($db, $query);

        if ($result) {
            return true;
        } else {
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }
?>