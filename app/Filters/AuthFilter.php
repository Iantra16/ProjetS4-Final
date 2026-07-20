<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        if ($arguments && is_array($arguments) && count($arguments) > 0) {
            $requiredRole = $arguments[0];
            if (session()->get('role') !== $requiredRole) {
                return redirect()->to('/unauthorized');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}