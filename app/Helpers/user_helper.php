<?php
if (!function_exists('generate_username_by_role')) {
    /**
     * Generate a unique username based on the last user with a specific role_id.
     *
     * @param int $role_id The role ID to filter users.
     * @return string
     */
    function generate_username_by_role(int $role_id)
    {
        
        if ($role_id !== 2) {
            throw new \InvalidArgumentException('This function only supports role_id = 2.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');     
        $builder->select('username')->where('role_id', $role_id)->orderBy('id', 'DESC')->limit(1);
        $query = $builder->get();
        $lastUser = $query->getRow();

        $base = 'owner@';
        $newNumber = 1;

        if ($lastUser && isset($lastUser->username)) {
            $lastUsername = $lastUser->username;
            if (strpos($lastUsername, $base) === 0) {
                $parts = explode('@', $lastUsername);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $newNumber = (int)$parts[1] + 1;
                }
            }
        }

        
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        return $base . $formattedNumber;
    }
}

