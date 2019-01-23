<?php
/**
 * Created by PhpStorm.
 * User: sarfaoui
 * Date: 07/01/2019
 * Time: 08:35
 */

namespace App\Service;

class PasswordManager
{
    /**
     * @param int $length
     * @return string
     */
    private function generatePassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function checkPassword()
    {
        $checked = false;

        do {
            $password = $this->generatePassword();
            if (preg_match('/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[\d])\S*$/', $password)) {
                $checked = true;
            }

        } while ($checked == false);

        return $password;
    }

}