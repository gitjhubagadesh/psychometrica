<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {

    protected $table = 'psy_admin_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email', 'role_level', 'name'];

    // Fetch user by username
    public function getUserByUsername($username) {
        return $this->where('username', $username)->first();
    }

    public function authenticate($username, $password) {
        $user = $this->getUserByUsername($username);
        if (!$user) {
            log_message('error', 'User not found: ' . $username);
            return null;
        }

        log_message('debug', 'Stored Hashed Password: ' . $user['password']);
        log_message('debug', 'Entered Password: ' . $password);

        if (password_verify($password, $user['password'])) {
            log_message('debug', 'Password verification successful!');
            return $user;
        } else {
            log_message('error', 'Password verification failed for user: ' . $username);
            return null;
        }
    }

    public function checkUsersLogin($user_id, $password) {
        $builder = $this->db->table('psy_users');
        $builder->select('
            psy_users.*, 
            psy_companies.company_name, 
            psy_companies.website, 
            psy_companies.logo_image_path,
            psy_user_registration.id AS register_id,
            psy_user_registration.first_name,
            psy_user_registration.last_name
        ');
        $builder->join('psy_companies', 'psy_companies.id = psy_users.company_id', 'left');
        $builder->join('psy_user_registration', 'psy_user_registration.user_id = psy_users.id', 'left'); // 👈 make sure this join condition is correct
        $builder->where('psy_users.user_id', $user_id);
        $builder->where('psy_users.password', $password);
        $builder->where('CURDATE() BETWEEN validity_from AND validity_to', null, false);

        $query = $builder->get();
        $user = $query->getRowArray();

        if ($user) {
            // ✅ Check if current date is within the validity range
            $builderDateCheck = $this->db->table('psy_users');
            $builderDateCheck->select('*');
            $builderDateCheck->where('user_id', $user_id);
            $builderDateCheck->where('password', $password);
            $builderDateCheck->where('CURDATE() BETWEEN validity_from AND validity_to', null, false);

            $queryDate = $builderDateCheck->get();
            $validUser = $queryDate->getRowArray();

            if ($validUser) {
                $user['status'] = 'valid'; // ✅ User login is valid
            } else {
                $user['status'] = 'expired'; // ❌ Valid credentials, but date is out of range
            }

            return $user;
        }

        return null; // ❌ User not found
    }
}
