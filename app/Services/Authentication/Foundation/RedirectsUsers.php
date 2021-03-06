<?php

namespace APP\Services\Authentication\Foundation;
/**
 * Class RedirectsUsers
 * @package APP\Services\Authentication\Foundation
 */
trait RedirectsUsers
{
    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
    }
}