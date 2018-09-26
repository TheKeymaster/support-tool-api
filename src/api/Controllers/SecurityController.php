<?php

namespace api\Controllers;

class SecurityController
{
    const INVALID_AUTHKEY = 'Invalid or unknown authkey!';

    const ACCESS_DENIED = 'You do not have permission to request that data!';

    /**
     * Checks whether the requester is allowed to access data or not.
     *
     * @param $requester
     * @param $result
     * @return bool
     */
    public function isAllowedToSeeUserData($requester, $result)
    {
        if ($this->isSupportOrAdmin($result['role']) ||
            $this->isSupportOrAdmin($requester['role']) ||
            $this->equals($requester['email'], $result['email'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks the role and returns true if the role is support or administrator.
     *
     * @param $roleId
     * @return bool
     */
    private function isSupportOrAdmin($roleId)
    {
        return ($roleId > 1);
    }

    /**
     * Checks whether two variables are equal. Note: the type is not being checked! This means string "3" equals
     * to integer 3.
     *
     * @param $value1
     * @param $value2
     * @return bool
     */
    private function equals($value1, $value2)
    {
        return $value1 == $value2;
    }
}
