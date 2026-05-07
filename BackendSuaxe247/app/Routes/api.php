    <?php

    use CodeIgniter\Router\RouteCollection;

    /**
     * @var RouteCollection $routes
     */

    $routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
        $routes->resource('category', [
            'controller' => 'CSparePartCategory',
            'placeholder' => '(:segment)'
        ]);
        
        $routes->resource('spare-part-inventory', [
            'controller' => 'CSparePartInventory',
            'placeholder' => '(:segment)'
        ]);
        
        $routes->resource('spare-part', [
            'controller' => 'CSparePart',
            'placeholder' => '(:segment)'
        ]);

        $routes->get('spare-part/by-store/(:segment)', 'CSparePart::getByStore/$1');

        $routes->resource('supplier', [
            'controller' => 'CSupplier',
            'placeholder' => '(:segment)' 
        ]);

        $routes->resource('import-receipt', [
            'controller' => 'CImportReceipt',
            'placeholder' => '(:segment)' 
        ]);

        // === staff ===
        // $routes->delete('api/import-receipt/(:segment)', 'CImportReceipt::delete/$1'); 
        $routes->get('user/staff/technician', 'CStaff::getTechnicians');
        $routes->get('user/staff/countTechnicians/(:segment)', 'CStaff::countTechniciansByStore/$1');
        $routes->resource('user/staff', [
        'controller' => 'CStaff',
        'placeholder' => '(:segment)'
        ]);

        // === customer ===
        $routes->get('user/customer/by-phone/(:segment)', 'CCustomer::getByPhone/$1');

        // === user ====
        $routes->post('user/login', 'CCustomer::login');
        $routes->post('user/logout', 'CCustomer::logout');
        $routes->post('user/register', 'CCustomer::register');
        $routes->get('user/session', 'CCustomer::checkSession');
        $routes->get('user/customer', 'CCustomer::listCustomers');
        $routes->get('user/customer/(:num)', 'CCustomer::getCustomerDetail/$1');
        $routes->put('user/resetCustomerPassword/(:num)', 'CCustomer::resetCustomerPassword/$1');
        $routes->get('user/current', 'CCustomer::getCurrentUser');
        $routes->put('user/updateProfile', 'CCustomer::updateProfile');        
        $routes->post('user/changePassword', 'CCustomer::changePassword'); 


        $routes->get('roles', 'CRole::index');       // Lấy tất cả role
        $routes->get('roles/staff', 'CRole::staffRoles'); // Lấy role nhân viên (trừ Giám đốc, Khách hàng)

        //Route xe
        // Lấy tất cả xe của người dùng (không phân trang)
        $routes->get('userVehicles/all/(:num)', 'CVehicle::allUserVehicles/$1');


        $routes->get('userVehicles/(:num)', 'CVehicle::userVehicles/$1'); 
        $routes->resource('vehicle', [
        'controller'=>'CVehicle',
        'placeholder' => '(:segment)'
        ]);

        // Route cho Invoice (ID là int)
        $routes->resource('invoice', [
            'controller' => 'CInvoice',
            'placeholder' => '(:segment)'
        ]);
        $routes->get('invoices/user', 'CInvoice::userInvoices');
        $routes->get('invoices/user/(:segment)', 'CInvoice::userInvoiceDetail/$1');

        // Route cho Store
        $routes->resource('store', [
            'controller' => 'CStore',
            'placeholder' => '(:segment)'
        ]);
        $routes->get('store', 'CStore::index');
        $routes->get('store2', 'CStore::index2');
        $routes->get('store/(:segment)', 'CStore::show/$1');
        $routes->post('store', 'CStore::create');
        $routes->post('store/(:segment)', 'CStore::update/$1'); // UPDATE DÙNG POST
        $routes->delete('store/(:segment)', 'CStore::delete/$1');



        $routes->get('service', 'CService::index');
        $routes->get('service/(:segment)', 'CService::show/$1');
        $routes->post('service', 'CService::create');
        $routes->post('service/(:segment)', 'CService::update/$1'); // UPDATE DÙNG POST
        $routes->delete('service/(:segment)', 'CService::delete/$1');
        $routes->get('service-all', 'CService::getAllWithDeleted');
        $routes->post('service/(:segment)/restore', 'CService::restore/$1');

        $routes->get('appointments', 'CAppointment::index');                     // Lấy tất cả lịch hẹn
        $routes->get('appointments/customer/(:segment)', 'CAppointment::getByCustomer/$1');  // Lấy lịch hẹn theo customer
        $routes->get('appointments/store/(:segment)', 'CAppointment::getByStore/$1');        // Lấy lịch hẹn theo store
        $routes->get('appointments/search', 'CAppointment::search');                         // Tìm kiếm lịch hẹn
        $routes->put('appointments/status/(:segment)', 'CAppointment::updateStatus/$1');     // Cập nhật trạng thái
        $routes->post('appointments', 'CAppointment::create');                               // Tạo lịch hẹn
        $routes->delete('appointments/(:segment)', 'CAppointment::delete/$1');
        $routes->get('appointments/(:segment)', 'CAppointment::show/$1'); // Lấy chi tiết lịch hẹn theo ID
        $routes->post('appointments/(:segment)', 'CAppointment::update/$1'); // Cập nhật lịch hẹn theo ID (dùng POST thay vì PUT)
        $routes->get('appointments/confirmed/(:segment)', 'CAppointment::confirmedAppointmentsByStore/$1');
        $routes->get('appointments/count-by-store-time/(:segment)', 'CAppointment::countByStoreAndTime/$1');



        $routes->get('users', 'CUser::index');         // Lấy danh sách user
        $routes->get('users/(:num)', 'CUser::show/$1'); // Lấy chi tiết user theo ID
        $routes->post('users', 'CUser::create');        // Thêm user mới
        $routes->post('users/(:num)', 'CUser::update/$1'); // Cập nhật user
        $routes->delete('users/(:num)', 'CUser::delete/$1'); // Xóa mềm user

        $routes->resource('store-spare-part', [
            'controller' => 'CStoreSparePart',
            'placeholder' => '(:segment)'
        ]);

        // Các route mở rộng (ngoài chuẩn CRUD)
        $routes->get('store-spare-part/store/(:segment)', 'CStoreSparePart::getByStore/$1');
        $routes->get('store-spare-part/low-stock/(:segment)', 'CStoreSparePart::getLowStock/$1');
        $routes->post('store-spare-part/spare-part-insert', 'CStoreSparePart::sparePartsInsert');
        $routes->put('store-spare-part/delete-by-store/(:any)', 'CStoreSparePart::softDeleteByStore/$1');


        $routes->group('appointment-service', function($routes) {
            $routes->get('all', 'CAppointmentService::getAllAppointmentServices');
            $routes->get('(:num)', 'CAppointmentService::getServicesByAppointment/$1');
            $routes->post('add', 'CAppointmentService::addServicesToAppointment');
            $routes->delete('remove', 'CAppointmentService::removeServiceFromAppointment');
            $routes->delete('remove-all/(:num)', 'CAppointmentService::removeAllServicesFromAppointment/$1');
            $routes->put('update', 'CAppointmentService::updateServicesForAppointment');
        });

        $routes->get('warehouse_report', 'CReport::warehouseReport');          // Lấy báo cáo xuất nhập kho
        // $routes->get('inventory/export', 'CReport::exportInventory'); // (Tùy chọn) Xuất file Excel
        $routes->get('store_report', 'CReport::storeReport');

        $routes->post('email/send', 'CEmail::sendEmailNotification');
        $routes->get('email/send-confirmation/(:num)', 'CEmail::sendAppointmentConfirmation/$1');
        $routes->get('email/send-status-update/(:num)', 'CEmail::sendStatusUpdate/$1');
        $routes->get('email/send-reminder/(:num)', 'CEmail::sendAppointmentReminder/$1');
        $routes->get('email/send-cancellation/(:num)', 'CEmail::sendCancellationNotification/$1');
        $routes->get('email/test/(:num)', 'CEmail::testEmail/$1');
        $routes->get('email/check-reminders', 'CEmail::checkUpcomingAppointments');
        $routes->get('cron/send-reminders', 'CEmail::runReminderCron');

            
            $routes->get('cron/send-reminders', 'CCron::sendReminders');
    });