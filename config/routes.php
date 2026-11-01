<?php

/** @var \App\Core\Router $router */
$router = $this->router();

// Public
$router->get('/', 'HomeController@index');
$router->get('/privacy', 'HomeController@privacy');

// Auth
$router->get('/login', 'AuthController@showLogin', ['guest']);
$router->post('/login', 'AuthController@login', ['guest', 'csrf']);
$router->post('/logout', 'AuthController@logout', ['auth', 'csrf']);

// API (auth + csrf via header/body)
$router->get('/api/face/settings', 'Api\\FaceApiController@settings', ['auth']);
$router->get('/api/face/descriptors', 'Api\\FaceApiController@descriptors', ['auth']);
$router->post('/api/face/register', 'Api\\FaceApiController@register', ['auth', 'csrf']);
$router->post('/api/face/attendance', 'Api\\FaceApiController@attendance', ['auth', 'csrf']);

// Admin
$router->get('/admin/dashboard', 'Admin\\DashboardController@index', ['admin']);

$router->get('/admin/users', 'Admin\\UserController@index', ['admin']);

$router->get('/admin/students', 'Admin\\StudentController@index', ['admin']);
$router->get('/admin/students/create', 'Admin\\StudentController@create', ['admin']);
$router->post('/admin/students', 'Admin\\StudentController@store', ['admin', 'csrf']);
$router->get('/admin/students/export', 'Admin\\StudentController@export', ['admin']);
$router->get('/admin/students/{id}', 'Admin\\StudentController@show', ['admin']);
$router->get('/admin/students/{id}/edit', 'Admin\\StudentController@edit', ['admin']);
$router->post('/admin/students/{id}', 'Admin\\StudentController@update', ['admin', 'csrf', 'method:PUT']);
$router->post('/admin/students/{id}/delete', 'Admin\\StudentController@destroy', ['admin', 'csrf', 'method:DELETE']);
$router->get('/admin/students/{id}/face', 'Admin\\FaceController@register', ['admin']);

$router->get('/admin/teachers', 'Admin\\TeacherController@index', ['admin']);
$router->get('/admin/teachers/create', 'Admin\\TeacherController@create', ['admin']);
$router->post('/admin/teachers', 'Admin\\TeacherController@store', ['admin', 'csrf']);
$router->get('/admin/teachers/{id}/edit', 'Admin\\TeacherController@edit', ['admin']);
$router->post('/admin/teachers/{id}', 'Admin\\TeacherController@update', ['admin', 'csrf', 'method:PUT']);
$router->post('/admin/teachers/{id}/delete', 'Admin\\TeacherController@destroy', ['admin', 'csrf', 'method:DELETE']);

$router->get('/admin/classes', 'Admin\\ClassController@index', ['admin']);
$router->get('/admin/classes/create', 'Admin\\ClassController@create', ['admin']);
$router->post('/admin/classes', 'Admin\\ClassController@store', ['admin', 'csrf']);
$router->get('/admin/classes/{id}/edit', 'Admin\\ClassController@edit', ['admin']);
$router->post('/admin/classes/{id}', 'Admin\\ClassController@update', ['admin', 'csrf', 'method:PUT']);
$router->post('/admin/classes/{id}/delete', 'Admin\\ClassController@destroy', ['admin', 'csrf', 'method:DELETE']);

$router->get('/admin/subjects', 'Admin\\SubjectController@index', ['admin']);
$router->get('/admin/subjects/create', 'Admin\\SubjectController@create', ['admin']);
$router->post('/admin/subjects', 'Admin\\SubjectController@store', ['admin', 'csrf']);
$router->get('/admin/subjects/{id}/edit', 'Admin\\SubjectController@edit', ['admin']);
$router->post('/admin/subjects/{id}', 'Admin\\SubjectController@update', ['admin', 'csrf', 'method:PUT']);
$router->post('/admin/subjects/{id}/delete', 'Admin\\SubjectController@destroy', ['admin', 'csrf', 'method:DELETE']);

$router->get('/admin/assignments/class-subjects', 'Admin\\AssignmentController@classSubjects', ['admin']);
$router->post('/admin/assignments/class-subjects', 'Admin\\AssignmentController@storeClassSubject', ['admin', 'csrf']);
$router->post('/admin/assignments/class-subjects/{id}/delete', 'Admin\\AssignmentController@destroyClassSubject', ['admin', 'csrf', 'method:DELETE']);

$router->get('/admin/assignments/teachers', 'Admin\\AssignmentController@teacherAssignments', ['admin']);
$router->post('/admin/assignments/teachers', 'Admin\\AssignmentController@storeTeacherAssignment', ['admin', 'csrf']);
$router->post('/admin/assignments/teachers/{id}/delete', 'Admin\\AssignmentController@destroyTeacherAssignment', ['admin', 'csrf', 'method:DELETE']);

$router->get('/admin/attendance', 'Admin\\AttendanceController@index', ['admin']);
$router->get('/admin/attendance/export', 'Admin\\AttendanceController@export', ['admin']);

$router->get('/admin/leave', 'Admin\\LeaveController@index', ['admin']);
$router->get('/admin/leave/{id}', 'Admin\\LeaveController@show', ['admin']);
$router->post('/admin/leave/{id}/review', 'Admin\\LeaveController@review', ['admin', 'csrf']);

$router->get('/admin/settings', 'Admin\\SettingsController@index', ['admin']);
$router->post('/admin/settings', 'Admin\\SettingsController@update', ['admin', 'csrf']);

$router->get('/admin/audit', 'Admin\\AuditController@index', ['admin']);
$router->get('/admin/kiosk', 'Admin\\KioskController@index', ['admin']);

// Teacher
$router->get('/teacher/dashboard', 'Teacher\\DashboardController@index', ['teacher']);
$router->get('/teacher/classes', 'Teacher\\AttendanceController@classes', ['teacher']);
$router->get('/teacher/attendance', 'Teacher\\AttendanceController@index', ['teacher']);
$router->get('/teacher/students/{id}/attendance', 'Teacher\\AttendanceController@student', ['teacher']);

// Student
$router->get('/student/dashboard', 'Student\\DashboardController@index', ['student']);
$router->get('/student/profile', 'Student\\ProfileController@index', ['student']);
$router->get('/student/password', 'Student\\ProfileController@passwordForm', ['student']);
$router->post('/student/password', 'Student\\ProfileController@updatePassword', ['student', 'csrf']);
$router->get('/student/face/register', 'Student\\FaceController@register', ['student']);
$router->get('/student/attendance/scan', 'Student\\AttendanceController@scan', ['student']);
$router->get('/student/attendance/history', 'Student\\AttendanceController@history', ['student']);
$router->get('/student/attendance/statistics', 'Student\\AttendanceController@statistics', ['student']);
$router->get('/student/leave', 'Student\\LeaveController@index', ['student']);
$router->post('/student/leave', 'Student\\LeaveController@store', ['student', 'csrf']);
