<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        $user = $session->get('user');

        // Nếu chưa đăng nhập
        if (!$user) {
            return service('response')->setJSON([
                'status' => 'error',
                'message' => 'Bạn chưa đăng nhập.'
            ])->setStatusCode(401);
        }

        // Nếu có yêu cầu kiểm tra role cụ thể
        if ($arguments && !in_array($user['roleName'], $arguments)) {
            return service('response')->setJSON([
                'status' => 'error',
                'message' => 'Bạn không có quyền truy cập chức năng này.'
            ])->setStatusCode(403);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // không cần xử lý sau
    }
}
